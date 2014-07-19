<?php
/**
* @package		ZOOcart Module
* @author    	ZOOlanders http://www.zoolanders.com
* @copyright 	Copyright (C) JOOlanders SL
* @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

return 
'{"fields": {

	"show_items":{
		"type":"radio",
		"label":"MOD_ZOOCART_PARAMS_SHOW_ITEMS",
		"default":"1"
	},
	"show_cart_link":{
		"type":"radio",
		"label":"MOD_ZOOCART_PARAMS_SHOW_CART_LINK",
		"default":"1"
	},
	"show_addresses_link":{
		"type":"radio",
		"label":"MOD_ZOOCART_PARAMS_SHOW_ADDRESSES_LINK",
		"default":"1"
	},
	"show_orders_link":{
		"type":"radio",
		"label":"MOD_ZOOCART_PARAMS_SHOW_ORDERS_LINK",
		"default":"1"
	}

}}';