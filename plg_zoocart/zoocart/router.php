<?php
/**
* @package		ZL Framework
* @author    	ZOOlanders http://www.zoolanders.com
* @copyright 	Copyright (C) JOOlanders SL
* @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * The zoocart router class
 */
class zlRouterZoocart extends zlRouter {

	/**
	 * Route building
	 */
	public function buildRoute(&$query, &$segments)
	{
		// is ZL menu active?
		$zlmenu = ($menu = $this->app->system->application->getMenu('site')
					and $menu instanceof JMenu
					and isset($query['Itemid'])
					and $item = $menu->getItem($query['Itemid'])
					and @$item->component == 'com_zoolanders');


		/* CART */
		$task = 'display';
		$controller = 'cart';
		if ((((@$query['task'] == $task || @$query['task'] == '') && (@$query['view'] == $controller || @$query['controller'] == $controller )))) {
			$segments[] = $controller;
			unset($query['task']);
			unset($query['view']);
			unset($query['controller']);
		}

		/* SUBSCRIPTIONS */
		$task = 'display';
		$controller = 'subscriptions';
		if ((((@$query['task'] == $task || @$query['task'] == '') && (@$query['view'] == $controller || @$query['controller'] == $controller )))) {

			// set controller only if no Menu
			if (!$zlmenu || (@$item->params->get('extension')->view != $controller)) {
				$segments[] = $controller;
			}

			unset($query['task']);
			unset($query['view']);
			unset($query['controller']);
		}

		$task = 'view';
		$controller = 'subscriptions';
		if ((((@$query['task'] == $task) && (@$query['view'] == $controller || @$query['controller'] == $controller )))) {

			// set controller only if no Menu
			if (!$zlmenu || (@$item->params->get('extension')->view != $controller)) {
				$segments[] = $controller;
			}

			$segments[] = @$query['id'];
			unset($query['task']);
			unset($query['view']);
			unset($query['controller']);
			unset($query['id']);
		}

		/* ORDERS */
		$task = 'display';
		$controller = 'orders';
		if ((((@$query['task'] == $task || @$query['task'] == '') && (@$query['view'] == $controller || @$query['controller'] == $controller )))) {

			// set controller only if no Menu
			if (!$zlmenu || (@$item->params->get('extension')->view != $controller)) {
				$segments[] = $controller;
			}

			unset($query['task']);
			unset($query['view']);
			unset($query['controller']);
		}

		$task = 'view';
		$controller = 'orders';
		if ((((@$query['task'] == $task) && (@$query['view'] == $controller || @$query['controller'] == $controller )))) {

			// set controller only if no Menu
			if (!$zlmenu || (@$item->params->get('extension')->view != $controller)) {
				$segments[] = $controller;
			}

			$segments[] = @$query['id'];
			unset($query['task']);
			unset($query['view']);
			unset($query['controller']);
			unset($query['id']);
		}

		$task = 'pay';
		$controller = 'orders';
		if ((((@$query['task'] == $task) && (@$query['view'] == $controller || @$query['controller'] == $controller )))) {

			// set controller only if no Menu
			if (!$zlmenu || (@$item->params->get('extension')->view != $controller)) {
				$segments[] = $controller;
			}

			$segments[] = $task;
			$segments[] = @$query['id'];
			unset($query['task']);
			unset($query['view']);
			unset($query['controller']);
			unset($query['id']);
		}


		/* ADDRESSES */
		$task = 'display';
		$controller = 'addresses';
		if ((((@$query['task'] == $task  || @$query['task'] == '') && (@$query['view'] == $controller || @$query['controller'] == $controller )))) {

			// set controller only if no Menu
			if (!$zlmenu || (@$item->params->get('extension')->view != $controller)) {
				$segments[] = $controller;
			}

			unset($query['task']);
			unset($query['view']);
			unset($query['controller']);
		}

		$task = 'edit';
		$controller = 'addresses';
		if ((((@$query['task'] == $task) && (@$query['view'] == $controller || @$query['controller'] == $controller )))) {

			// set controller only if no Menu
			if (!$zlmenu || (@$item->params->get('extension')->view != $controller)) {
				$segments[] = $controller;
			}
			
			$segments[] = isset($query['id']) ? $query['id'] : 'new';
			unset($query['task']);
			unset($query['view']);
			unset($query['controller']);
			unset($query['id']);
		}	
	}
	
