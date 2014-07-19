<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/


// no direct access
defined('_JEXEC') or die('Restricted access');

// load config
require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

	// prepare tax class options
	$taxclasses = array();
	foreach ($this->app->zoocart->table->taxclasses->all() as $taxclass) {
		$taxclasses[$taxclass->name] = $taxclass->id;
	}

	// prepare currency options
	$currencies = array();
	foreach ($this->app->zoocart->table->currencies->all() as $currency) {
		$currencies[$currency->name] = $currency->id;
	}

	return 
	'{
		"_default":{
			"type":"text",
			"label":"PLG_ZLFRAMEWORK_DEFAULT",
			"help":"PLG_ZOOCART_ELEMENTS_FIELD_DEFAULT_DESC"
		},
		"_default_tax_class":{
			"type":"select",
			"label":"PLG_ZOOCART_CONFIG_DEFAULT_TAX_CLASS",
			"help":"PLG_ZOOCART_ELEMPRICE_DEFAULT_TAX_CLASS_DESC",
			"specific":{
				"options":' . json_encode($taxclasses) . '
			}
		},
		"_currency":{
			"type":"select",
			"label":"PLG_ZLFRAMEWORK_CURRENCY",
			"help":"PLG_ZOOCART_ELEMPRICE_CURRENCY_DESC",
			"specific":{
				"options":' . json_encode($currencies) . '
			}
		}
	}';