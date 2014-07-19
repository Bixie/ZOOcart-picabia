<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class Update31BETA21 extends zlUpdate {

	/**
	 * Performs the update
	 */
	public function run()
	{
		// add cartitems variations column
		if(!$this->column_exists('variations', '#__zoo_zl_zoocart_cartitems')) {
			$query = 'ALTER TABLE `#__zoo_zl_zoocart_cartitems`'
					.' ADD `variations` text NOT NULL'
					.' AFTER `quantity`';
			$this->db->setQuery($query)->execute();
		}

		// add orderitems variations column
		if(!$this->column_exists('variations', '#__zoo_zl_zoocart_orderitems')) {
			$query = 'ALTER TABLE `#__zoo_zl_zoocart_orderitems`'
					.' ADD `variations` text NOT NULL'
					.' AFTER `quantity`';
			$this->db->setQuery($query)->execute();
		}
	}
}