	/**
	 * Route parsing
	 */
	public function parseRoute(&$segments, &$vars)
	{
		$count = count($segments);


		/* CART */
		$task = 'display';
		$controller = 'cart';
		if ($count == 1 && $segments[0] == $controller) {
			$vars['task']   = $task;
			$vars['view']	= $controller;
			$vars['controller'] = $controller;
		}


		/* ORDERS */
		$task = 'display';
		$controller = 'orders';
		if ($count == 1 && $segments[0] == $controller) {
			$vars['task']   = $task;
			$vars['view']	= $controller;
			$vars['controller'] = $controller;
		}

		$task = 'view';
		$controller = 'orders';
		if ($count == 2 && $segments[0] == $controller) {
			$vars['task']   = $task;
			$vars['view']	= $controller;
			$vars['controller'] = $controller;
			$vars['id'] = $segments[1];
		}

		$task = 'pay';
		$controller = 'orders';
		if (($count == 3 || $count == 2) && $segments[0] == $controller && $segments[1] == $task) {
			$vars['task']   = $task;
			$vars['view']	= $controller;
			$vars['controller'] = $controller;
			// Id can be passed via POST too
			if($count == 3) {
				$vars['id'] = $segments[2];
			}
		}


		/* ADDRESSES */
		$task = 'display';
		$controller = 'addresses';
		if ($count == 1 && $segments[0] == $controller) {
			$vars['task']   = $task;
			$vars['view']	= $controller;
			$vars['controller'] = $controller;
		}

		$task = 'edit';
		$controller = 'addresses';
		if ($count == 2 && $segments[0] == $controller) {
			$vars['task']   = $task;
			$vars['view']	= $controller;
			$vars['controller'] = $controller;
			if ($segments[1] != 'new') {
				$vars['id'] = $segments[1];
			}
		}

		/* SUBSCRIPTIONS */
		$task = 'display';
		$controller = 'subscriptions';
		if ($count == 1 && $segments[0] == $controller) {
			$vars['task']   = $task;
			$vars['view']	= $controller;
			$vars['controller'] = $controller;
		}

		$task = 'view';
		$controller = 'subscriptions';
		if ($count == 2 && $segments[0] == $controller) {
			$vars['task']   = $task;
			$vars['view']	= $controller;
			$vars['controller'] = $controller;
			$vars['id'] = $segments[1];
		}

		$task = 'pay';
		$controller = 'subscriptions';
		if (($count == 3 || $count == 2) && $segments[0] == $controller && $segments[1] == $task) {
			$vars['task']   = $task;
			$vars['view']	= $controller;
			$vars['controller'] = $controller;
			// Id can be passed via POST too
			if($count == 3) {
				$vars['id'] = $segments[2];
			}
		}

		// try to retrieve vars from menu item
		if (empty($vars)) {
			if ($menu_item = $this->app->menu->getActive()) {
				$vars['controller'] = @$menu_item->params->get('extension')->view;

				switch ($vars['controller']) {
					case 'addresses':
						if ($count == 1) {
							$vars['task'] = 'edit';
							if(!$segments[0] == 'new') {
								$vars['id'] = (int) $segments[0];
							}
						}
						break;

					case 'orders':
						if ($count == 1) {
							$vars['task'] = 'view';
							$vars['id'] = (int) $segments[0];
						}
						if ($count == 2) {
							$vars['task'] = 'pay';
							$vars['id'] = (int) $segments[1];
						}
						break;
				}
			}
		}
	}

	/**
	 * Get route to cart
	 *
	 * @param boolean $route If it should be run through JRoute::_()
	 *
	 * @return string The route
	 */
	public function cart($route = true)
	{
		// set key
		$key = $this->_active_menu_item_id.'-cart-'.$route;

		// check for cached value
		if ($this->_cache && $cached = $this->_cache->get($key)) {
			return $cached;
		}

		// Priority 1: direct link to cart
		if ($menu_item = $this->_find('cart')) {
			$link = $this->getLinkBase().'&Itemid='.$menu_item->id;
		} else {

			// build item link
			$link = $this->getLinkBase().'&controller=cart';
		}

		// process routing
		if ($route) {
			$link = JRoute::_($link, false);
		}

		// store link for future lookups
		if ($key && $this->_cache) {
			$this->_cache->set($key, $link)->save();
		}

		return $link;
	}



