<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class OrderEvent {

	/**
	 * Triggered on order save action
	 *
	 * @param $event
	 * @param array $args
	 */
	public static function saved($event, $args = array()) {
		
		$app = App::getInstance('zoo');

		$order = $event->getSubject();

		$is_new = $event['is_new'];
		$old = $event['old'];

		$old_state = $old->state;

		if ($event['old']->id) {
			// Calculate the payment method fee
			if ($event['old']->payment_method != $order->payment_method ) {
				JPluginHelper::importPlugin('payment', $order->payment_method);
				$dispatcher = JDispatcher::getInstance();
				$result = array_sum($dispatcher->trigger('getPaymentFee'));
				$order->payment = $result;
			}

			// Calculate totals if somthing has changed
			if ($event['old']->getTotal(true) != $order->getTotal(true)) {
				$app->zoocart->table->orders->save($order);
			}
		}

		// Deal with quantities
		$config = $app->zoocart->getConfig();
		$update_state = $config->get('quantity_update_state');
		
		// Completed order -> remove quantity
		if (($order->state == $update_state ) && ($old_state != $order->state ) && $config->get('update_quantities', false)) {
			foreach($order->getItems() as $orderitem) {
				$item = $app->table->item->get($orderitem->item_id);
				$app->zoocart->quantity->alterQuantity($item, $orderitem->quantity, false);
			}
		}

		// Replenish on reverting order state
		if (($order->state == $config->get('canceled_orderstate')) && ($old_state != $order->state) && $config->get('update_quantities', false)) {
			foreach($order->getItems() as $orderitem) {
				$item = $app->table->item->get($orderitem->item_id);
				$app->zoocart->quantity->alterQuantity($item, $orderitem->quantity, true);
			}
		}

        // Dealing with subscriptions:
        if(in_array($order->state, array(
                                        $config->get('payment_received_orderstate'),
                                        $config->get('finished_orderstate')
                                        )))
        {
            // Activate related subscriptions:
            $app->zoocart->subscription->updateSubscriptions($order, 1);
        }
        elseif(in_array($order->state, array(
                                        $config->get('new_orderstate'),
                                        $config->get('payment_pending_orderstate'),
                                        $config->get('canceled_orderstate'),
                                        $config->get('payment_failed_orderstate')
                                        )))
        {
            // Dectivate related subscriptions:
            $app->zoocart->subscription->updateSubscriptions($order, 0);
        }
        

		// Compare states and fix the changes, were made to orderhistories:
		$changes = $order->compareWith($old);
		$fix_time = time();
		if(!$is_new && !empty($changes)){
			foreach($changes as $key=>$change){
				$history = $app->object->create('Orderhistory');

				$history->order_id = $order->id;
				$history->timestamp = $fix_time;
				$history->property = $key;
				$history->value_old = $change;
				$history->value_new = $order->$key;
				$history->modified_by = $app->user->get()->id;

				$app->zoocart->table->orderhistories->save($history);
			}
		}

		// Now deal with notification emails
		if (!$is_new && $old_state != $order->state && $app->request->getBool('notify_user', 0)) {

			$app->zoocart->email->send('order_state_change', $order);

		}

		// New order notification
		if ($is_new) {
		
			//bixie add orderdata
			$items = $order->getItems();
			$itemnames = array();
			foreach ($items as $item) {
				$itemnames[] = $item->name;
			}
			$order->order_items = implode('<br/>',$itemnames);
			
			$address = $order->getBillingAddress();
			$elements = $address->getElements();
			$allData = array();
			foreach($elements as $key => $element) {
				$key = $element->config->get('billing', '');
				$value = $element->get('value');
				if (!empty($key)) {
					$allData[] = $element->config->get('name', '').': '.$value;
				}
			}
			$order->user_data = implode('<br/>',$allData);
	

			$app->zoocart->email->send('order_new', $order);
			$app->zoocart->email->sendAdmin('order_new_admin', $order);

			if(!empty($order->discount_info))
			{
				$discount = json_decode($order->discount_info);
				$app->zoocart->table->discounts->hit($discount->id);
			}
		}
	}

	/**
	 * Triggered on order deleted action
	 *
	 * @param $event
	 * @param array $args
	 */
	public static function deleted($event, $args = array()){

		$id = $event['order_id'];
		if(!$id){
			return;
		}

		$app = App::getInstance('zoo');

		// Cleaning up related orderhistories:
		$app->zoocart->table->orderhistories->removeByOrder($id);

		// Cleaning up orderitems:
		$app->zoocart->table->orderitems->removeByOrder($id);
	}

}
