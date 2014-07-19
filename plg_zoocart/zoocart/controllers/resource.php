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
	Class: ResourceController
		The resource controller
*/
abstract class ResourceController extends AppController {

	protected $_scope = 'admin';

	protected $resource_name = '';

	protected $resource_class = '';

	protected $default_order = '`id`';

	protected $default_order_dir = 'ASC';

	protected $filters = array();

	/**
	 * Auto-populate filter states
	 */
	protected function _getFilters(){

		// Catch filtering values from request and set filters state here.
		return null;
	}

	public function __construct($default = array()) {
		parent::__construct($default);

		// set table
		$table_name = $this->resource_name;

		$this->table = $table_name?$this->app->table->$table_name:null;

		// save the active comp ref
		$this->component = $this->app->component->active;

		// Catch filters state
		$this->_getFilters();
	}

	/**
	 * Get ordering options os object
	 *
	 * @param   bool If true - return as object
	 * @return  mixed
	 */
	protected function _getOrdering($object = false){
		$ordering = new stdClass();

		// consider with ordering options:
		$order = $this->app->request->getString('filter_order','');
		$order_direction = $this->app->request->getString('filter_order_Dir','ASC');

		if($order){
			$ordering->order = $order;
			$ordering->direction   = $order_direction;
		}else{
			$ordering->order = $this->default_order;
			$ordering->direction   = $this->default_order_dir;
		}

		return $object?$ordering:$ordering->order.' '.$ordering->direction;
	}

	public function getView($name = '', $type = '', $prefix = '', $config = array()) {

		$view = parent::getView($name, $type, $prefix, $config);

		$view_name = $view->getName()?$view->getName():$this->default_view;

		// Default templates and partials paths
		$view->addTemplatePath($this->app->path->path('zoocart:views/'.$this->_scope.'/partials'));
		if($path = $this->app->path->path('zoocart:views/'.$this->_scope.'/'.$view_name.'/tmpl')) {
			$view->addTemplatePath($path);
		}

		// Look for templates and partials overrides
		$cur_template = JFactory::getApplication()->getTemplate();
		$subfolder = ($this->_scope=='admin')?'administrator/':'';
		if($path = $this->app->path->path('root:'.$subfolder.'templates/'.$cur_template.'/html/plg_system_zoocart/'.$view_name)) {
			$view->addTemplatePath($path);
		}

		if($path = $this->app->path->path('root:'.$subfolder.'templates/'.$cur_template.'/html/plg_system_zoocart/partials')) {
			$view->addTemplatePath($path);
		}

		return $view;
	}

	public function display($cachable = false, $urlparams = false) 
	{
		// get database
		$this->db = $this->app->database;

		// get Joomla application
		$this->joomla = $this->app->system->application;

		// get filters
		$per_page	= $this->joomla->getCfg('list_limit');
		$page		= $this->app->request->getInt('page', 1);

		$count = $this->table->count();
		
		// set pagination
		$this->pagination = $this->app->pagination->create($count, $page, $per_page, 'page', 'uikit');
		$this->pagination->setShowAll($per_page == 0);
		$this->pagination_link = $this->component->link(array('controller' => $this->controller, 'task' => 'display'),false);

		// get resources
		$this->resources = array_merge($this->getResources());
	}

	public function view() {

		// get request vars
		$cid  = $this->app->request->get('id', 'int');
		
		if (!$this->resource = $this->table->get($cid)) {
			$this->app->error->raiseError(500, JText::sprintf('PLG_ZOOCART_ERROR_UNABLE_ACCESS_RESOURCE', $cid));
			return;
		}
		
		$this->renderer = $this->app->renderer->create('item')->addPath(array($this->app->path->path('component.site:'), $this->app->path->path('zoocart:')));
		
		// display view
		$this->getView()->setLayout('view')->display();
	}

	protected function getResources()
	{
		// Filters
		$state_prefix	= $this->option.'.'.$this->resource_name;
		$limit		        = $this->joomla->getUserStateFromRequest('global.list.limit', 'limit', $this->joomla->getCfg('list_limit'), 'int');
		$limitstart			= $this->joomla->getUserStateFromRequest($state_prefix.'limitstart', 'limitstart', 0,	'int');

		$options = $limit > 0 ? array('offset' => $limitstart, 'limit' => $limit, 'order' => $this->_getOrdering()) : array('order' => $this->_getOrdering());

		if(property_exists($this->resource_class, 'user_id')) {
			$options['conditions'] = 'user_id = ' . (int) $this->app->user->get()->id;
		}

		return $this->table->all($options);
	}
}

/*
	Class: ResourceControllerException
*/
class ResourceControllerException extends AppException {}