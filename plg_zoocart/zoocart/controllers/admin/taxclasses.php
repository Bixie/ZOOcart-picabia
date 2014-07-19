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
	Class: TaxclassesController
		The controller class for tax classes
*/
class TaxclassesController extends AdminResourceController {

	public function __construct($default = array()) {

		$this->resource_name = 'taxclasses';

		$this->resource_class = 'Taxclass';

		parent::__construct($default);
	}

}

/*
	Class: TaxclassesControllerException
*/
class TaxclassesControllerException extends AppException {}