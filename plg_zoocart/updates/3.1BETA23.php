<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class Update31BETA23 extends zlUpdate {

	/* List of obsolete files and folders */
	protected $_obsolete = array(
		'files'	=> array(
			'plugins/system/zoocart/zoocart/views/admin/orders/tmpl/mail.admin.order.new.php',
			'plugins/system/zoocart/zoocart/views/admin/orders/tmpl/mail.order.new.php',
			'plugins/system/zoocart/zoocart/views/admin/orders/tmpl/mail.order.statechange.php'
		)
	);

	/**
	 * Performs the update
	 */
	public function run()
	{
		// move any custom plugin tmpl to the correct override path
		foreach(JFolder::folders(JPATH_SITE.'/templates') as $template)
		{
			$path = JPath::clean(JPATH_SITE.'/templates/'.$template.'/html/plugins/zoocart_payment');
			if (JFolder::exists($path)) foreach(JFolder::folders($path) as $plugin)
			{
				$plg_path = JPath::clean($path.'/'.$plugin);
				if (JFolder::exists($plg_path)) {
					// move using copy instead of move as its not working on windows servers
					if (JFolder::copy(
						$plg_path,
						JPath::clean(JPATH_SITE . '/templates/' . $template . '/html/plg_zoocart_payment_' . $plugin)
					)) {
						JFolder::delete($plg_path);
					}
				}

				// delete the type folder
				JFolder::delete($path);
			}

			$path = JPath::clean(JPATH_SITE.'/templates/'.$template.'/html/plugins/zoocart_shipping');
			if (JFolder::exists($path)) foreach(JFolder::folders($path) as $plugin)
			{
				$plg_path = JPath::clean($path.'/'.$plugin);
				if (JFolder::exists($plg_path)) {
					// move using copy instead of move as its not working on windows servers
					if (JFolder::copy(
						$plg_path,
						JPath::clean(JPATH_SITE . '/templates/' . $template . '/html/plg_zoocart_shipping_' . $plugin)
					)) {
						JFolder::delete($plg_path);
					}
				}

				// delete the type folder
				JFolder::delete($path);
			}
		}

		// add subscriptions DB columns to CARTITEMS table
		if(!$this->column_exists('subscription', '#__zoo_zl_zoocart_cartitems')) {
			$query = 'ALTER TABLE `#__zoo_zl_zoocart_cartitems`'
					.' ADD `subscription` text NOT NULL'
					.' AFTER `quantity`';
			$this->db->setQuery($query)->execute();
		}

		// add subscriptions DB columns to ORDERITEMS table
		if(!$this->column_exists('subscription', '#__zoo_zl_zoocart_orderitems')) {
			$query = 'ALTER TABLE `#__zoo_zl_zoocart_orderitems`'
					.' ADD `subscription` text NOT NULL'
					.' AFTER `quantity`';
			$this->db->setQuery($query)->execute();
		}


		/** ORDER HISTORY UPDATES **/

		// add new columns
		if(!$this->column_exists('timestamp', '#__zoo_zl_zoocart_orderhistories')) {
			$query = 'ALTER TABLE `#__zoo_zl_zoocart_orderhistories`'
				.' ADD `timestamp` int(11) NOT NULL'
				.' AFTER `order_id`';
			$this->db->setQuery($query)->execute();
		}

		if(!$this->column_exists('property', '#__zoo_zl_zoocart_orderhistories')) {
			$query = 'ALTER TABLE `#__zoo_zl_zoocart_orderhistories`'
				.' ADD `property` varchar(20) NOT NULL'
				.' AFTER `timestamp`';
			$this->db->setQuery($query)->execute();
		}

		if(!$this->column_exists('value_old', '#__zoo_zl_zoocart_orderhistories')) {
			$query = 'ALTER TABLE `#__zoo_zl_zoocart_orderhistories`'
				.' ADD `value_old` text NOT NULL'
				.' AFTER `property`';
			$this->db->setQuery($query)->execute();
		}

		if(!$this->column_exists('value_new', '#__zoo_zl_zoocart_orderhistories')) {
			$query = 'ALTER TABLE `#__zoo_zl_zoocart_orderhistories`'
				.' ADD `value_new` text NOT NULL'
				.' AFTER `value_old`';
			$this->db->setQuery($query)->execute();
		}

		if(!$this->column_exists('modified_by', '#__zoo_zl_zoocart_orderhistories')) {
			$query = 'ALTER TABLE `#__zoo_zl_zoocart_orderhistories`'
				.' ADD `modified_by` int(11) NOT NULL'
				.' AFTER `value_new`';
			$this->db->setQuery($query)->execute();
		}

		// set the new format values
		if($this->column_exists('state', '#__zoo_zl_zoocart_orderhistories')) {
			$query = 'SELECT id, state, date, user_id'
				.' FROM `#__zoo_zl_zoocart_orderhistories`'
				.' ORDER BY `date` ASC';

			if ($stories = $this->db->setQuery($query)->loadObjectList('id')) {
				$prev_state = $stories[0]->state?$stories[0]->state:1; // Default orderstate preset id is "1";

				foreach ($stories as $id => $story) {

					$value_old = $prev_state;
					$value_new = $story->state;

					$timestamp = strtotime($story->date);
					$modified_by = $story->user_id;

					// update table
					if($value_old==$value_new){
						$query = 'DELETE FROM `#__zoo_zl_zoocart_orderhistories`'
								.' WHERE `id` = '.(int)$id;
					}else{
						$query = 'UPDATE `#__zoo_zl_zoocart_orderhistories`'
							.' SET `timestamp` = ' . (int)$timestamp
							.', `property` = ' . $this->db->quote('state')
							.', `value_old` = ' . $this->db->quote($value_old)
							.', `value_new` = ' . $this->db->quote($value_new)
							.', `modified_by` = ' . (int)$modified_by
							.' WHERE `id` = ' . $id;
						$prev_state = $value_new;
					}

					$this->db->setQuery($query)->execute();
				}
			}

			// remove deprecated columns
			$query = 'ALTER TABLE `#__zoo_zl_zoocart_orderhistories`'
				.' DROP COLUMN `state`';
			$this->db->setQuery($query)->execute();

			$query = 'ALTER TABLE `#__zoo_zl_zoocart_orderhistories`'
				.' DROP COLUMN `user_id`';
			$this->db->setQuery($query)->execute();

			$query = 'ALTER TABLE `#__zoo_zl_zoocart_orderhistories`'
				.' DROP COLUMN `date`';
			$this->db->setQuery($query)->execute();
		}
		
		// remove obsolete files
		$this->removeObsolete();
	}
}