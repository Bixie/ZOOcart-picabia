<?php
/**
 * @package		ZOOcart
 * @author		ZOOlanders http://www.zoolanders.com
 * @copyright	Copyright (C) JOOlanders, SL
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
define('SECONDS_PER_DAY', 86400);

/**
 * Class zoocartSubscriptionHelper
 * Subscriptions operations support helper
 */
class zoocartSubscriptionHelper extends AppHelper {

	/**
	 * Difference between now and timestamp in days
	 *
	 * @param $end_time
	 * @return int
	 */
	private function _daysLeft($end_time){

		$left = ceil(($end_time-time())/SECONDS_PER_DAY);

		return ($left>0)?$left:0;
	}

	/**
	 * Updaterelated subscriptions
	 *
	 * @param Order
	 * @param int Newstate
	 *
	 * @return void
	 */
	public function updateSubscriptions($order, $newstate=1){
		if($order){

			$tzoffset = $this->app->date->getOffset();
			$cur_date = $this->app->date->create('now', $tzoffset);

			$orderitems = $order->getItems();
			if(!empty($orderitems)){
				foreach($orderitems as $item){
					$subscription  = $this->app->table->subscriptions->getRelatedSubscription($order->id, $item->item_id);

					if($subscription && $item->subscription)
					{
						$subs_data = json_decode($item->subscription);

                        if($subscription->published != $newstate)
                        {
                            // Update it only if subscription state inverted:
						    $subscription->publish_up = $newstate ? $cur_date->toSql() : '0000-00-00 00:00:00';
						    $subscription->publish_down = $newstate ? $this->app->date->create('+'.(int)$subs_data->duration.' day', $tzoffset)->toSql() : '0000-00-00 00:00:00';
						    $subscription->published = $newstate;
                        }

						$this->app->zoocart->table->subscriptions->save($subscription);
					}
				}
			}
		}
	}

	/**
	 * Cleanup subscriptions
	 *
	 * @param Order
	 * @param int
	 *
	 * @return void
	 */
	public function CleanupSubscriptions($order_id){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete($this->app->table->subscriptions->name)
			  ->where(array(
				  'order_id='.(int)$order_id
			          ));
		$db->setQuery($query);
		$db->execute();
	}


	/**
	 * Return subscription status of user to subscriptionlevel as a code
	 * 0 - Not subscribed
	 * 1 - Subscribed, active subscription
	 * 2 - Subscribed, subscription not active
	 * 3 - Subscribed, subscription expired
	 *
	 * @param   int $user_id
	 * @param   int $subscription_id
	 * @internal param $order_id
	 * @return  object Status
	 */
	public function getUserSubscriptionStatus($user_id, $subscription_id){
		$state = new stdClass();
		$state->code = 0;
		$state->days_left = 0;

		if(empty($subscription_id)){
			return $state;
		}

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query  ->select('s.*')
				->from($this->app->table->subscriptions->name.' s')
				->where(array(
					's.`user_id`='.(int)$user_id,
				    's.`id`='.(int)$subscription_id
				        ))
				->limit(1);
		$db->setQuery($query);
		$subs = $db->loadObject();

		if(!empty($subs)){

			$now = time();
			$exp = strtotime($subs->publish_down);

			$exp = $exp>0?$exp:0;

			$state->subscription = $subs->id;

			// If level expiration date has gone:
			if(($exp>0 && $now>$exp)){
				$state->code = 3;
			}elseif(!$subs->published){
				$state->code = 2;
			}else{
				$state->code = 1;
			}

			// Count of days till the end of term of subscription:
			if($exp>0){
				$state->days_left = $this->_daysLeft($exp);
			}
		}

		return $state;
	}

	/**
	 * Check if user has at least 1 valid subscription for the item
	 *
	 * @param mixed     Item
	 * @param int       User id  (Current user id will be taken if ommited)
	 * @param int       Mode: 0 "OR" mode - returns TRUE if at least one of subs is valid, 1 "AND" mode - returns TRUE if all subscriptions is valid
	 *
	 * @return bool
	 */
	public function hasValidSubscription($items, $user_id = null, $mode = 0){
		$valid = false;
		$user_id = $user_id?$user_id:$this->app->user->get()->id;
		$item_ids = array();
		$db = $this->app->database;

		if($user_id && !empty($items)){

			if(is_array($items)){
				$first = $items[0];
				// Check array type:
				if(is_object($first)){
					// Looks like we dealing with object array, so let's extract item ids:
					foreach($items as $item){
						$item_ids[] = $item->id;
					}
				}else{
					$item_ids = $items;
				}
			}else{
				$item_ids[] = is_object($items)?(int)$items->id:(int)$items;
			}

			JArrayHelper::toInteger($item_ids);

			// Get all user's subscriptions for the provided items:
			$query = $db->getQuery(true);
			$query  ->select('i.`id`, s.`id` AS sub')
					->from('`#__zoo_item` i')
					->join('LEFT', '`'.$this->app->zoocart->table->subscriptions->name.'` s ON s.`item_id`=i.`id` AND s.`user_id`='.(int)$user_id)
					->where(array(
					    'i.`id` IN('.implode(',', $item_ids).')'
					        ));
			$db->setQuery($query);
			$subs = $db->loadObjectList();

			if(!empty($subs)){
				$valid = ($mode==1);
				foreach($subs as $subscription){
					$sub_valid = false;
					if(1==$this->getUserSubscriptionStatus($user_id, $subscription->sub)->code){
						$sub_valid = true;
					}
					if(!$mode){
						if($sub_valid){
							$valid = true;
							break;
						}
					}else{
						$valid = $valid && $sub_valid;
					}
				}
			}
		}

		return $valid;
	}

	/**
	 * Get duration element from item, using mapping
	 *
	 * @param   Item    $item
	 * @param   string  Subscription property name, that you wish to get (e.g. duration, etc.)
	 * @return  mixed
	 */
	public function getSubscriptionElements($item){

		$duration = array();

		if(!empty($item)){

			$config = array();
			$config[] = $item->getApplication()->application_group;
			$config[] = $item->type;
			$config[] = 'subscription';

			$renderer = $this->app->renderer->create('item')->addPath( array($this->app->path->path( "zoocart:mapping" )) );
			$subs_config = $renderer->getConfig('item')->get(implode('.',$config));

			if(!empty($subs_config)){
				foreach($subs_config as $property=>$elements){
					// Get first element from each position:
					$element = @array_shift($elements);
					if($element){
						$duration[$property] = $item->getElement($element['element']);
					}
				}
			}
		}

		return $duration;
	}

	/**
	 * Detects if the subscription is renewable
	 *
	 * @param object
	 * @return bool
	*/
	public function isRenewable($subscription){
		//@TODO: Implement subs renewable restrictions
		return true;
	}

	/**
	 * Returns data object, necessary for creation renewal operation request
	 *
	 * @param \Subscription
	 * @return onject
	 */
	public function getRenewalData($subscrition){
		$data = null;

		if(!empty($subscrition)){
			$order_item = $this->app->zoocart->table->orderitems->getByOrderItem($subscrition->order_id, $subscrition->item_id);
			if(!empty($order_item)){
				$data = new stdClass();
				$data->item_id = $subscrition->item_id;
				$data->quantity = $order_item->quantity;
				$data->variations = $order_item->variations;
			}
		}

		return $data;
	}

}