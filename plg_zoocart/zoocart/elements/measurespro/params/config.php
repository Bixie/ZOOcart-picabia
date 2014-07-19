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
	'{
		"default":{
			"type":"text",
			"label":"PLG_ZLFRAMEWORK_DEFAULT",
			"help":"PLG_ZLELEMENTS_TEXTS_DEFAULT_TEXT_DESC"
		},
		"units":{
			"type":"text",
			"label":"PLG_ZLFRAMEWORK_UNIT",
			"help":"PLG_ZOOCART_MEASURESPRO_UNIT_DESC"
		}
	}';