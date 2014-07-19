<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class zoocartPriceHelper extends AppHelper {

	private $_prices = array('net' => array(), 'gross' => array());

	/**
	 * Get the price with taxes based on a given user
	 *
	 * @param float $price The net price
	 * @param integer $tax_class_id The tax class id
	 * @param integer $user_id The user id
	 * @param object $address The address
	 * 
	 * @return float The gross price
	 */
	public function getGrossPrice($price, $tax_class_id = null, $user_id = null, $address = null) {
		return $price + $this->app->zoocart->tax->getTaxes($price, $tax_class_id, $user_id, $address);
	}

	/**
	 * Get the Price element object
	 * 
	 * @param object $item The Item containing the Element
	 * 
	 * @return object The Price Element
	 */
	public function getPriceElement($item)
	{
		// init vars
		$currency = $this->app->zoocart->currency->getDefaultCurrency();

		// check currency setting
		if (!$currency) {
			$this->app->zoocart->informer->enqueue(JText::_('PLG_ZOOCART_INFORMER_SET_DEFAULT_CURRENCY'));
			return null;
		}

		// find the price element associated to current currency
		foreach ($item->getElementsByType('pricepro') as $element) {
			if($element->getCurrency() && $element->getCurrency()->code == $currency->code) {
				return $element;
			}
		}

		return null;
	}

	/**
	 * Get the Item Net price
	 * 
	 * @param object $item The Item
	 * 
	 * @return float The Net price
	 */
	public function getItemNetPrice($item)
	{
		// set hash for caching
		$hash = md5(serialize(array($item->id, @$item->variations)));
		if (!array_key_exists($hash, $this->_prices['net']))
		{
			if ($el = $this->getPriceElement($item)) {
				$this->_prices['net'][$hash] = $el->getNetPrice();
			} else {
				$this->app->zoocart->informer->enqueue(JText::_('PLG_ZOOCART_INFORMER_SET_CURRENCIES'));
				$this->_prices['net'][$hash] = null;
			}
		}

		return $this->_prices['net'][$hash];
	}

	/**
	 * Get the Item Gross price. Wrapping the getGrossPrice() function
	 * 
	 * @param object $item The Item
	 * 
	 * @return float The Item gross price
	 */
	public function getItemGrossPrice($item, $user_id = null, $address = null)
	{
		// set hash for caching
		$hash = md5(serialize(array($item->id, @$item->variations)));
		if (!array_key_exists($hash, $this->_prices['gross']))
		{
			if ($el = $this->getPriceElement($item)) {
				$this->_prices['gross'][$hash] = $this->getGrossPrice($el->getNetPrice(), $el->getTaxClass(), $user_id, $address);
			} else {
				$this->app->zoocart->informer->enqueue(JText::_('PLG_ZOOCART_INFORMER_SET_CURRENCIES'));
				$this->_prices['gross'][$hash] = null;
			}
		}

		return $this->_prices['gross'][$hash];
	}
}