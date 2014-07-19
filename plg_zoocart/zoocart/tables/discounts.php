<?php
/**
 * @package		ZOOcart
 * @author		ZOOlanders http://www.zoolanders.com
 * @copyright	Copyright (C) JOOlanders, SL
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Class DiscountsTable
 * Discount db table
 */
class DiscountsTable extends ZoocartTable {

	/**
	 * Class constructor
	 *
	 * @param $app
	 */
	public function __construct($app) {
		parent::__construct($app, 'discount');
	}

	/**
	 * Finding discount record by Coupon code
	 *
	 * @param $code
	 * @return object
	 */
	public function getDiscountByCode($code){
		$discount = null;
		if(!empty($code))
		{
			$db = $this->database;
			$query = $db->getQuery(true);
			$query  ->select('*')
					->from($this->name)
					->where(array("code LIKE ".$db->quote($db->escape($code))))
					->limit(1);
			$db->setQuery($query);
			$discount = $db->loadObject($this->class);
		}

		return $discount;
	}

	/**
	 * Save prepared data
	 *
	 * @param $object
	 */
	public function save($object)
	{
		$object->usergroups = empty($object->usergroups)?'':implode(',',$object->usergroups);

		parent::save($object);
	}

	/**
	 * Increase used field of the chosen discount
	 *
	 * @param $id
	 */
	public function hit($id){
		$db = JFactory::getDBo();
		$query = $db->getQuery(true);
		$query  ->update($this->name)
				->set(array('`used`=`used`+1'))
				->where(array('`id`='.(int)$id));
		$db->setQuery($query);
		$db->execute();

		$discount = $this->get($id);
		if($discount->hits_per_user){
			$hitrow = $this->app->table->user_discount->getRecord($this->app->user->get()->id,$id);

			if(empty($hitrow)){
				$hitrow = new stdClass();
				$hitrow->user_id = (int)$this->app->user->get()->id;
				$hitrow->discount_id = (int)$id;
				$hitrow->hits = 0;
			}

			$hitrow->hits +=1;
			$this->app->table->user_discount->save($hitrow);
		}
	}
}

/**
 * Class DiscountsTableException
 */
class DiscountsTableException extends ZoocartTableException {}