<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class PaymentEvent {

	public static function saved($event, $args = array()) {
		$app = App::getInstance('zoo');

		$payment = $event->getSubject();
		
		if($payment->order_id) {
			if($payment->status == 1) {
				$order = $app->zoocart->table->orders->get($payment->order_id);
				$order->state = $app->zoocart->getConfig()->get('payment_received_orderstate', 2);
				$app->zoocart->table->orders->save($order);
			}
		}
	}

}
