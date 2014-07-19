<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// register TableHelper class
App::getInstance('zoo')->loader->register('TableHelper', 'helpers:table.php');

/**
 * Helper for the database table classes
 * 
 * @package Framework.Helpers
 */
class zoocartTableHelper extends TableHelper {

	/**
	 * The table prefix
	 * 
	 * @var string
	 */
	protected $_prefix;

	/**
	 * The list of loaded table classes
	 * 
	 * @var array
	 */
	protected $_tables = array();

	/**
	 * Get a table object
	 * 
	 * @param string $name The name of the table to retrieve
	 * @param string $prefix An alternative prefix
	 * 
	 * @return AppTable The table object
	 */
	public function get($name, $prefix = null) {
		
		// load table class
		$class = $name.'Table';
		$this->app->loader->register($class, 'zoocart:tables/'.strtolower($name).'.php');

		// set tables prefix
		if ($prefix == null) {
			$prefix = $this->_prefix;
		}
		
		// add table, if not exists
		if (!isset($this->_tables[$name])) {
			$this->_tables[$name] = class_exists($class) ? new $class($this->app) : new AppTable($this->app, $prefix.$name);
		}

		return $this->_tables[$name];
	}
}