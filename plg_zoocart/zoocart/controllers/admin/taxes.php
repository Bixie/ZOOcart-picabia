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
	Class: TaxesController
		The controller class for taxes
*/
class TaxesController extends AdminResourceController {

	/**
	 * @var string Default ordering field
	 */
	protected $default_order = '`ordering`';

	public function __construct($default = array()) {

		$this->resource_name = 'taxes';

		$this->resource_class = 'Tax';

		parent::__construct($default);
	}

	protected function beforeEditDisplay() {
		// published select
		$this->lists['select_enabled'] = $this->app->html->booleanlist('published', '', $this->resource->published);

		// published searchable
		$this->lists['select_vies'] = $this->app->html->booleanlist('vies', '', $this->resource->vies);
	}

}

/*
	Class: TaxesControllerException
*/
class TaxesControllerException extends AppException {}