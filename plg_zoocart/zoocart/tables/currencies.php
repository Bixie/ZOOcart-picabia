<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CurrenciesTable extends ZoocartTable {

	/**
	 * Class constructor
	 *
	 * @param $app
	 */
	public function __construct($app) {
		parent::__construct($app, 'currency');
	}

	/**
	 * Save prepared data
	 *
	 * @param $object
	 */
	public function save($object)
	{
		$object->code = strtoupper($object->code);

		parent::save($object);
	}
	
}

class CurrenciesTableException extends ZoocartTableException {}