<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/*
	Class: ShippingratesController
		The controller class for shipping rates
*/
class ShippingratesController extends AdminResourceController {

	public function __construct($default = array()) {

		$this->resource_name = 'shippingrates';

		$this->resource_class = 'Shippingrate';

		parent::__construct($default);
	}

	protected function beforeEditDisplay() {
		// published select
		$this->lists['select_enabled'] = $this->app->html->booleanlist('published', '', $this->resource->published);
	}

}

/*
	Class: ShippingratesControllerException
*/
class ShippingratesControllerException extends AppException {}