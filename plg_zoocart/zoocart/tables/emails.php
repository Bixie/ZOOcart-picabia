<?php
/**
 * @package		ZOOcart
 * @author		ZOOlanders http://www.zoolanders.com
 * @copyright	Copyright (C) JOOlanders, SL
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class EmailsTable extends ZoocartTable {

	public function __construct($app) {
		parent::__construct($app, 'email');
	}

	/**
	 * Get email template by it's type
	 *
	 * @param   string
	 *
	 * @return  mixed
	 */
	public function getByType($type){
		$db = JFactory::getDBo();
		$query = $db->getQuery(true);
		$query  ->select('*')
				->from($this->name)
				->where(array(
					"`type`='".$db->escape($type)."'"
				        ));
		$db->setQuery($query, 0, 1);

		return $db->loadObjectList();
	}

	/**
	 * Save hook
	 *
	 * @param $object
	 */
	public function save($object)
	{
		$object->groups = implode(',',$object->groups);

		parent::save($object);
	}
}

class EmailsTableException extends ZoocartTableException {}