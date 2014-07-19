<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class Update31BETA24 extends zlUpdate {

	/* List of obsolete files and folders */
	protected $_obsolete = array(
		'files'	=> array(
			'plugins/system/zoocart/zoocart/classes/ukpagination.php',
			'plugins/system/zoocart/zoocart/views/site/cart/tmpl/_address.php',
			'plugins/system/zoocart/zoocart/views/site/cart/tmpl/_chooseshipping.php',
			'plugins/system/zoocart/zoocart/views/site/cart/tmpl/_choosepayment.php',
			'plugins/system/zoocart/zoocart/views/site/cart/tmpl/_checkout_address.php',
			'plugins/system/zoocart/zoocart/views/site/cart/tmpl/_authenticate.php',
			'plugins/system/zoocart/zoocart/views/site/cart/tmpl/_login.php',
			'plugins/system/zoocart/zoocart/views/site/cart/tmpl/_register.php',
			'plugins/system/zoocart/zoocart/views/site/addresses/tmpl/_addressrow.php',
			'plugins/system/zoocart/zoocart/views/site/addresses/tmpl/edit.php',
			'plugins/system/zoocart/zoocart/renderer/address.php'
		)
	);

	/**
	 * Performs the update
	 */
	public function run()
	{
		// remove obsolete files
		$this->removeObsolete();

		if(!$this->column_exists('published', '#__zoo_zl_zoocart_taxes')) {
			$query = 'ALTER TABLE `#__zoo_zl_zoocart_taxes` CHANGE `enabled` `published` TINYINT(1)';
			$this->db->setQuery($query)->execute();
		}

		if(!$this->column_exists('published', '#__zoo_zl_zoocart_emails')) {
			$query = 'ALTER TABLE `#__zoo_zl_zoocart_emails` CHANGE `enabled` `published` TINYINT(1)';
			$this->db->setQuery($query)->execute();
		}

		if(!$this->column_exists('published', '#__zoo_zl_zoocart_shippingrates')) {
			$query = 'ALTER TABLE `#__zoo_zl_zoocart_shippingrates` ADD `published` TINYINT(1) DEFAULT 0';
			$this->db->setQuery($query)->execute();
			// Updating query for existing records:
			$uquery = 'UPDATE `#__zoo_zl_zoocart_shippingrates` SET `published`=1';
			$this->db->setQuery($uquery)->execute();
		}

		if(!$this->column_exists('published', '#__zoo_zl_zoocart_currencies')) {
			$query = 'ALTER TABLE `#__zoo_zl_zoocart_currencies` ADD `published` TINYINT(1) DEFAULT 0';
			$this->db->setQuery($query)->execute();
			// Updating query for existing records:
			$uquery = 'UPDATE `#__zoo_zl_zoocart_currencies` SET `published`=1';
			$this->db->setQuery($uquery)->execute();
		}
	}
}