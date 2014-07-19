<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class Shippingrate {

	public $id;
	
	public $name;

	public $type;

	public $price_from;

	public $price_to;

	public $quantity_from;

	public $quantity_to;

	public $weight_from;

	public $weight_to;

	public $price;

	public $countries;

	public $states;

	public $cities;

	public $zips;

	public $user_groups;

	public $published;

}