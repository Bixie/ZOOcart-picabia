<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class AddressesTable extends ZoocartTable {

	public function __construct($app) {
		parent::__construct($app, 'address');

		// DB reference
		$this->db = $this->app->database;
	}

	protected function _initObject($object) {

		parent::_initObject($object);

		// workaround for php bug, which calls constructor before filling values
		if (is_string($object->params) || is_null($object->params)) {
			// decorate data as object
			$object->params = $this->app->parameter->create($object->params);
		}

		if (is_string($object->elements) || is_null($object->elements)) {
			// decorate data as object
			$object->elements = $this->app->data->create($object->elements);
		}

		// add to cache
		$key_name = $this->key;

		if ($object->$key_name && !key_exists($object->$key_name, $this->_objects)) {
			$this->_objects[$object->$key_name] = $object;
		}

		// trigger init event
		$this->app->event->dispatcher->notify($this->app->event->create($object, 'address:init'));

		return $object;
	}

	/**
	 * Count addresses, related to current user:
	 */
	public function countOwn(){
		$count = 0;
		$user = $this->app->user->get();
		if($user->id)
		{
			$db = JFactory::getDBo();
			$query = $db->getQuery(true);
			$query  ->select("COUNT(id)")
				    ->from($this->name)
					->where(array("user_id=".(int)$user->id));
			$db->setQuery($query);
			$count = (int)$db->loadResult();
		}

		return $count;
	}

	public function save($object) {

		$new = !(bool) $object->id;

		if(count($this->getByUser($object->user_id, $object->type)) <= 0) {
			$object->default = 1;
		}

		if(is_array($object->elements)) {
			$object->elements = json_encode($object->elements);
		}

		$result = parent::save($object);

		// trigger save event
		$this->app->event->dispatcher->notify($this->app->event->create($object, 'address:saved', compact('new')));

		return $result;
	}

	/*
		Function: delete
			Override. Delete object from database table.

		Returns:
			Boolean.
	*/
	public function delete($object) {

		$result = parent::delete($object);

		// trigger deleted event
		$this->app->event->dispatcher->notify($this->app->event->create($object, 'address:deleted'));

		return $result;
	}

	public function getByUser( $user_id, $type = null) {

		$options = 'user_id = ' . (int)$user_id;
		if ($type) {
			$options .= " AND type = ".$this->db->q($type);
		}

		return $this->all(array('conditions' => $options));
	}

	public function getDefaultAddress( $user_id, $type = 'billing') {

		$options = 'user_id = ' . (int)$user_id;
		$options .= " AND type = ".$this->db->q($type);

		if($default = $this->first(array('conditions' => $options . ' AND ' . $this->db->quoteName('default') . ' = 1'))) {
			return $default;
		} else {
			return $this->first(array('conditions' => $options));
		}
	}

	public function isInitialized($key) {
		return isset($this->_objects[$key]);
	}
}

class AddressesTableException extends AppException {}