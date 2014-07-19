<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class zoocartTaxHelper extends AppHelper {

	/**
	 * Check if this is a valid VIES VAT
	 *
	 * Code credits: Nicholas from AkeebaBackup.com
	 */
	public function isValidVat($country, $vat) {
		
		$vat = trim(strtoupper($vat));
		$country = trim(strtoupper($country));
		
		// cache
		$data = json_decode($this->app->session->get('zoocart_vat_cache'), true);
		if($data && array_key_exists($country.$vat, $data)) {
			return $data[$country.$vat];
		}
		
		if (!class_exists('SoapClient')) {
			$ret = false;
		} else {
			// Using the SOAP API
			// Code credits: Angel Melguiz / KMELWEBDESIGN SLNE (www.kmelwebdesign.com)
			try {
				$sClient = new SoapClient('http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl');
				$params = array('countryCode'=>$country,'vatNumber'=>$vat);
				$response = $sClient->checkVat($params);
				if ($response->valid) {
					$ret = true;
				}else{
					$ret = false;
				}
			} catch(SoapFault $e) {
				$ret = false;
			}
		}

		$data[$country.$vat] = $ret;
		$this->app->session->set('zoocart_vat_cache', json_encode($data));
		
		// Return the result
		return $ret;
	}

	/**
	 * Get the taxes for an Item given the parameters
	 *
	 * @return float The gross price
	 */
	public function getItemTaxes($item, $user_id = null, $address = null)
	{
		if ($el = $this->app->zoocart->price->getPriceElement($item)) {
			$price = $this->app->zoocart->price->getItemNetPrice($item);
			return $this->getTaxes($price, $el->getTaxClass(), $user_id, $address);
		} else {
			$this->app->zoocart->informer->enqueue(JText::_('PLG_ZOOCART_INFORMER_SET_CURRENCIES'));
			return null;
		}
	}

	/**
	 * Get the tax amount for a net price given the parameters
	 *
	 * @return float The tax value
	 */
	public function getTaxes($price, $tax_class_id = null, $user_id = null, $address = null) {
		return $price * $this->getTaxRate($tax_class_id, $user_id, $address) / 100;
	}

	/**
	 * Get the best tax rate given the parameters
	 *
	 * @return float The tax rate
	 */
	public function getTaxRate($tax_class_id = null, $user_id = null, $address = null)
	{
		if ($tax_rate = $this->getTaxRateObject($tax_class_id, $user_id, $address)) {
			return $tax_rate->taxrate;
		}

		return 0;
	}

	/**
	 * Get the best tax rate object associate with the given parameters
	 * Inspired by akeeba subscriptions by Akeeba (thx Nicholas)
	 * 
	 * @param integer $tax_class_id The tax class id
	 * @param integer $user_id The user id to apply the tax from
	 * @param object $address The billing address
	 *
	 * @return object The tax rate
	 */
	public function getTaxRateObject($tax_class_id = null, $user_id = null, $address = null) {
		
		// utility vars
		$tax = $this->app->object->create('Tax');
		$config = $this->app->zoocart->getConfig();

		// Treat guest first: default tax rate + default country
		$tax->tax_class_id = $tax_class_id ? $tax_class_id : $config->get('default_tax_class');
		$tax->country = $config->get('default_country');
		$isVIES = false;

		if ($user_id && !$address) {
			// try to get the user billing address ?? TODO
			
		} else if($address) {

			if (isset($address->country)){
				if($this->app->country->isEU($address->country)){

					if (isset($address->vat)){
						$isVIES = $this->app->zoocart->tax->isValidVat($address->country, $address->vat);
					}
				}

				$tax->country = $address->country;
			}

			$tax->state = isset($address->state) ? $address->state : null;
			$tax->city = isset($address->city) ? $address->city : null;
		}

		// Get the associated tax rules
		$options = "(country = '' OR country = ".$this->app->database->q($tax->country).") AND tax_class_id = ".(int) $tax->tax_class_id;
		$taxrules = $this->app->zoocart->table->taxes->all(array('conditions' => $options));

		$bestTaxRule = (object)array(
			'match'		=> 0,
			'fuzzy'		=> 0,
			'taxrate'	=> 0
		);

		// Best tax rule matching (thanks Nicholas @ Akeeba)
		foreach($taxrules as $rule)
		{
			// For each rule, get the match and fuzziness rating. The best, least fuzzy and last match wins.
			$match = 0;
			$fuzzy = 0;
			
			if(empty($rule->country)) {
				$match++;
				$fuzzy++;
			} elseif($rule->country == $tax->country) {
				$match++;
			}
			
			if(empty($rule->state)) {
				$match++;
				$fuzzy++;
			} elseif($rule->state == $tax->state) {
				$match++;
			}
			
			if(empty($rule->city)) {
				$match++;
				$fuzzy++;
			} elseif(strtolower(trim($rule->city)) == strtolower(trim($tax->city))) {
				$match++;
			}
			
			if( ($rule->vies && $isVIES) || (!$rule->vies && !$isVIES)) {
				$match++;
			}
			
			if(
				($bestTaxRule->match < $match) ||
				( ($bestTaxRule->match == $match) && ($bestTaxRule->fuzzy > $fuzzy) )
			) {
				if($match == 0) continue;
				$bestTaxRule->match = $match;
				$bestTaxRule->fuzzy = $fuzzy;
				$bestTaxRule->taxrate = $rule->taxrate;
				$bestTaxRule->id = $rule->id;
			}
		}
		return $bestTaxRule;
	}

	/**
	 * Check if Zoocart Show price with Tax enabled
	 */
	public function checkTaxEnabled(){
		return $this->app->zoocart->getConfig()->get('show_price_with_tax');
	}
}