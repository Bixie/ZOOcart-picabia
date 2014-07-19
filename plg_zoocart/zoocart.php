<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgSystemZoocart extends JPlugin { 

	// Just to easy the boring work of copy-paste
	protected $ext_name = 'zoocart';
	
	/**
	 * onAfterInitialise handler
	 *
	 * Adds ZOO event listeners
	 *
	 * @access	public
	 * @return null
	 */
	function onAfterInitialise()
	{
		// Get Joomla instances
		$this->joomla = JFactory::getApplication();
		$jlang = JFactory::getLanguage();

		// load default and current language
		$jlang->load('plg_system_zoocart', JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('plg_system_zoocart', JPATH_ADMINISTRATOR, null, true);

		// check dependences
		if (!defined('ZLFW_DEPENDENCIES_CHECK_OK')){
			$this->checkDependencies();
			return; // abort
		}
		
		// Get the ZOO App instance
		$this->app = App::getInstance('zoo');
		
		// register plugin path
		if ( $path = $this->app->path->path( 'root:plugins/system/zoocart/zoocart' ) ) {
			$this->app->path->register($path, 'zoocart');
		}

		// register controllers
		if ( $path = $this->app->path->path( $this->ext_name.':controllers/' ) ) {
			$this->app->loader->register('ResourceController', $this->ext_name.':controllers/resource.php');
			$this->app->loader->register('AdminResourceController', $this->ext_name.':controllers/adminresource.php');
			$this->app->loader->register('SiteResourceController', $this->ext_name.':controllers/siteresource.php');
		}

		if ($this->joomla->isAdmin()) { 
			if ( $path = $this->app->path->path( $this->ext_name.':controllers/admin/' ) ) {
				$this->app->path->register($path, 'controllers');
			}
		} else {
			if ( $path = $this->app->path->path( $this->ext_name.':controllers/site/' ) ) {
				$this->app->path->register($path, 'controllers');
			}
		}

		// register assets
		if ( $path = $this->app->path->path( $this->ext_name.':assets/' ) ) {
			$this->app->path->register($path, 'assets');
		}
		
		// register helper
		if ( $path = $this->app->path->path( $this->ext_name.':helpers/' ) ) {
			$this->app->path->register($path, 'helpers');
		}

		// gorce registering ZOO renderer class before classes to avoid the known "renderer path" ZOO bug
		if ( $path = $this->app->path->path( 'classes:renderer/' ) ) {
			$this->app->path->register($path, 'renderer');
		}

		// register classes
		if ( $path = $this->app->path->path( $this->ext_name.':classes/' ) ) {
			$this->app->path->register($path, 'classes');
		}

		// register payment classes
		if ( $path = $this->app->path->path( $this->ext_name.':payment/' ) ) {
			$this->app->path->register($path, 'payment');
			$this->app->loader->register('JPaymentDriver', 'payment:driver.php');
		}

		// register shipping classes
		if ( $path = $this->app->path->path( $this->ext_name.':shipping/' ) ) {
			$this->app->path->register($path, 'shipping');
			$this->app->loader->register('JShippingDriver', 'shipping:driver.php');
		}

		// register tables
		if ( $path = $this->app->path->path( $this->ext_name.':tables/' ) ) {
			$this->app->path->register($path, 'tables');
			$this->app->loader->register('ZoocartTable', 'tables:zoocart.php');
		}

		// register elements
		if ( $path = $this->app->path->path( $this->ext_name.':elements/' ) ) {
			$this->app->path->register($path, 'elements');
		}
		
		// register fields
		if ( $path = $this->app->path->path( $this->ext_name.':fields/' ) ) {
			$this->app->path->register($path, 'fields');
		}

		// register events
		if ( $path = $this->app->path->path( $this->ext_name.':events/' ) ) {
			$this->app->path->register($path, 'events');
		}

		// register router
		if ($this->joomla->isSite()) {
			$this->app->zl->route->register('zoocart', 'zoocart:router.php');
		}

		// ZOO Events
		$this->app->event->dispatcher->connect('layout:init', array($this, 'initTypeLayouts'));
		$this->app->event->dispatcher->connect('element:configparams', array($this, 'addElementConfig'));
		
		// ZOOcart Events
		$this->app->event->register('OrderEvent');
		$this->app->event->register('PaymentEvent');
		$this->app->event->dispatcher->connect('order:saved', array('OrderEvent', 'saved'));
		$this->app->event->dispatcher->connect('order:deleted', array('OrderEvent', 'deleted'));
		$this->app->event->dispatcher->connect('payment:saved', array('PaymentEvent', 'saved'));
		$this->app->event->dispatcher->connect('element:beforeaddressdisplay', array($this, 'beforeAddressDisplay'));

		// ZOOlanders events
		$this->app->event->dispatcher->connect('zoolanders:menuitems', array($this, 'addConfigMenuItems'));

		// admin events
		if ($this->joomla->isAdmin()) {
			$this->app->event->dispatcher->connect('zoolanders:joomlamenuitems', array($this, 'joomlaMenuItems'));

		// site events
		} else {
			$this->app->event->dispatcher->connect('item:beforedisplay', array($this, 'itemBeforeDisplay'));
		}
	}

	/*
		Function: ItemBeforeDisplay
			Trigered on Item beforedisplay event

		Parameters:
			$event - object
	*/
	public static function itemBeforeDisplay($event)
	{
		$zoo = App::getInstance('zoo');

		// get variation element
		$variation_el = $event->getSubject()->getElementsByType('variations');
		$variation_el = array_shift($variation_el);

		// element check
		if ($variation_el) {

			// only apply if not ajax request and not in cart/order views
			if (!$zoo->zlfw->enviroment->is('site.com_zoolanders.cart site.com_zoolanders.order') 
				&& $zoo->request->get('format', 'string') != 'raw') {
				$variation_el->applyVariations();
			}

			if (!defined('ZOOCART_AFTERDISPLAY_EVENT_INITED')) {
				// set event for Variations
				$zoo->event->dispatcher->connect('element:afterdisplay', 'plgSystemZoocart::elementAfterDisplay');
				define('ZOOCART_AFTERDISPLAY_EVENT_INITED', true);
			}
		}
	}

	/*
		Function: elementAfterDisplay
			Change the element layout after it has been displayed

		Parameters:
			$event - object
	*/
	public static function elementAfterDisplay($event)
	{
		// get params
		$item = $event->getSubject();
		$params = $event['params'];

		// prepare hash
		$hash = md5(serialize(
			$item->id.$params['element'].$params['_layout'].$params['_position'].$params['_index']
		));

		// set hash
		$event['html'] = str_replace('class="element', 'data-zoocart-hash="'.$hash.'" data-zoocart-id="'.$params['element'].'" class="element', $event['html']);
	}

	/**
	 * joomlaMenuItems
	 * 
	 * @param AppEvent $event The event triggered
	 */
	public function joomlaMenuItems($event)
	{
		// set views
		$views = array();
		$views[] = array('name' => 'cart', 'title' => 'ZOOcart Cart', 'path' => 'zoocart:views/site/cart/params.json');
		$views[] = array('name' => 'orders', 'title' => 'ZOOcart Orders', 'path' => 'zoocart:views/site/orders/params.json');
		$views[] = array('name' => 'addresses', 'title' => 'ZOOcart Addresses', 'path' => 'zoocart:views/site/addresses/params.json');
		$views[] = array('name' => 'subscriptions', 'title' => 'ZOOcart Subscriptions', 'path' => 'zoocart:views/site/subscriptions/params.json');

		// add to list
		$list = (array) $event->getReturnValue();
		$list[] = array(
			'name' => 'zoocart',
			'views' => $views
		);

		// save
		$event->setReturnValue($list);
	}

	/**
	 * addConfigMenuItems
	 * 
	 * @param AppEvent $event The event triggered
	 */
	public function addConfigMenuItems($event)
	{
		// init vars
		$tab = $event['tab'];

		// set the Zoocart Tab and it's submenus
		$controller = 'zoocart';
		$link = $this->app->zl->link(array('controller' => $controller));
		$zoocart_subtab = $this->app->object->create('ZlMenuItem', array($controller, 'ZOOcart', $link));

		// Subscriptions:
		$controller = 'subscriptions';
		$link = $this->app->zl->link(array('controller' => $controller));
		$zoocart_subtab->addChild($this->app->object->create('ZlMenuItem', array($controller, JText::_('PLG_ZOOCART_CONFIG_SUBSCRIPTIONS'), $link)));
		
		// Orders
		$controller = 'orders';
		$link = $this->app->zl->link(array('controller' => $controller));
		$zoocart_subtab->addChild($this->app->object->create('ZlMenuItem', array($controller, JText::_('PLG_ZOOCART_ORDERS'), $link)));

		// Discounts
		if ($this->app->zoocart->getConfig()->get('discounts_allowed')) {
			$controller = 'discounts';
			$link = $this->app->zl->link(array('controller' => $controller));
			$zoocart_subtab->addChild($this->app->object->create('ZlMenuItem', array($controller, JText::_('PLG_ZOOCART_DISCOUNTS'), $link)));
		}

		// divider
		$zoocart_subtab->addChild($this->app->object->create('ZlMenuDivider'));

		// Settings
		$controller = 'zoocart';
		$link = $this->app->zl->link(array('controller' => $controller));
		$zoocart_subtab->addChild($this->app->object->create('ZlMenuItem', array($controller, JText::_('PLG_ZLFRAMEWORK_SETTINGS'), $link, array('icon' => 'cogs'))));

		// add the new Tab
		$tab->addChild($zoocart_subtab);
	}

	/**
	 * onUserLogin handler
	 */
	public function onUserLogin($user_data)
	{
		if ($user_data['status'] != 1) {
			return true;
		}

		if (get_class($this->app->table->cartitems) != 'CartitemsTable') {
			return true;
		}

		$user = JUser::getInstance($user_data['username']);

		$cartitems = $this->app->zoocart->table->cartitems->getByUser();

		if(!empty($cartitems))
		{
			// Clear user's existing cart items:
			$this->app->zoocart->table->cartitems->flushByUserId($user->id);

			// Change user_id in the guest's cart items
			foreach($cartitems as $cartitem) {
					$cartitem->user_id = $user->id;
					$this->app->zoocart->table->cartitems->save($cartitem);
			}
		}

		return true;
	}

	/*
		Function: initTypeLayouts
			Callback function for the zoo layouts

		Returns:
			void
	*/
	public function initTypeLayouts($event) 
	{
		$extensions = (array) $event->getReturnValue();
		
		// clean all ZOOfilter layout references
		$newextensions = array();
		foreach ($extensions as $ext) {
			if (strtolower($ext['name']) != 'zoocart') {
				$newextensions[] = $ext;
			}
		}
		
		// add new ones
		$newextensions[] = array('name' => 'ZOOcart', 'path' => $this->app->path->path('zoocart:'), 'type' => 'plugin');
		$newextensions[] = array('name' => 'ZOOcart mapping', 'path' => $this->app->path->path('zoocart:mapping'), 'type' => 'plugin');

		$event->setReturnValue($newextensions);
	}

	/** 
	 * New method for adding params to the element
	 */
	public function addElementConfig($event)
	{
		// apply only on Address view
		if ($this->app->request->getString('controller') != 'addresses') return;
		
		// Custom Params File
		$file = $this->app->path->path( 'zoocart:params/params.xml');
		$xml = simplexml_load_file( $file );

		// Old params
		$params = $event->getReturnValue();

		// add new params from custom params file if type = address
		$element = $event->getSubject();
		$type = $element->getType();

		$params[] = $xml->asXML();

		$event->setReturnValue($params);
	}

	/**
	 * Check the language before displaying the element
	 */
	public function beforeAddressDisplay($event)
	{
		$item 	 = $event->getSubject();
		$element = $event['element'];
		$config  = $element->config;
		$dparams = $this->app->data->create($event['params']);
			
		$label = $dparams->get('language', $config->get('language'));
		$language = JFactory::getLanguage()->get('tag');

		if (@$label[$language]) {
			$params = $event['params'];
			$params['altlabel'] = @$label[$language];
			$event['params'] = $params;
		}
	}

	/**
	 *  checkDependencies
	 */
	public function checkDependencies()
	{
		if($this->joomla->isAdmin())
		{
			// if ZLFW not enabled
			if(!JPluginHelper::isEnabled('system', 'zlframework') || !JComponentHelper::getComponent('com_zoo', true)->enabled) {
				$this->joomla->enqueueMessage(JText::_('PLG_ZOOCART_MISSING_DEPENDENCIES'), 'notice');
			} else {
				// load zoo
				require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');

				// fix plugins ordering
				$this->app = App::getInstance('zoo');
				$this->app->loader->register('ZlfwHelper', 'plugins:system/zlframework/zlframework/helpers/zlfwhelper.php');
				$this->app->zlfw->checkPluginOrder($this->ext_name);
			}
		}
	}

}