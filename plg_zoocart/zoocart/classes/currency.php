<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class Currency {

	public $id;

	public $name;

	public $code;
	
	public $symbol;

	public $format;
	
	public $num_decimals;
	
	public $num_decimals_show;

	public $decimal_sep;

	public $thousand_sep;

	public $conversion_rate;

	public $published;
}