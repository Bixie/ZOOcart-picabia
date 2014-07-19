<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class Update31BETA17 extends zlUpdate {

	/* List of obsolete files and folders */
	protected $_obsolete = array(
		'files'	=> array(
			'plugins/system/zoocart/zoocart/assets/js/zoocart.min.js',
			'plugins/system/zoocart/zoocart/elements/addtocart/tmpl/render/default/script.min.js',
			'plugins/system/zoocart/zoocart/renderer/element/submission/uikit.php'
		),
		'folders' => array()
	);

	/**
	 * Performs the update
	 */
	public function run()
	{
		// add order discount_info column
		if(!$this->column_exists('discount_info', '#__zoo_zl_zoocart_orders')) {
			$query = 'ALTER TABLE `#__zoo_zl_zoocart_orders`'
					.' ADD `discount_info` text NOT NULL'
					.' AFTER `shipping_method`';
			$this->db->setQuery($query)->execute();
		}

		// add order currency column
		if(!$this->column_exists('currency', '#__zoo_zl_zoocart_orders')) {
			$query = 'ALTER TABLE `#__zoo_zl_zoocart_orders`'
					.' ADD `currency` text NOT NULL'
					.' AFTER `total`';
			$this->db->setQuery($query)->execute();
		}

		// Replace order states presets with the new ones:
		$this->db->setQuery("UPDATE `#__zoo_zl_zoocart_orderstates` SET `name`='PLG_ZOOCART_ORDER_STATE_PENDING', `description`='PLG_ZOOCART_ORDER_STATE_PENDING_DESCR' WHERE `id`=1;")->execute();
		$this->db->setQuery("UPDATE `#__zoo_zl_zoocart_orderstates` SET `name`='PLG_ZOOCART_ORDER_STATE_RECEIVED', `description`='PLG_ZOOCART_ORDER_STATE_RECEIVED_DESCR' WHERE `id`=2;")->execute();
		$this->db->setQuery("UPDATE `#__zoo_zl_zoocart_orderstates` SET `name`='PLG_ZOOCART_ORDER_STATE_PROCESSING', `description`='PLG_ZOOCART_ORDER_STATE_PROCESSING_DESCR' WHERE `id`=3;")->execute();
		$this->db->setQuery("UPDATE `#__zoo_zl_zoocart_orderstates` SET `name`='PLG_ZOOCART_ORDER_STATE_SHIPPED', `description`='PLG_ZOOCART_ORDER_STATE_SHIPPED_DESCR' WHERE `id`=4;")->execute();
		$this->db->setQuery("UPDATE `#__zoo_zl_zoocart_orderstates` SET `name`='PLG_ZOOCART_ORDER_STATE_COMPLETED', `description`='PLG_ZOOCART_ORDER_STATE_COMPLETED_DESCR' WHERE `id`=5;")->execute();
		$this->db->setQuery("UPDATE `#__zoo_zl_zoocart_orderstates` SET `name`='PLG_ZOOCART_ORDER_STATE_FAILED', `description`='PLG_ZOOCART_ORDER_STATE_FAILED_DESCR' WHERE `id`=6;")->execute();
		$this->db->setQuery("UPDATE `#__zoo_zl_zoocart_orderstates` SET `name`='PLG_ZOOCART_ORDER_STATE_CANCELED', `description`='PLG_ZOOCART_ORDER_STATE_CANCELED_DESCR' WHERE `id`=7;")->execute();
		$this->db->setQuery("UPDATE `#__zoo_zl_zoocart_orderstates` SET `name`='PLG_ZOOCART_ORDER_STATE_REFUNDED', `description`='PLG_ZOOCART_ORDER_STATE_REFUNDED_DESCR' WHERE `id`=8;")->execute();
		$this->db->setQuery("UPDATE `#__zoo_zl_zoocart_orderstates` SET `name`='PLG_ZOOCART_ORDER_STATE_VALIDATING', `description`='PLG_ZOOCART_ORDER_STATE_VALIDATING_DESCR' WHERE `id`=9;")->execute();

		// remove obsolete files
		$this->removeObsolete();
	}
}