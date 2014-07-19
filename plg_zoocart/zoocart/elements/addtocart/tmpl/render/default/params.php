<?php
/**
* @package		ZL Elements
* @author    	JOOlanders, SL http://www.zoolanders.com
* @copyright 	Copyright (C) JOOlanders, SL
* @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

	return 
	'{"fields": {

		"layout_wrapper":{
			"type": "fieldset",
			"fields": {
				"_label":{
					"type":"text",
					"label":"PLG_ZOOCART_ADDTOCART_FIELD_LABEL_LABEL",
					"help":"PLG_ZOOCART_ADDTOCART_FIELD_LABEL_DESC",
					"specific":{
						"placeholder":"PLG_ZOOCART_ADDTOCART_ADD_TO_CART"
					}
				},
				"_complete_label":{
					"type":"text",
					"label":"PLG_ZOOCART_ADDTOCART_FIELD_COMPLETE_LABEL_LABEL",
					"help":"PLG_ZOOCART_ADDTOCART_FIELD_COMPLETE_LABEL_DESC",
					"specific":{
						"placeholder":"PLG_ZOOCART_ADDTOCART_COMPLETE_LABEL"
					}
				},
				"_action":{
					"type":"select",
					"label":"PLG_ZOOCART_ADDTOCART_FIELD_ACTION_LABEL",
					"help":"PLG_ZOOCART_ADDTOCART_FIELD_ACTION_DESC",
					"default":"none",
					"specific":{
						"options":{
							"PLG_ZLFRAMEWORK_NONE":"none",
							"PLG_ZOOCART_ADDTOCART_FIELD_REDIRECT_TO_CART":"cart"
						}
					}
				},
				"_avoid_readd":{
					"type":"radio",
					"label":"PLG_ZOOCART_ADDTOCART_FIELD_AVOID_READD_LABEL",
					"help":"PLG_ZOOCART_ADDTOCART_FIELD_AVOID_READD_DESC",
					"default":"1"
				}
			}
		}

	}}';