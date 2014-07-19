<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

abstract class JPaymentDriver extends JPlugin {

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

		$jlang->load('plg_zoocart_payment_'.$plg_name, JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('plg_zoocart_payment_'.$plg_name, JPATH_ADMINISTRATOR, null, true);

		// set zoo app reference
		$this->app = App::getInstance('zoo');
	}

	/**
	 * Returns the payment fee based on the order. Default = 0
	 *
	 * @param array $data The data containing the order values
	 * @return int The net total of the fee to apply. Taxes WILL be applied to this on if necessary
	 */
	public function getPaymentFee($data = array()) {
		return 0;
	}


	/**
	 * Plugin even triggered when the payment plugin notifies for the transaction
	 *
	 * @param  array  $data The data received
	 *
	 * @return array(
	 *         		status: 0 => failed, 1 => success, -1 => pending
	 *         		transaction_id
	 *         		order_id,
	 *         		total,
	 *         		redirect: false (default) or internal url
	 *         )
	 */
	public function callback($data = array()) {

	}

	public function cancel($data = array()) {
		return $this->renderLayout('cancel', $this->getCancelData($data));
	}

	public function message($data = array()) {
		return $this->renderLayout('message', $this->getMessageData($data));
	}

	public function render($data = array()) {
		return $this->renderLayout('default', $this->getRenderData($data));
	}

	protected function getRenderData($data = array()) {
		$data['params'] = $this->params;
		return $data;
	}

	protected function getMessageData($data = array()) {
		$data['params'] = $this->params;
		return $data;
	}

	protected function getCancelData($data = array()) {
		$data['params'] = $this->params;
		return $data;
	}

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

	protected function getLayout($layout) {

		foreach($this->_path as $path) {
			if (JFile::exists($path . $layout . '.php')) {
				return $path . $layout . '.php';
			}
		}

		return false;
	}

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