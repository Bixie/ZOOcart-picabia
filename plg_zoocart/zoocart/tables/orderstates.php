<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class OrderstatesTable extends ZoocartTable {

	public function __construct($app) {
		parent::__construct($app, 'orderstate');
	}
	
	public function getByName($name){

		// get database
		$db = $this->database;

 		$query = "SELECT * "
		        ." FROM ".$this->name." AS a"
		        ." WHERE a.name = ".$db->Quote($name);

		return $this->_queryObject($query);

	}
}

class OrderstatesTableException extends ZoocartTableException {}