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
 * Class ZoocartTable
 * Abstract class. Contains methods for typical operations with records and lists
 */
abstract class ZoocartTable extends AppTable {

	/**
	 * Table constructor
	 *
	 * @param   object  Application
	 * @param   string  Resource name
	 */
	public function __construct($app, $resource_name){

		$inflector = $app->zoocart;
		$resource_name = strtolower($resource_name);

		parent::__construct($app, '#__zoo_zl_zoocart_'.$inflector->toPlural($resource_name), 'id');

		$this->app->loader->register(ucfirst($resource_name), 'classes:'.$resource_name.'.php');
		$this->class = ucfirst($resource_name);
	}

	/**
	 * Toggle record publication state
	 *
	 * @param   int     Record id
	 * @param   string  State field name
	 *
	 * @return  mixed
	 */
	public function toggleState($id){
		$db = $this->app->database;

		if(!array_key_exists('published', $this->getTableColumns())){
			// Do nothing if there are no such column in the table
			return false;
		}

		$query = $db->getQuery(true);
		$query  ->update($this->name)
		        ->set(array('`published`= NOT `published`'))
		        ->where(array('`'.$this->key.'`='.(int)$id));
		$db->setQuery($query);

		return $db->execute();
	}

	/**
	 * Update record ordering, according to incoming array
	 *
	 * @param   array   Ordered array of record ids
	 *
	 * @return  mixed
	 */
	public function updateOrdering($data){
		$db = $this->app->database;

		if(!array_key_exists('ordering', $this->getTableColumns())){
			// Do nothing if there are no such column in the table
			return false;
		}

		if(!empty($data))
		{
			foreach($data as $position=>$id){
				$query = $db->getQuery(true);
				$query  ->update($this->name)
				        ->set(array('`ordering`='.(int)$position))
				        ->where(array('`'.$this->key.'`='.(int)$id));
				$db->setQuery($query);
				$success = $db->execute();
			}
		}

		return $success;
	}
}

/**
 * Class ZoocartTableException
 * Extends basic AppTableException class
 */
class ZoocartTableException extends AppTableException {}