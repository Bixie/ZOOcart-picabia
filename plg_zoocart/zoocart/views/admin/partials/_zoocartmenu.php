<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// render menu
$menu = $this->app->menu->get('nav')
	->addFilter(array('ZooCartMenuFilter', 'activeFilter'))
	->addFilter(array('ZooCartMenuFilter', 'nameFilter'))
	->addFilter(array('ZooCartMenuFilter', 'versionFilter'))
	->applyFilter();

echo '<div id="nav"><div class="bar"></div>'.$menu->render(array('AppMenuDecorator', 'index')).'</div>';

/*
	Class: ZooMenuFilter
		Filter for menu class.
*/
class ZooCartMenuFilter {

	public static function activeFilter(AppMenuItem $item) {

		// init vars
		$id          = '';
		$app		 = App::getInstance('zoo');
		$application = $app->zoo->getApplication();
		$controller  = $app->request->getWord('controller');
		$task 		 = $app->request->getWord('task');
		$classes     = array();

		if (!empty($application)) {
			$id = $application->id.'-'.($controller?$controller:'taxes');
		}

		// save current class attribute
		$class = $item->getAttribute('class');
		if (!empty($class)) {
			$classes[] = $class;
		}
		
		// set active class
		if ($item->getId() == $id || $item->hasChild($id, true)) {
			$classes[] = 'active';
		}

		// replace the old class attribute
		$item->setAttribute('class', implode(' ', $classes));
	}

	public static function nameFilter(AppMenuItem $item) {
		if ($item->getId() != 'new' && $item->getId() != 'manager') {
			$item->setName(htmlspecialchars($item->getName(), ENT_QUOTES, 'UTF-8'));
		}
	}

	public static function versionFilter(AppMenuItem $item) {
		$app = App::getInstance('zoo');

		if ($item->getId() == 'manager') {
			if (($xml = simplexml_load_file($app->path->path('component.admin:zoo.xml'))) && ((string) $xml->name == 'ZOO') || (string) $xml->name == 'com_zoo') {
				$item->setAttribute('data-zooversion', current($xml->xpath('//version')));
			}
		}
	}

}