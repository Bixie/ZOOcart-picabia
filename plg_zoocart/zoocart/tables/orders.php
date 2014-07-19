<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class OrdersTable extends ZoocartTable {

	public function __construct($app) {
		parent::__construct($app, 'order');
	}

	protected function _initObject($object) {

		parent::_initObject($object);

		// trigger init event
		$this->app->event->dispatcher->notify($this->app->event->create($object, 'order:init'));

		return $object;
	}

	public function save($object) {

		$is_new = !(bool) $object->id;
		$old = $is_new ? $this->app->object->create('Order') : clone $this->get($object->id, true);

		$object->modified_on = $this->app->date->create('now', $this->app->date->getOffset())->toSql();

		if ($is_new) {
			$object->created_on = $this->app->date->create('now', $this->app->date->getOffset())->toSql();
			$object->ip = array_key_exists('REMOTE_ADDR', $_SERVER) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';	
		}

		// save order
		$result = parent::save($object);

		// save order items
		foreach ($object->getItems() as $orderitem) {
			$orderitem->order_id = $object->id;
			$this->app->zoocart->table->orderitems->save($orderitem);
		}

		// trigger save event
		$this->app->event->dispatcher->notify($this->app->event->create($object, 'order:saved', compact('is_new', 'old')));

		return $result;
	}

	public function delete($object) {

		$result = parent::delete($object);

		// trigger deleted event
		$this->app->event->dispatcher->notify($this->app->event->create($object, 'order:deleted',  array('order_id' => $object->id)));

		return $result;
	}
	
}

class OrderTableException extends ZoocartTableException {}