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
	Class: ZoocartController
		The zoocart controller class
*/
class ZoocartController extends AdminResourceController {

	public function __construct($default = array()) {

		parent::__construct($default);

		$this->registerDefaultTask('settings');
	}

	public function settings() {

		// set toolbar items
		JToolbarHelper::title(JText::_('PLG_ZLFRAMEWORK_SETTINGS'), 'zoolanders');
		$this->app->toolbar->apply('saveSettings', JText::_('PLG_ZLFRAMEWORK_APPLY'));
		$this->app->toolbar->cancel();
		$this->active_tab = $this->app->request->getCmd('open','');

		// get extensions / trigger layout init event
		$this->extensions = $this->app->event->dispatcher->notify($this->app->event->create($this->app, 'layout:init'))->getReturnValue();

		// display view
		$this->getView()->setLayout('settings')->display();
	}

	public function saveSettings() {

		// check for request forgeries
		$this->app->zlfw->checkToken();
		$link = $this->component->link(array('controller' => $this->controller, 'task' => 'settings'),false);

		// get the zoocart params
		$params = $this->app->request->get('zoocart', 'array');

		// save to com_zoolanders
		$this->app->zl->setConfig('zoocart', $params);

		// set result msg and redirect
		$msg = JText::_('PLG_ZOOCART_GENERAL_CONFIG_SAVED');
		$this->setRedirect($link, $msg);
	}
}

/*
	Class: ZoocartControllerException
*/
class ZoocartControllerException extends AppException {}