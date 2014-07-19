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

// check for Type object
if ($element->getType()) {

	// get elements type list with pricepro/quantity/variations/addtocart exclusion
	$elements = array();
	foreach ($element->getType()->getElements() as $el) {
		$type = $el->getElementType();
		if($type != 'pricepro' && $type != 'quantity' && $type != 'variations' && $type != 'addtocart') {
			$elements[] = $type;
		}
	}

	// JSON
	return
	'{
		"variations_wrapper": {
			"type":"wrapper",
			"fields": {

				"elements":{
					"type":"elements",
					"label":"PLG_ZLFRAMEWORK_ELEMENTS",
					"help":"PLG_ZOOCART_VARIATIONS_CONFIG_EL_DESC",
					"specific": {
						"apps":"'.$element->getType()->getApplication()->getGroup().'",
						"types":"'.$element->getType()->id.'",
						"elements":"'.implode(' ', $elements).'",
						"multi":"true"
					}
				},

				'./* attributes */'
				"attributes": {
					"type":"attributes",
					"label":"PLG_ZLFRAMEWORK_ATTRIBUTES"
				}
			}
		}
	}';


// new instance
} else {

	// JSON
	return
	'{
		"_id":{
			"type":"info",
			"specific":{
				"text":"PLG_ZLFRAMEWORK_SAVE_TYPE"
			}
		}
	}';

}