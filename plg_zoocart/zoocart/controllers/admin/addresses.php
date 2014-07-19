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
	Class: AddressesController
		The controller class for addresses
*/
class AddressesController extends AdminResourceController {

	public function __construct($default = array()) {

		$this->resource_name = 'addresses';

		$this->resource_class = 'Address';

		parent::__construct($default);

		// set base url
		$this->baseurl = $this->component->link(array('controller' => $this->controller), false);

		// get types
		$this->address = $this->app->zoocart->address->getAddressType();
		
		// register tasks
		$this->registerTask('applyelements', 'saveelements');
		$this->registerTask('applyassignelements', 'saveassignelements');
		$this->registerTask('applysubmission', 'savesubmission');

		$this->registerDefaultTask('types');
	}

	public function types() {

		// set toolbar items
		JToolbarHelper::title(JText::_('PLG_ZOOCART_CONFIG_ADDRESS_TYPE'), 'zoolanders');
		if ($this->app->joomla->isVersion('2.5')) {
			$this->app->toolbar->editListX('editElements');
		} else {
			$this->app->toolbar->editList('editElements');
		}

		// get extensions / trigger layout init event
		$this->extensions = $this->app->event->dispatcher->notify($this->app->event->create($this->app, 'layout:init'))->getReturnValue();

		// display view
		$this->getView()->setLayout('addresstypes')->display();
	}

	public function editElements() {

		// disable menu
		$this->app->request->setVar('hidemainmenu', 1);

		// get request vars
		$cid = $this->app->request->get('cid.0', 'string', '');

		// get type
		$this->type = $this->address;

		// set toolbar items
		JToolbarHelper::title(JText::_('PLG_ZOOCART_CONFIG_ADDRESS_TYPE').': '.$this->type->name.' <small><small>[ '.JText::_('PLG_ZLFRAMEWORK_EDIT_ELEMENTS').' ]</small></small>', 'zoolanders');
		$this->app->toolbar->save('saveelements');
		$this->app->toolbar->apply('applyelements');
		$this->app->toolbar->cancel('types', 'Close');

		// sort elements by group
		$this->elements = array();
		foreach ($this->app->element->getAll() as $element) {
			$this->elements[$element->getGroup()][$element->getElementType()] = $element;
		}
		ksort($this->elements);
		foreach ($this->elements as $group => $elements) {
			ksort($elements);
			$this->elements[$group] = $elements;
		}

		// display view
		$breaking = $this->app->zoocart->getBreakingPlugins();
		if(empty($breaking))
		{
			$layout = 'editelements';
		}else{
			$layout = 'warning';
			$this->warning = JText::sprintf('PLG_ZOOCART_ADDRESSES_WARNING', implode(', ', $breaking));
		}

		$this->getView()->setLayout($layout)->display();
	}

	public function addElement() {

		// get request vars
		$element = $this->app->request->getWord('element', 'text');

		// load element
		$this->element = $this->app->element->create($element);
		$this->element->identifier = $this->app->utility->generateUUID();

		// display view
		$this->getView()->setLayout('addElement')->display();
	}

	public function saveElements() {

		// check for request forgeries
		$this->app->zlfw->checkToken();

		// init vars
		$post = $this->app->request->get('post:', 'array', array());
		$cid  = $this->app->request->get('cid.0', 'string', '');

		try {

			// save types elements
			$type = $this->address;
			$type->bindElements($post);

			if ($cid == $this->app->zoocart->getConfig()->get('billing_address_type', 'billing') && !$this->checkAddressType($type)) {
				$this->app->error->raiseNotice(0, JText::_('PLG_ZOOCART_NOTICE_ADDRESS_TYPE_MUST_HAVE_COUNTRY_ELEM'));
				$this->_task = 'applyelements';
				$msg = null;
			} else {
				$this->saveAddressType($type);
				$msg = JText::_('PLG_ZOOCART_CONFIG_ADDRESS_ELEMENTS_SAVED');	
			}

		} catch (AppException $e) {

			$this->app->error->raiseNotice(0, JText::sprintf('PLG_ZOOCART_ERROR_SAVING_ADDRESS_ELEMENTS', $e));
			$this->_task = 'applyelements';
			$msg = null;

		}

		switch ($this->getTask()) {
			case 'applyelements':
				$link = $this->baseurl.'&task=editelements&cid[]='.$type->id;
				break;
			case 'saveelements':
			default:
				$link = $this->baseurl.'&task=types';
				break;
		}

		$this->setRedirect($link, $msg);
	}

