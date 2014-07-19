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
	Class: OrderstatesController
		The controller class for order states
*/
class OrderstatesController extends AdminResourceController {

	public function __construct($default = array()) {

		$this->resource_name = 'orderstates';

		$this->resource_class = 'Orderstate';

		parent::__construct($default);
	}

}

/*
	Class: OrderstatesControllerException
*/
class OrderstatesControllerException extends AppException {}