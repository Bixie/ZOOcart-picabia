<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class ShippingratesTable extends ZoocartTable {

	public function __construct($app) {
		parent::__construct($app, 'shippingrate');
	}

	public function save($object)
	{
		$object->user_groups = empty($object->user_groups)?'':implode(',',$object->user_groups);
		$object->countries = empty($object->countries)?'':implode(',',$object->countries);

		parent::save($object);
	}

	public function getByType($type){

		// get database
		$db = $this->database;

 		$query = "SELECT * "
		        ." FROM ".$this->name." AS a"
		        ." WHERE a.type = ".$db->Quote($type);

		return $this->_queryObject($query);

	}
}

class ShippingratesTableException extends ZoocartTableException {}