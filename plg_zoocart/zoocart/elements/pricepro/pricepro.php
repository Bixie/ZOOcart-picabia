<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class ElementPricePro extends ElementRepeatablePro implements iRepeatSubmittable {

	/**
	 * The element currency
	 *
	 * @var object
	 */
	public $currency = null;

	/**
	 * Class Constructor
	 */
	public function __construct() {

		// call parent constructor
		parent::__construct();

		$this->registerCallback('getGrossPriceAjax');
	}

	/*
		Function: getNetPrice
			Get the net price

		Returns:
			float - the net price
	*/
	public function getNetPrice() {
		return $this->get('value');
	}

	/*
		Function: getGrossPrice
			Get the gross price for the current element instance price and tax
			excluding other tax arguments as user and address

		Returns:
			float - the gross price
	*/
	public function getGrossPrice($app_id = null, $user_id = null, $address = null) {
		return $this->app->zoocart->price->getGrossPrice($this->getNetPrice(), $this->getTaxClass(), $user_id, $address);
	}

	/*
		Function: getGrossPriceAjax
			Get the gross price for ajax calls

		Returns:
			Integer - the gross price
	*/
	public function getGrossPriceAjax() {
		$net_price = $this->app->request->get('net_price', '');
		return json_encode(array('gross' => $this->getGrossPrice($net_price)));
	}


	/*
		Function: getTaxClass
			Get the tax class

		Returns:
			int - the tax class id
	*/
	public function getTaxClass() {
		return $this->get('tax_class');
	}
	
	/*
		Function: hasValue
			Checks if the element's value is set.

		Parameters:
			$params - AppData render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		
		foreach($this as $self) {
			return $this->_hasValue($params);
		}
		return false;
	}
	
	/*
		Function: _hasValue
			Checks if the repeatables element's value is set.

		Parameters:
			$params - AppData render parameter

		Returns:
			Boolean - true, on success
	*/
	protected function _hasValue($params = array()) {
		$value = $this->get('value', $this->config->find('specific._default'));
		return !empty($value);
	}

	/*
		Function: _getSearchData
			Get repeatable elements search data.
					
		Returns:
			String - Search data
	*/	
	protected function _getSearchData() {
		return $this->get('value');
	}

	/*
		Function: _edit
			Renders the repeatable edit form field.

		Returns:
			String - html
	*/
	protected function _edit() {

		if ($layout = $this->getLayout('edit/_edit.php')) {
			return $this->renderLayout($layout,
				array(
					'default' => $this->config->find('specific._default'),
					'value' => $this->get('value')
				)
			);
		}
	}
	
	/*
		Function: loadAssets
			Load elements css/js assets.

		Returns:
			Void
	*/
	public function loadAssets() {
		
		parent::loadAssets();
		$this->app->zlfw->zlux->loadMainAssets(true);
		$this->app->document->addScript('elements:pricepro/assets/js/price.js');
		$this->app->document->addScript('zlfw:assets/js/accounting.min.js');
		$this->app->document->addStylesheet('elements:pricepro/assets/css/price-admin.css');
		return $this;
	}

	/*
		Function: render
			Renders the element.

		Parameters:
			$params - AppData render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {
		$params = $this->app->data->create($params);

		// render layout
		if ($layout = $this->getLayout('render/' . $params->find('layout._layout', 'default.php'))) {
			return $this->renderLayout($layout, compact('params'));
		}
	}
	
	/*
		Function: formatNumber
			Formats the price

		Parameters:
			$price - the raw price number

		Returns:
			String - the formated number
	*/
	public function formatNumber($price) {
		return $this->app->zoocart->currency->format($price, $this->getCurrency());
	}
	
	/*
		Function: _renderSubmission
			Renders the element in submission.

		Parameters:
			$params - submission parameters

		Returns:
			String - html
	*/
	public function _renderSubmission($params = array()) {
		return $this->edit();
	}
	
	/*
		Function: _renderRepeatable
			Renders the repeatable

		Returns:
			String - output
	*/
	protected function _renderRepeatable($function, $params = array()) {
		return $this->renderLayout($this->app->path->path("elements:pricepro/tmpl/edit/edit.php"), compact('function', 'params'));
	}
	
	/*
		Function: getConfigForm
			Get parameter form object to render input form.

		Returns:
			Parameter Object
	*/
	public function getConfigForm() {
		
		$form = parent::getConfigForm();
		$form->addElementPath($this->app->path->path( 'zoocart:fields'));

		return $form;
	}

	/*
		Function: getCurrency
			Get the currency

		Returns:
			Currency Object
	*/
	public function getCurrency() {

		if (!$this->currency) {
			$this->currency = $this->app->zoocart->table->currencies->get($this->config->find('specific._currency'));

			// check currency, use default if fails
			$this->currency = $this->currency ? $this->currency : $this->app->zoocart->currency->getDefaultCurrency();
		}

		return $this->currency;
	}

}