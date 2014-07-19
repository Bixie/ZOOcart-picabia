<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TaxesTable extends ZoocartTable {

	public function __construct($app) {
		parent::__construct($app, 'tax');
	}

}

class TaxesTableException extends ZoocartTableException {}