<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Object that represents an Order Instance
 */
class Order {

    /**
     * Primary key of the application record
     *
     * @var int
     */
	public $id;
	
	public $user_id;
	
	public $net;
	
	public $tax;

	public $shipping;

	public $payment;

	public $discount = 0;

	public $discount_info = null;

	public $shipping_tax;

	public $payment_tax;

	public $subtotal;

	public $weight;

	public $tax_total;

	public $total;

	public $currency;

	public $params;

	public $created_on;

	public $modified_on;

	public $notes;

	public $ip;

	public $shipping_address;

	public $billing_address;

	public $payment_method;

	public $shipping_method;

	public $state;

	protected $_state = null;

	protected $_billing_address = null;

	protected $_shipping_address = null;	

	protected $_items = null;

	protected $_payments = null;

	/**
 	 * Class Constructor
 	 */
	public function __construct() {

		// init vars
		$this->app = App::getInstance('zoo');
	}

	/**
	 * Get the subtotal
	 *
	 * @return Int The subtotal
	 */
	public function getSubtotal($force = false) {

		if (!$this->subtotal || $force) {
			$this->subtotal = $this->net + $this->shipping + $this->payment - $this->discount;
		}

		return $this->subtotal;
	}

	/**
	 * getPaymentTax
	 */
	public function getPaymentTax($force = false) {

		if (!$this->payment_tax || $force) {
			$this->payment_tax = $this->app->zoocart->tax->getTaxes($this->payment, null, null, $this->getTaxAddress());
		}

		return $this->payment_tax;
	}

	/**
	 * getShippingTax
	 */
	public function getShippingTax($force = false) {

		if (!$this->shipping_tax || $force) {
			$this->shipping_tax = $this->app->zoocart->tax->getTaxes($this->shipping, null, null, $this->getTaxAddress());
		}

		return $this->shipping_tax;
	}

	/**
	 * getTaxTotal
	 */
	public function getTaxTotal($force = false) {

		if (!$this->tax_total || $force) {
				$this->tax_total = $this->tax + $this->getShippingTax($force) + $this->getPaymentTax($force);
		}

		return $this->tax_total;
	}

	/**
	 * getTotal
	 */
	public function getTotal($force = false) {

		if (!$this->total || $force) {
			$this->total = $this->getSubtotal() + $this->getTaxTotal($force) - $this->discount;
		}

		return $this->total;
	}

	/**
	 * getCurrency
	 */
	public function getCurrency() {
		return json_decode($this->currency);
	}

	/**
	 * getState
	 */
	public function getState() {

		if(!$this->_state) {
			$this->_state = $this->app->zoocart->table->orderstates->get($this->state);
		}
		return $this->_state;
	}

	/**
	 * getBillingAddress
	 */
	public function getBillingAddress() {

		if(!$this->_billing_address) {
			$this->_billing_address = $this->_getAddress('billing');
		}
		return $this->_billing_address;
	}

	/**
	 * getShippingAddress
	 */
	public function getShippingAddress() {

		if(!$this->_shipping_address) {
			$this->_shipping_address = $this->_getAddress('shipping');
		}
		return $this->_shipping_address;
	}

	protected function _getAddress($type = 'billing') {

		$address = $this->app->object->create('Address');
		$address->type = $type;
		$key = $type . '_address';
		$data = json_decode($this->$key, true);

		$address->bind($data, true);

		return $address;
	}

	/**
	 * Some
	 */
	public function getTaxAddress() {

		$billing_type = $this->app->zoocart->getConfig()->get('billing_address_type', 'billing');

		if (strtolower($billing_type == 'shipping')) {
			return $this->getShippingAddress();
		}

		return $this->getBillingAddress();
	}

	/**
	 * setItems
	 */
	public function setItems($items) {
		$this->_items = $items;
	}

	/**
	 * getItems
	 */
	public function getItems() {

		if (!$this->_items) {
			$this->_items = $this->app->zoocart->table->orderitems->getByOrder($this->id);
		}

		return $this->_items;
	}

	/**
	 * getPayments
	 */
	public function getPayments() {

		if (!$this->_payments) {
			$this->_payments = $this->app->zoocart->table->payments->getByOrder($this->id);
		}

		return $this->_payments;
	}

	/**
	 * getShippingMethod
	 */
	public function getShippingMethod() {

		$shipping = json_decode($this->shipping_method, true);
		return $shipping ? $shipping : array();
	}

	/**
	 * Compare order properties with another object
	 *
	 * @param \Order
	 * @return array    // Array of differences
	 */
	public function compareWith(Order $subject){
		$diff = array();

		//Properties that should be compared:
		$watch = array(
			'user_id',
		    'shipping_address',
		    'billing_address',
		    'shipping_method',
		    'payment_method',
		    'discount',
		    'notes',
		    'state'
		);

		$own = get_object_vars($this);
		$external = get_object_vars($subject);
		foreach($own as $key=>$value){
			if(!in_array($key, $watch)){
				continue;
			}
			if($value!==$external[$key]){
				$diff[$key] = $external[$key];
			}
		}

		return $diff;
	}

}