	/**
	 * Get route to addresses
	 *
	 * @param boolean $route If it should be run through JRoute::_()
	 *
	 * @return string The route
	 */
	public function addresses($route = true)
	{
		// set key
		$key = $this->_active_menu_item_id.'-addresses-'.$route;

		// check for cached value
		if ($this->_cache && $cached = $this->_cache->get($key)) {
			return $cached;
		}

		// Priority 1: direct link to addresses
		if ($menu_item = $this->_find('addresses')) {
			$link = $this->getLinkBase().'&Itemid='.$menu_item->id;
		} else {

			// build item link
			$link = $this->getLinkBase().'&controller=addresses';
		}

		// process routing
		if ($route) {
			$link = JRoute::_($link);
		}

		// store link for future lookups
		if ($key && $this->_cache) {
			$this->_cache->set($key, $link)->save();
		}

		return $link;
	}

	/**
	 * Get route to orders
	 *
	 * @param boolean $route If it should be run through JRoute::_()
	 *
	 * @return string The route
	 */
	public function orders($route = true)
	{
		// set key
		$key = $this->_active_menu_item_id.'-orders-'.$route;

		// check for cached value
		if ($this->_cache && $cached = $this->_cache->get($key)) {
			return $cached;
		}

		// Priority 1: direct link to orders
		if ($menu_item = $this->_find('orders')) {
			$link = $this->getLinkBase().'&Itemid='.$menu_item->id;
		} else {

			// build item link
			$link = $this->getLinkBase().'&controller=orders';
		}

		// process routing
		if ($route) {
			$link = JRoute::_($link);
		}

		// store link for future lookups
		if ($key && $this->_cache) {
			$this->_cache->set($key, $link)->save();
		}

		return $link;
	}

	/**
	 * Get route to subscriptions
	 *
	 * @param boolean $route If it should be run through JRoute::_()
	 *
	 * @return string The route
	 */
	public function subscriptions($route = true)
	{
		// set key
		$key = $this->_active_menu_item_id.'-subscriptions-'.$route;

		// check for cached value
		if ($this->_cache && $cached = $this->_cache->get($key)) {
			return $cached;
		}

		// Priority 1: direct link to subscriptions
		if ($menu_item = $this->_find('subscriptions')) {
			$link = $this->getLinkBase().'&Itemid='.$menu_item->id;
		} else {

			// build item link
			$link = $this->getLinkBase().'&controller=subscriptions';
		}

		// process routing
		if ($route) {
			$link = JRoute::_($link);
		}

		// store link for future lookups
		if ($key && $this->_cache) {
			$this->_cache->set($key, $link)->save();
		}

		return $link;
	}

	/**
	 * Finds a menu item by its type and id in the menu items
	 *
	 * @param string $type
	 * @param string $id
	 *
	 * @return stdClass menu item
	 */
	protected function _find($type, $id=0)
	{
		if ($this->_menu_items == null) {
			$menu_items = $this->app->system->application->getMenu('site')->getItems('component_id', JComponentHelper::getComponent('com_zoolanders')->id);
			$menu_items = $menu_items ? $menu_items : array();

			$this->_menu_items = array_fill_keys(array('cart'), array());
			foreach ($menu_items as $menu_item) {
				switch (@$menu_item->params->get('extension')->view) {
					case 'cart':
						$this->_menu_items['cart'][$id] = $menu_item;
						break;
					case 'addresses':
						$this->_menu_items['addresses'][$id] = $menu_item;
						break;
					case 'orders':
						$this->_menu_items['orders'][$id] = $menu_item;
						break;
					case 'subscriptions':
						$this->_menu_items['subscriptions'][$id] = $menu_item;
						break;
				}
			}
		}

		return @$this->_menu_items[$type][$id];
	}
}