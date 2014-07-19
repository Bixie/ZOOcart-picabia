<?php
/**
* @package		ZL Elements
* @author    	JOOlanders, SL http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

	$app = App::getInstance('zoo');

	return
	'{
		"_default":{
			"type": "checkbox",
			"label": "PLG_ZOOCART_ADDTOCART_FIELD_DEFAULT_LABEL",
			"help": "PLG_ZOOCART_ADDTOCART_FIELD_DEFAULT_DESC"
		},
		"item_type":{
			"type":"select",
			"label":"PLG_ZOOCART_ADDTOCART_FIELD_ITEM_TYPE",
			"help":"PLG_ZOOCART_ADDTOCART_FIELD_ITEM_TYPE_DESC",
			"specific":{
				"options":{
					"PLG_ZOOCART_PRODUCT":"product",
					"PLG_ZOOCART_DIGITAL":"digital",
					"PLG_ZOOCART_SUBSCRIPTION":"subscription"
				}
			}
		}
	}';