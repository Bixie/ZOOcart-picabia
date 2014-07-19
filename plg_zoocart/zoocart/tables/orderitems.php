<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class OrderitemsTable extends ZoocartTable {

	public function __construct($app) {
		parent::__construct($app, 'orderitem');
	}

	/**
	 * Get orderitems by order id
	 *
	 * @param $order_id
	 * @return mixed
	 */
	public function getByOrder($order_id) {
		return $this->all(array('conditions' => 'order_id = ' . (int) $order_id));
	}

	/**
	 * Get single orderitem by pair: order_id-item_id
	 *
	 * @param int orderId
	 * @param int item_id
	 *
	 * @return mixed
	 */
	public function getByOrderItem($order_id, $item_id){
		return $this->find('first', array('conditions' => 'order_id='.(int)$order_id.' AND item_id='.(int)$item_id ));
	}

	public function save($object){

		$new = !(bool) $object->id;

		if($new && !empty($object->subscription)){

			$subscription = $this->app->object->create('Subscription');
			$subscription->item_id = (int)$object->item_id;
			$subscription->order_id = (int)$object->order_id;
			$subscription->user_id = (int) $this->app->user->get()->id;

			$this->app->table->subscriptions->save($subscription);
		}

		$result = parent::save($object);

		return $result;
	}

	/**
	 * Cleanup records related to specified order_id
	 *
	 * @param $order_id
	 * @return void
	 */
	public function removeByOrder($order_id){
		$db = $this->app->database;

		$query = $db->getQuery(true);
		$query  ->delete($this->name)
		        ->where(array('`order_id`='.(int)$order_id));
		$db->setQuery($query);
		$db->execute();
	}

}

class OrderitemsTableException extends ZoocartTableException {}