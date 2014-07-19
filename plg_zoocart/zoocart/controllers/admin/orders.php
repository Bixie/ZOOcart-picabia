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
	Class: OrdersController
		The controller class for orders
*/
class OrdersController extends AdminResourceController {

	public function __construct($default = array()) {

		$this->resource_name = 'orders';

		$this->resource_class = 'Order';

		$this->default_order = '`created_on`';

		$this->default_order_dir = 'DESC';

		parent::__construct($default);
	}

	protected function _getFilters(){

		$state_prefix = $this->option.'.'.$this->resource_name;
		$this->filters['state']		        = $this->app->system->application->getUserStateFromRequest($state_prefix.'filter_state', 'filter_state', '', 'string');
		$this->filters['created_on_from'] 	= $this->app->system->application->getUserStateFromRequest($state_prefix.'created_on_from', 'created_on_from', '', 'string');
		$this->filters['created_on_to']	    = $this->app->system->application->getUserStateFromRequest($state_prefix.'created_on_to', 'created_on_to', '', 'string');

		return $this->filters;
	}

	public function printOrder() {
		$this->app->request->set('tmpl', 'component');
		
		// get request vars
		$cid  = $this->app->request->get('cid.0', 'int');
		$edit = $cid > 0;

		// get item
		if (!$this->resource = $this->table->get($cid)) {
			$this->app->error->raiseError(500, JText::sprintf('PLG_ZOOCART_ERROR_UNABLE_ACCESS_RESOURCE', $cid));
			return;
		}

		// display view
		$this->getView()->setLayout('print')->display();
	}

	protected function getEditToolbar() {
		$cid  = $this->app->request->get('cid.0', 'int');
		if($edit = $cid > 0) {
			$this->app->toolbar->custom('printorder', 'print', 'printorder', 'PLG_ZOOCART_PRINT', false);
		}

		// set toolbar items
		$this->app->toolbar->save();
		$this->app->toolbar->apply();
		$this->app->toolbar->cancel('cancel', $edit ? 'PLG_ZLFRAMEWORK_CLOSE' : 'PLG_ZLFRAMEWORK_CANCEL');
		$this->app->zoo->toolbarHelp();
	}

	public function display($cachable = false, $urlparams = false) {

		$state_prefix       = $this->option.'.'.$this->resource_name;
		$filter_state     	= $this->filters['state'];
		$this->lists['created_on_from'] 	= $this->filters['created_on_from'];
		$this->lists['created_on_to'] 	    = $this->filters['created_on_to'];
		$this->lists['select_state']        = $this->app->zoocart->orderstatesList('filter_state', $filter_state, 'class="inputbox auto-submit"', true);

		parent::display($cachable, $urlparams);
	}

	protected function getResources() {
		// Filters
		$state_prefix       = $this->option.'.'.$this->resource_name;
		$limit		        = $this->joomla->getUserStateFromRequest('global.list.limit', 'limit', $this->joomla->getCfg('list_limit'), 'int');
		$limitstart			= $this->joomla->getUserStateFromRequest($state_prefix.'limitstart', 'limitstart', 0,	'int');

		$tzoffset = $this->app->date->getOffset();

		$conditions = array();
		if ($this->filters['state']) {
			$conditions[] = "`state` = " . (int)$this->filters['state'];
		}
		if ($this->filters['created_on_from']) {
			$conditions[] = "`created_on` >= " . $this->app->database->Quote($this->app->date->create($this->filters['created_on_from'], $tzoffset)->toSql());
		}
		if ($this->filters['created_on_to']) {
			$conditions[] = "`created_on` <= " .  $this->app->database->Quote($this->app->date->create($this->filters['created_on_to'], $tzoffset)->toSql());
		}

		$options = array('order' => $this->_getOrdering(), 'conditions' => implode(' AND ', $conditions));
		$count = $this->table->count($options);

		// in case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$limitstart = $limitstart > $count ? floor($count / $limit) * $limit : $limitstart;

		if ($limit > 0) {
			$options['offset'] = $limitstart;
			$options['limit'] = $limit; 
		}

		return $this->table->all($options);
	}

}

/*
	Class: OrdersControllerException
*/
class OrdersControllerException extends AppException {}