<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class Update31BETA14 extends zlUpdate {

	/* List of obsolete files and folders */
	protected $_obsolete = array(
		'files'	=> array(
			'plugins/system/zoocart/zoocart/assets/js/bootstrap.min.js',
			'plugins/system/zoocart/zoocart/assets/css/bootstrap.min.css',
			'plugins/system/zoocart/zoocart/assets/css/bootstrap-responsive.min.css',
			'plugins/system/zoocart/zoocart/assets/js/checkout.js',
			'plugins/system/zoocart/zoocart/classes/bspagination.php',
			'plugins/system/zoocart/zoocart/renderer/element/submission/bootstrap.php',
			'plugins/system/zoocart/zoocart/views/admin/partials/typeconfig.php'
		),
		'folders' => array(
			'plugins/system/zoocart/zoocart/assets/js/bootstrap_previous',
			'plugins/system/zoocart/zoocart/elements/addtocart/assets',
			'plugins/system/zoocart/zoocart/sql/updates'
		)
	);

	/**
	 * Performs the update
	 */
	public function run()
	{
		// add currencies new columns
		if(!$this->column_exists('symbol', '#__zoo_zl_zoocart_currencies')) {
			$query = 'ALTER TABLE `#__zoo_zl_zoocart_currencies`'
				.' ADD `symbol` varchar(6) NOT NULL'
				.' AFTER `name`';
			$this->db->setQuery($query)->execute();
		}

		if(!$this->column_exists('format', '#__zoo_zl_zoocart_currencies')) {
			$query = 'ALTER TABLE `#__zoo_zl_zoocart_currencies`'
				.' ADD `format` varchar(255) NOT NULL'
				.' AFTER `symbol`';
			$this->db->setQuery($query)->execute();
		}


		// set the new format values
		if($this->column_exists('symbol_left', '#__zoo_zl_zoocart_currencies')) {
			$query = 'SELECT id, symbol_left, symbol_right'
					.' FROM `#__zoo_zl_zoocart_currencies`';

			if ($currencies = $this->db->setQuery($query)->loadObjectList('id')) {
				foreach ($currencies as $id => $currency) {

					// check which symbol is set and use it as reference
					if (strlen($currency->symbol_left)) {
						$symbol = $currency->symbol_left;
						$format = '%s%v / %s -%v';
					} else {
						$symbol = $currency->symbol_right;
						$format = '%v%s / -%v%s';
					}
					
					// update table
					$query = 'UPDATE `#__zoo_zl_zoocart_currencies`'
							.' SET `symbol` = ' . $this->db->quote($symbol)
							.', `format` = ' . $this->db->quote($format)
							.' WHERE `id` = ' . $id;
					$this->db->setQuery($query)->execute();
				}
			}

			// remove deprecated columns
			$query = 'ALTER TABLE `#__zoo_zl_zoocart_currencies`'
					.' DROP COLUMN `symbol_left`';
			$this->db->setQuery($query)->execute();

			$query = 'ALTER TABLE `#__zoo_zl_zoocart_currencies`'
					.' DROP COLUMN `symbol_right`';
			$this->db->setQuery($query)->execute();
		}


		// remove obsolete files
		$this->removeObsolete();

		  
		// app related tasks
		$apps_dir = JPATH_ROOT.'/media/zoo/applications';
		foreach(JFolder::folders(JPATH_ROOT.'/media/zoo/applications') as $app_group)
		{
			// remove deprecated app config files
			$path = $apps_dir . '/' . $app_group . '/config/zoocart.xml';
			if (is_readable($path) && is_file($path) && JFile::exists($path)) {
				JFile::delete($path);
			}

			// move address type to new ubication
			$path = $apps_dir . '/' . $app_group . '/zoocart-configs/addresses/address.config';
			if (is_readable($path) && is_file($path) && JFile::exists($path)) {
				// move using copy instead of move as its not working on windows servers
				if (JFile::copy(
					JPath::clean($path),
					JPath::clean(JPATH_ROOT.'/plugins/system/zoocart/zoocart/types/address.config')
				)) {
					JFolder::delete(JPath::clean($apps_dir . '/' . $app_group . '/zoocart-configs'));
				}
			}
		}

		// move ZOOcart settings from ZOO to ZL component
		if ($apps = $this->db->setQuery('SELECT params FROM `#__zoo_application`')->loadObjectList('params')) {
			foreach ($apps as $id => $app_params)
			{
				$app_params = json_decode($app_params->params, true);

				// find ZOOcart data
				if (isset($app_params['global.zoocart'])) {

					// save it to ZL if has not already saved data
					$component = JComponentHelper::getComponent('com_zoolanders');
					if ($component->params->get('zoocart') == null) {

						// set zoocart params
						$component->params->set('zoocart', $app_params['global.zoocart']);

						// update table
						$query = 'UPDATE `#__extensions`'
								.' SET `params` = ' . $this->db->quote($component->params->toString())
								.' WHERE `extension_id` = ' . $component->id;
						$this->db->setQuery($query)->execute();
					}

					// if data found stop the iteration, we can just port one App data
					break;
				}
			}
		}
	}
}