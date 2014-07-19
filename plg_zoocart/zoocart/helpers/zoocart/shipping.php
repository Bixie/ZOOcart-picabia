<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class zoocartShippingHelper extends AppHelper {

	/**
	 * The shipping rates
	 *
	 * @var array The rates
	 */
	protected $_shipping_rates = array();

	/**
	 * Get a list of the ZOOcart Shipping Plugins
	 *
	 * @return array The list
	 */
	public function getShippingPlugins($name = null) {
		return JPluginHelper::getPlugin('zoocart_shipping', $name);
	}

	/**
	 * Get Shipping from HTTP request
	 * 
	 * @return object The Shipping object
	 */
	public function getFromRequest()
	{
		// init vars
		$success = true;
		$response = array('errors' => array(), 'notices' => array(), 'fee' => 0, 'rate' => null);
		$ship_plugin = $this->app->request->getString('shipping_plugin');
		$ship_method = $this->app->request->getString('shipping_method');

		// validate
		if($this->app->zoocart->getConfig()->get('enable_shipping', true) && strlen($ship_plugin) && strlen($ship_method))
		{
			// get shipping rates
			$shipping_rates = $this->getShippingRates(array(
				'items' => $this->app->zoocart->table->cartitems->getByUser(),
				'address' => $this->app->zoocart->address->getFromRequest('shipping')
			));

			foreach($shipping_rates as $plugin => $rates) {
				if($plugin == $ship_plugin) foreach($rates as $rate) {
					if($rate['id'] == $ship_method) {
						$fee = $rate['price'];
						$rate['plugin'] = $plugin;

						$response['success'] = $success;
						$response['fee'] = $fee;
						$response['rate'] = $rate;
						return $response;
					}

				} else {
					$success = false;
				}
			}
		}

		// else
		$response['success'] = false;
		return $response;
	}

	/**
	 * Get plugin by it's name
	 *
	 * @param string Plugin name
	 */
	public function getPluginByName($name)
	{
		$plugins = $this->getShippingPlugins();
		$plg = null;

		if(!empty($plugins))
			foreach($plugins as $plugin){
				if($plugin->name==$name)
				{
					$plg = $plugin;
					break;
				}
			}

		return $plg;
	}

	/**
	 * Checks if order is compatible with provided rate
	 *
	 * @param $itemset
	 * @param $rate
	 *
	 * @return bool
	 */
	protected function _checkOrderRate($itemset, $rate){
		$compatible = true;

		//Count total item count and total price
		if(!empty($itemset))
		{
			$total_count = 0;

			foreach($itemset as $item){
				$total_count += $item->quantity;
			}

			$user_id = $this->app->user->get()->id;

			$total_price = $this->app->zoocart->cart->getNetTotal($user_id);
			$total_weight = $this->app->zoocart->cart->getTotalWeight($user_id);

			$compatible = (0==$rate['price_from'] || $rate['price_from']<=$total_price);
			$compatible = $compatible && (0==$rate['price_to'] || $rate['price_to']>=$total_price);
			$compatible = $compatible && (0==$rate['quantity_from'] || $rate['quantity_from']<=$total_count);
			$compatible = $compatible && (0==$rate['quantity_to'] || $rate['quantity_to']>=$total_count);
			$compatible = $compatible && (0==$rate['weight_from'] || $rate['weight_from']<=$total_weight);
			$compatible = $compatible && (0==$rate['weight_to'] || $rate['weight_to']>=$total_weight);
		}

		return $compatible;
	}

	/**
	 * Checks each item of provided set for compatibility with rate
	 *
	 * @param $itemset
	 * @param $rate
	 *
	 * @return bool
	 */
	protected function _checkItemsRate($itemset, $rate){
		$compatible = true;

		if(!empty($itemset)){
			foreach($itemset as $item){
				$quantity = $item->quantity;
				$price = $item->getNetTotal();
				$weight = $item->getWeight();

				$check = (0==$rate['price_from'] || $rate['price_from']<=$price);
				$check = $check && (0==$rate['price_to'] || $rate['price_to']>=$price);
				$check = $check && (0==$rate['quantity_from'] || $rate['quantity_from']<=$quantity);
				$check = $check && (0==$rate['quantity_to'] || $rate['quantity_to']>=$quantity);
				$check = $check && (0==$rate['weight_from'] || $rate['weight_from']<=$weight);
				$check = $check && (0==$rate['weight_to'] || $rate['weight_to']>=$weight);

				if(!$check){
					$compatible = false;
					break;
				}
			}
		}

		return $compatible;
	}

	/**
	 * Get the Shipping rates
	 *
	 * @param array $data
	 *
	 * @return array The rates list
	 */
	public function getShippingRates($data = array())
	{
		// get all posible rates
		if (!$this->_shipping_rates)
		{
			// init vars
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('zoocart_shipping');
			$plugins = array_merge($dispatcher->trigger('getShippingRates', $data));

			foreach ($plugins as $rates) {
				$this->_shipping_rates = array_merge($this->_shipping_rates, $rates);
			}
		}

		// filter
		$hash = serialize($data);
		$available = array();
		if(!empty($data) && !empty($this->_shipping_rates)){
			foreach($this->_shipping_rates as $plugin => $plugin_rates){
				$available_rates = array();
				foreach($plugin_rates as $rate){
					if($this->canBeShipped($data['address'], $rate)){
						if($rate['type'] == 'order'){
							if($this->_checkOrderRate($data['items'], $rate))
								$available_rates[] = $rate;
						} else if($rate['type'] == 'item'){
							if($this->_checkItemsRate($data['items'], $rate))
								$available_rates[] = $rate;
						} else {
							$available_rates[] = $rate;
						}
					}
				}

				// add filtered rates
				if (!empty($available_rates)) {
					$available[$plugin] = $available_rates;
				}
			}
		}

		// and return
		return $available;
	}

	/**
	 * Just prepare constrains for output like diapason from-to
	 *
	 * @param   int
	 * @param   int
	 *
	 * @return  string
	 */
	public function fromTo($value1=0, $value2=0){
		$string = '';
		if(!empty($value1) || !empty($value2)){
			if(empty($value1)){
				return JText::_('PLG_ZLFRAMEWORK_UP_TO').' '.$value2;
			}
			if(empty($value2)){
				return JText::_('PLG_ZLFRAMEWORK_OVER').' '.$value1;
			}
			$string = sprintf('%s - %s', $value1, $value2);
		}

		return $string;
	}

	/**
	 * Returns true if provided shipping rate is available for provided address
	 *
	 * @param   mixed     $address
	 * @param   mixed     $rate
	 *
	 * @return  bool
	 */
	public function canBeShipped($address, $rate){

		$allowed = !$this->app->zoocart->getConfig()->get('enable_shipping', true);
		$allowed = $allowed || !$this->app->zoocart->getConfig()->get('require_address', false);

		// No need to check further if no address required or shipping rates is disabled
		if(empty($address) || $allowed){
			return true;
		}

		if(is_array($rate)){
			// Check country:
			if(!trim($rate['countries'])){
				return true;
			}
			$countries = explode(',',$rate['countries']);
			$allowed = (empty($countries) || empty($address->country) || in_array($address->country, $countries));
			// Check states:
			$states = explode(',',$rate['states']);
			$allowed = $allowed && (empty($states) || empty($address->state) || in_array($address->state, $states));
			// Check zips:
			$zips = explode(',',$rate['zips']);
			$allowed = $allowed && (empty($zips) || empty($address->zip) || in_array($address->zip, $zips));
			// Check cities:
			$cities = explode(',',$rate['states']);
			$allowed = $allowed && (empty($cities) || empty($address->city) || in_array($address->city, $cities));
			// Check usergroups:
			$groups = explode(',',$rate['user_groups']);
			$user = $this->app->user->get();
			$common_groups = array_intersect($groups, $user->getAuthorisedGroups());
			$allowed = $allowed && (empty($groups) || $this->app->user->isJoomlaAdmin($user) || !empty($common_groups));
		}

		return $allowed;
	}

	/**
	 * Get the Shipping rates Types field
	 *
	 * @param string $name The field control name
	 * @param arrat $selected The selected options
	 *
	 * @return string HTML form field
	 */
	public function shippingRateTypes($name, $selected) {

		// init vars
		$attribs = '';
		$options = array();

		// set options
		$options[] = $this->app->html->_('select.option', 'order', JText::_('PLG_ZOOCART_CONFIG_SHIPPINGRATE_ORDER_TYPE'));
		$options[] = $this->app->html->_('select.option', 'item', JText::_('PLG_ZOOCART_CONFIG_SHIPPINGRATE_ITEM_TYPE'));

		return $this->app->html->_('select.genericlist', $options, $name, $attribs, 'value', 'text', $selected);
	}

	/**
	 * Get weight element from item, using mapping
	 *
	 * @param   Item    $item
	 *
	 * @return  mixed
	 */
	public function getWeightElement($item){

		$element = null;

		if(!empty($item)){
			$elements = $item->getElementsByType('measurespro');
			if(!empty($elements))
			{
				// Take only first element:
				$element = array_shift($elements);
			}
		}

		return $element;
	}

	/**
	 * Get the Item Net weight
	 *
	 * @param object $item The Item
	 *
	 * @return float The Net weight
	 */
	public function getItemNetWeight($item) {
		$weight_element = $this->getWeightElement($item);
		if(empty($weight_element)) {
			return 0;
		} else {
			return floatval($weight_element->get('value'));
		}
	}
}