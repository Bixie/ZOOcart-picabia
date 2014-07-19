<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

abstract class JShippingDriver extends JPlugin {

	/**
	 * Provided template paths
	 * @var array
	 */
	protected $_path = array();

	public function __construct(&$subject, $config = array()) {
		parent::__construct($subject, $config);

		// Seems like the params sometime are not populated....
        if (isset($config['params']))
        {
            if ($config['params'] instanceof JRegistry) {
                $this->params = $config['params'];
            } else {
                $this->params = new JRegistry($config['params']);
            }
        }

		$this->addTemplatePath(dirname(__FILE__) . '/tmpl');
		$this->addTemplatePath(JPATH_SITE . '/plugins/' . $this->_type . '/' . $this->_name . '/tmpl/');
		// Joomla standart override path
		$this->addTemplatePath(JPATH_SITE . '/templates/' . JFactory::getApplication()->getTemplate() . '/html/plg_' . $this->_type . '_' . $this->_name);

		// load the plugin default and current language
		$jlang = JFactory::getLanguage();
		$plg_name = isset($config['name']) ? $config['name'] : '';

		$jlang->load('plg_zoocart_shipping_'.$plg_name, JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('plg_zoocart_shipping_'.$plg_name, JPATH_ADMINISTRATOR, null, true);

		// set zoo app reference
		$this->app = App::getInstance('zoo');
	}

	/**
	 * Return a list of rates available for this shipping address
	 *
	 * @param  array  $data The data from the order
	 *
	 * @return array       the list of rates
	 */
	public function getShippingRates($data = array()) {
		return array($this->_name => $this->getRates($data));
	}

	/**
	 * This is the method to override in the shipping plugins
	 *
	 * @return array An array of arrays with structure id, name, price
	 * @see 	getShippingRates
	 */
	protected function getRates($data = array()) {
		return array();
	}

	/**
	 * Render provided plugin layout with args
	 *
	 * @param $layout
	 * @param array $args
	 * @return string
	 */
	protected function renderLayout($layout, $args = array()) {

		$file = $this->getLayout($layout);
		if (!$file) {
			return '';
		}

		ob_start();
		extract($args);
		include $file;
		$result = ob_get_contents();
		ob_end_clean();

		return $result;
	}

	/**
	 * Get plugin layout
	 *
	 * @param $layout
	 * @return bool|string
	 */
	protected function getLayout($layout) {

		foreach($this->_path as $path) {
			if (JFile::exists($path . $layout . '.php')) {
				return $path . $layout . '.php';
			}
		}

		return false;
	}

	/**
	 * Add template path to the list of provided paths
	 *
	 * @param $path
	 * @return $this
	 */
	public function addTemplatePath($path) {
		// just force to array
        settype($path, 'array');

        // loop through the path directories
        foreach ($path as $dir)
        {
            // no surrounding spaces allowed!
            $dir = trim($dir);

            // add trailing separators as needed
            if (substr($dir, -1) != DIRECTORY_SEPARATOR) {
                // directory
                $dir .= DIRECTORY_SEPARATOR;
            }

            // Add to the top of the search dirs
            array_unshift($this->_path, $dir);
        }

        return $this;
	}
}