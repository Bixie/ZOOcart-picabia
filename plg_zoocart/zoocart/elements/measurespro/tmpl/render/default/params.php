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

		"_sublayout":{
			"type": "layout",
			"label": "PLG_ZLFRAMEWORK_SUB_LAYOUT",
			"help": "PLG_ZLFRAMEWORK_SUB_LAYOUT_DESC",
			"default": "_default.php",
			"specific": {
				"path":"elements:'.$element->getElementType().'\/tmpl\/render\/default\/_sublayouts",
				"regex":'.json_encode('^([_A-Za-z0-9]*)\.php$').',
				"minimum_options":"2"
			},
			"childs":{						
				"loadfields": {
					"layout_wrapper":{
						"type": "fieldset",
						"min_count":"1",
						"fields": {

							"subfield": {
								"type":"subfield",
								"path":"elements:'.$element->getElementType().'\/tmpl\/render\/default\/_sublayouts\/{value}\/params.php"
							}

						}
					}
				}
			}
		}

	}}';