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
	Class: SiteResourceController
		The site resource controller
*/
class SiteResourceController extends ResourceController {

	protected $_scope = 'site';

	public function __construct($default = array()) {
		parent::__construct($default);

		// load zlux assets
		$this->app->zlfw->zlux->loadMainAssets(true);
	}

	public function getView($name = '', $type = '', $prefix = '', $config = array()) {

		$view = parent::getView($name, $type, $prefix, $config);

		return $view;
	}

	public function view() {

		// get request vars
		$cid  = $this->app->request->get('id', 'int');

		if (!$this->resource = $this->table->get($cid)) {
			$this->app->error->raiseError(404, JText::sprintf('PLG_ZOOCART_ERROR_REQUESTED_ITEM_NOT_FOUND', $cid));
			return;
		}

		$this->template = $this->application->getTemplate();
		$this->renderer = $this->app->renderer->create('item')->addPath(array($this->app->path->path('component.site:'), $this->template->getPath(), $this->app->path->path('zoocart:')));

		// display view
		$this->getView()->setLayout('view')->display();
	}

	public function display($cachable = false, $urlparams = false) 
	{
		parent::display($cachable, $urlparams );

		// get resources
		$this->resources = array_merge($this->getResources());

		// Display
		$this->getView()->setLayout('default')->display();
	}

}