	protected function checkAddressType($type) {
		
		$elements = $type->getElements();
		$count = 0;
		foreach($elements as $element) {
			if($element->getElementType() == 'country') {
				$count++;
			}
		}

		if ($count != 1) {
			return false;
		}

		return true;
	}

	protected function saveAddressType($type) {

		// save config file
		if ($file = $type->getConfigFile()) {
			$config_string = (string) $type->config;
			if (!JFile::write($file, $config_string)) {
				throw new TypeException(JText::_('PLG_ZOOCART_ERROR_WRITING_ADDRESS_TYPE_CONFIG_FILE'));
			}
		}
	}

	public function assignElements() {

		// disable menu
		$this->app->request->setVar('hidemainmenu', 1);

		// init vars
		$type				 = $this->app->request->getString('type');
		$this->relative_path = urldecode($this->app->request->getVar('path'));
		$this->path			 = $this->relative_path ? JPATH_ROOT . '/' . $this->relative_path : '';
		$this->layout		 = $this->app->request->getString('layout');

		$dispatcher = JDispatcher::getInstance();
		if (strpos($this->relative_path, 'plugins') === 0) {
			@list($_, $plugin_type, $plugin_name) = explode('/', $this->relative_path);
			JPluginHelper::importPlugin($plugin_type, $plugin_name);
		}
		$dispatcher->trigger('registerZOOEvents');

		// get type
		$this->type = $this->address;

        if ($this->type) {
            // set toolbar items
            JToolbarHelper::title(JText::_('PLG_ZOOCART_CONFIG_ADDRESS_TYPE').': '.$this->type->name.' <small><small>[ '.JText::_('PLG_ZLFRAMEWORK_ASSIGN_ELEMENTS').': '. $this->layout .' ]</small></small>', 'zoolanders');
            $this->app->toolbar->save('saveassignelements');
            $this->app->toolbar->apply('applyassignelements');
            $this->app->toolbar->cancel('types');

            // get renderer
            $renderer = $this->app->renderer->create('address')->addPath($this->path);

            // get positions and config
            $this->config = $renderer->getConfig('address')->get($type.'.'.$this->layout);

            $prefix = 'address.';
            if ($renderer->pathExists('address'.DIRECTORY_SEPARATOR.$type)) {
                $prefix .= $type.'.';
            }
            $this->positions = $renderer->getPositions($prefix.$this->layout);

	        // display view
	        $breaking = $this->app->zoocart->getBreakingPlugins();
	        if(empty($breaking))
	        {
		        $layout = 'assignelements';
	        }else{
		        $layout = 'warning';
		        $this->warning = JText::sprintf('PLG_ZOOCART_ADDRESSES_WARNING', implode(', ', $breaking));
	        }

	        // display view
	        ob_start();
			$this->getView()->setLayout($layout)->display();
			$output = ob_get_contents();
			ob_end_clean();

			// trigger edit event
			$this->app->event->dispatcher->notify($this->app->event->create($this->type, 'addresstype:assignelements', array('html' => &$output)));

			echo $output;


        } else {

			$this->app->error->raiseNotice(0, JText::sprintf('PLG_ZOOCART_ERROR_UNABLE_FIND_ADDRESS_TYPE', $type));
			$this->setRedirect($this->baseurl . '&task=types');

		}
	}

	public function saveAssignElements() {

		// check for request forgeries
		$this->app->zlfw->checkToken();

		// init vars
		$type		   = $this->app->request->getString('type');
		$layout		   = $this->app->request->getString('layout');
		$relative_path = $this->app->request->getVar('path');
		$path		   = $relative_path ? JPATH_ROOT . '/' . urldecode($relative_path) : '';
		$positions	   = $this->app->request->getVar('positions', array(), 'post', 'array');

		// unset unassigned position
		unset($positions['unassigned']);

		// get renderer
		$renderer = $this->app->renderer->create('address')->addPath($path);

		// get config
		$config = $renderer->getConfig('address');

		// save config
		$config->set($type.'.'.$layout, $positions);
		$renderer->saveConfig($config, $path.'/renderer/address/positions.config');

		switch ($this->getTask()) {
			case 'applyassignelements':
				$link  = $this->baseurl.'&task=assignelements&type='.$type.'&layout='.$layout.'&path='.$relative_path;
				break;
			default:
				$link = $this->baseurl.'&task=types';
				break;
		}

		$this->setRedirect($link, JText::_('PLG_ZLFRAMEWORK_ELEMENTS_ASSIGNED'));
	}

