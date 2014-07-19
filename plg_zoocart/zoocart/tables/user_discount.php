<?php
/**
 * @package		ZOOcart
 * @author		ZOOlanders http://www.zoolanders.com
 * @copyright	Copyright (C) JOOlanders, SL
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class User_discountTable extends ZoocartTable {

	/**
	 * Class constructor
	 *
	 * @param $app
	 */
	public function __construct($app) {
		AppTable::__construct($app, '#__zoo_zl_zoocart_user_discount','id');
	}

	/**
	 * Get record by user id and discount id
	 *
	 * @param $uid
	 * @param $did
	 * @return mixed
	 */
	public function getRecord($uid,$did){

		// get database
		$db = JFactory::getDBo();

		$query = $db->getQuery(true);
		$query  ->select('*')
			    ->from($this->name)
				->where(array(
					'user_id='.(int)$uid,
				    'discount_id='.(int)$did
				     ))
				->limit(1);
		$db->setQuery($query);

		return $db->loadObject();
	}
}

class User_discountTableException extends ZoocartTableException {}