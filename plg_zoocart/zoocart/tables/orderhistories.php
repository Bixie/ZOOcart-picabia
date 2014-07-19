<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class OrderhistoriesTable extends ZoocartTable {

	public function __construct($app) {
		parent::__construct($app, 'orderhistory');
	}

	/**
	 * Save oredrhistory record
	 *
	 * @param $object
	 * @return mixed
	 */
	public function save($object) {

		$object->modified_by = JFactory::getUser()->id;

		$result = parent::save($object);

		return $result;
	}

	/**
	 * Get all changes by order_id
	 *
	 * @param $order_id
	 * @return mixed
	 */
	public function getByOrder($order_id) {
		return $this->all(array('conditions' => 'order_id = ' . (int) $order_id));
	}

	/**
	 * Get grouped by time record id's, related to order
	 *
	 * @param $order_id
	 * @return array
	 */
	public function getGroupedByTime($order_id){
		$records = array();

		$db = $this->app->database;

		$query = $db->getQuery(true);
		$query  ->select("h.`timestamp`, GROUP_CONCAT(DISTINCT h.`id` ORDER BY `timestamp` DESC SEPARATOR ',') AS `records`, u.`username`")
				->from($this->name.' h')
				->join('LEFT','#__users u ON h.`modified_by`=u.`id`')
				->where(array(
					'h.`order_id`='.(int)$order_id
				        ))
				->group('h.`timestamp`')
				->order('h.`timestamp` DESC');

		$db->setQuery($query);
		$records = $db->loadObjectList();

		return $records;
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

class OrderhistoriesTableException extends ZoocartTableException {}