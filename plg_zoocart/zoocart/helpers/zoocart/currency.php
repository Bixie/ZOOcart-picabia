<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class zoocartCurrencyHelper extends AppHelper {

	/**
	 * The default currency
	 * 
	 * @var object The currency
	 */
	protected $_default_currency;

	/**
	 * Get the currencies list HTML field
	 * 
	 * @param string $name The field name value
	 * @param array $selected The selected options
	 * 
	 * @return string The HTML field
	 */
	public function currenciesList($name, $selected) {

		$select_options = array('conditions'=>'`published`=1');
		$options = array();

		$currencies = $this->app->zoocart->table->currencies->all($select_options);

		if(!empty($currencies))
		{
			foreach ($currencies as $currency) {
				$options[] = $this->app->html->_('select.option', $currency->id, JText::_($currency->name));
			}
		}

		$attribs = '';

		return $this->app->html->_('select.genericlist', $options, $name, $attribs, 'value', 'text', $selected);
	}

	/**
	 * Format currency
	 * 
	 * @param int $price The price to be formated
	 * @param object $currency The currency object, optional
	 * 
	 * @return float The formated price with provided currency
	 */
	public function format($price, $currency = null)
	{
		// use default currency if none provided
		if (!$currency) {
			$currency = $this->getDefaultCurrency();
		}

		// make sure the price is an float
		settype($price, 'float');

		// if currency set
		if (is_object($currency)) {

			// make sure format is set
			if(empty($currency->format)) $currency->format = '%v%s / -%v%s';

			// set format
			$formats = explode('/', $currency->format);

			// if negative
			if($price < 0 && isset($formats[1])) {
				$format = trim($formats[1]);

			// if positive
			} else {
				$format = trim($formats[0]);
			}

			// get formated price
			$value = number_format(abs($price), $currency->num_decimals_show, $currency->decimal_sep, $currency->thousand_sep);

			// return formated currency
			return str_replace(array('%s', '%v'), array($currency->symbol, $value), $format);

		} else {
			// return basic format
			return number_format($price, 2, ',', '');
		}
	}

	/**
	 * Retrieve the default currency
	 *
	 * @return object The currency object
	 */
	public function getDefaultCurrency() {
		if(!$this->_default_currency) {
			$this->_default_currency = $this->app->zoocart->table->currencies->get($this->app->zoocart->getConfig()->get('default_currency'));
		}

		return $this->_default_currency;
	}
}