	public function assignSubmission() {

		// disable menu
		$this->app->request->setVar('hidemainmenu', 1);

		// init vars
		$type				 = $this->app->request->getString('type');
		$this->relative_path = urldecode($this->app->request->getVar('path'));
		$this->path			 = $this->relative_path ? JPATH_ROOT . '/' . $this->relative_path : '';
		$this->layout		 = $this->app->request->getString('layout');

		$dispatcher = JDispatcher::getInstance();
		if (strpos($this->relative_path, 'plugins') === 0) {
			@list($_, $plugin_type, $plugin_name) = explode('/', $this->relative_path);
			JPluginHelper::importPlugin($plugin_type, $plugin_name);
		}
		$dispatcher->trigger('registerZOOEvents');

		// get type
		$this->type = $this->address;

        if ($this->type) {

			// set toolbar items
			JToolbarHelper::title(JText::_('PLG_ZOOCART_CONFIG_ADDRESS_TYPE').': '.$this->type->name.' <small><small>[ '.JText::_('PLG_ZLFRAMEWORK_ASSIGN_ELEMENTS').': '. $this->layout .' ]</small></small>', 'zoolanders');
			$this->app->toolbar->save('savesubmission');
			$this->app->toolbar->apply('applysubmission');
			$this->app->toolbar->cancel('types');

			// get renderer
            $renderer = $this->app->renderer->create('address')->addPath($this->path);

            // get positions and config
            $this->config = $renderer->getConfig('address')->get($type.'.'.$this->layout);

            $prefix = 'address.';
            if ($renderer->pathExists('address'.DIRECTORY_SEPARATOR.$type)) {
                $prefix .= $type.'.';
            }
            $this->positions = $renderer->getPositions($prefix.$this->layout);

			// display view
			ob_start();
			$this->getView()->setLayout('assignsubmission')->display();
			$output = ob_get_contents();
			ob_end_clean();

			// trigger edit event
			$this->app->event->dispatcher->notify($this->app->event->create($this->type, 'addresstype:assignelements', array('html' => &$output)));

			echo $output;

        } else {

			$this->app->error->raiseNotice(0, JText::sprintf('PLG_ZOOCART_ERROR_UNABLE_FIND_ADDRESS_TYPE', $type));
			$this->setRedirect($this->baseurl . '&task=types');

		}
	}

	public function saveSubmission() {

		// check for request forgeries
		$this->app->zlfw->checkToken();

		// init vars
		$type		   = $this->app->request->getString('type');
		$layout		   = $this->app->request->getString('layout');
		$relative_path = $this->app->request->getVar('path');
		$path		   = $relative_path ? JPATH_ROOT . '/' . urldecode($relative_path) : '';
		$positions	   = $this->app->request->getVar('positions', array(), 'post', 'array');

		// unset unassigned position
		unset($positions['unassigned']);

		// get renderer
		$renderer = $this->app->renderer->create('address')->addPath($path);

		// get config
		$config = $renderer->getConfig('address');

		// save config
		$config->set($type.'.'.$layout, $positions);
		$renderer->saveConfig($config, $path.'/renderer/address/positions.config');

		switch ($this->getTask()) {
			case 'applysubmission':
				$link  = $this->baseurl.'&task=assignsubmission&type='.$type.'&layout='.$layout.'&path='.$relative_path;
				break;
			default:
				$link = $this->baseurl.'&task=types';
				break;
		}

		$this->setRedirect($link, JText::_('PLG_ZLFRAMEWORK_ELEMENTS_ASSIGNED'));
	}


}

/*
	Class: AddressesControllerException
*/
class AddressesControllerException extends AppException {}