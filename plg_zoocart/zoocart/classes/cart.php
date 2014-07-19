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
 * Class Cart
 * Cart object model implementation
 */
class Cart {

	/**
	 * @var object application
	 */
	public $app = null;

	/**
	 * @var object Dedicated resource table
	 */
	protected $table = null;

	/**
	 * @var array Cart items
	 */
	protected $items = array();

	/**
	 * @var int Cart net total
	 */
	protected $net_total = 0;

	/**
	 * @var int Total tax sum
	 */
	protected $taxes = 0;

	/**
	 * @var int Total weight
	 */
	protected $weight = 0;

	/**
	 * @var int Fee
	 */
	protected $fee = 0;

	/**
	 * @var int Discount
	 */
	protected $discount = 0;

	/**
	 * @var null Tax address
	 */
	protected $tax_address = null;

	/**
	 * Class constructor
	 */
	public function __construct(){
		// init vars
		$this->app = App::getInstance('zoo');
		$this->table = $this->app->zoocart->table->cartitems;
		$this->settings = $this->app->zoocart->getConfig();

		// clean cart on init
		$this->clean();

		return $this;
	}

	/**
	 * Set tax address
	 *
	 * @param   \Address Tax address
	 */
	public function setTaxAddress($address){

		$this->tax_address = $address;
	}

	/**
	 * Get tax address
	 *
	 * @return  \Address Tax address
	 */
	public function getTaxAddress(){

		return $this->tax_address;
	}

	/**
	 * Set fee amount
	 *
	 * @param  float    Fee amount
	 */
	public function setFee($fee){
		$this->fee = (float)$fee;
	}

	/**
	 * Set discount amount
	 *
	 * @param  float	Discount amount
	 */
	public function setDiscount($discount){
		$this->discount = (float)$discount;
	}

	/**
	 * Get cart items
	 *
	 * @return array
	 */
	public function getItems(){

		if(empty($this->items)){
			$this->items = $this->table->getByUser();
		}

		return $this->items;
	}

	/**
	 * Get summary cart items net total
	 *
	 * @return  float   Net Subtotal
	 */
	public function getNetTotal(){

		if(empty($this->net_total)){
			$items = $this->getItems();

			if(!empty($items)){
				foreach($items as $item){
					$this->net_total += $item->getNetTotal();
				}
			}
		}

		return $this->net_total;
	}

	/**
	 * Get summary cart items subtotal
	 *
	 * @return  float   Subtotal
	 */
	public function getSubTotal(){

		return	$this->getNetTotal() + $this->fee;
	}

	/**
	 * Get total
	 *
	 * @return  float Total sum
	 */
	public function getTotal(){

		return $this->getSubTotal() + $this->getTaxes() - $this->discount;
	}

	/**
	 * Get total tax sum
	 *
	 * @return  float Total tax sum
	 */
	public function getTaxes(){

		if(empty($this->taxes))
		{
			$items = $this->getItems();

			if(!empty($items)){
				foreach ($items as $item) {
					$this->taxes += $item->getItemTax($this->tax_address);
				}
			}

			// Apply fees
			$this->taxes += $this->app->zoocart->tax->getTaxes($this->fee, null, null, $this->tax_address);
		}

	 	return $this->taxes;
	}

	/**
	 * Get total weight of the cart items
	 *
	 * @return  float Total weight
	 */
	public function getTotalWeight(){

		if(empty($this->weight)){
			$items = $this->getItems();

			if(!empty($items)){
				foreach($items as $item){
					$this->weight += $item->getWeight();
				}
			}
		}

		return $this->weight;
	}

	/**
	 * Clear cart content
	 *
	 * @return bool
	 */
	public function clear(){

		foreach ($this->getItems() as $item) {
			$this->table->delete($item);
		}

		return true;
	}

	/**
	 * Clean cart from unvalid content
	 *
	 * @return bool
	 */
	public function clean(){

		foreach ($this->getItems() as $item) if(!$item->getItem()) {
			if($this->remove($item)) {
				unset($this->items[$item->id]);
			}
		}

		return true;
	}

	/**
	 * Add an item to the cart
	 *
	 * @param   object  \Item
	 * @param   int     Quantity
	 * @param   array   Variations
	 * @param   array   Options
	 *
	 * @return  object  Contains information about operation result
	 */
	public function add($item, $quantity, $variations = array(), $options = array()){

		$result = new stdClass();
		$result->success = false;
		$result->cartitem = null;
		$result->error = 0; // Returning error codes: 0 - no error, 1 - avoid re-add, 2 - quantity check failed

		$check_quantity = empty($options['check_quantity']) ? false : $options['check_quantity'];
		$avoid_readd = empty($options['avoid_readd']) ? false : $options['avoid_readd'];

		$cartitem = $this->table->getByItem(array('item_id' => $item->id, 'variations' => $variations));

		// abort if in cart and avoid_readd
		if ($cartitem) {
			if($avoid_readd)
			{
				$result->error = 1;
				return $result;
			}

			// If item already in the cart just update quantity
			$cartitem->quantity += $quantity;
		}else{
			// Or creae new cart item
			$cartitem = $this->app->object->create('Cartitem');

			// Detect subscription
			$subs_elements = $this->app->zoocart->subscription->getSubscriptionElements($item);

			if(array_key_exists('duration',$subs_elements) && ($duration = $subs_elements['duration'])){
				$value = $duration->get('value');
				if($value)
				{
					$subs = new stdClass();
					$subs->duration = $value;
					$subs_data = json_encode($subs);
				}
			}

			// prepare cartitem object
			$cartitem->subscription = empty($subs_data) ? '' : $subs_data;
			$cartitem->variations = $variations;
			$cartitem->item_id = $item->id;
			$cartitem->user_id = $this->app->user->get()->id;
			$cartitem->session_id = $this->app->session->getId();
			$cartitem->quantity = $quantity;
		}

		// check stock
		if(!$check_quantity || $this->app->zoocart->quantity->checkQuantity($cartitem->getItem()))
		{
			$this->table->save($cartitem);

			$result->success = true;
			$result->cartitem = $cartitem;
		}else{
			$result->error = 2;
		}

		return $result;
	}

	/**
	 * Remove item from the cart
	 *
	 * @param   object  \Item or \Cartitem
	 *
	 * @return mixed
	 */
	public function remove($item){

		$success = false;

		// Detect cart item
		if(is_a($item, 'Item'))
		{
			$cartitem = $this->table->getByItem($item);
		}elseif(is_a($item, 'Cartitem')){
			$cartitem = $item;
		}else{
			return $success;
		}

		if(!empty($cartitem)){
			$success = $this->table->delete($cartitem);
		}

		return $success;
	}

	/**
	 * Create order based on current cart state
	 *
	 * @param   array   Other (non-cart related) data array
	 *
	 * @return  mixed   Order object or null if not succeeded
	 */
	public function createOrder($data){

		$order = $this->app->object->create('Order');

		$order->id = 0;
		$order->user_id = $this->app->user->get()->id;

		$order->billing_address = json_encode($data['billing']['address']);
		$order->shipping_address = json_encode($data['shipping']['address']);

		// Set tax address
		$tax_address = $this->app->zoocart->getConfig()->get('billing_address_type') == 'billing' ? $data['billing']['address'] : $data['shipping']['address'];
		$this->setTaxAddress($tax_address);

		// Set shipping data
		$order->shipping = $data['shipping']['fee'];
		$order->shipping_tax = $this->app->zoocart->tax->getTaxes($data['shipping']['fee'], null, null, $this->tax_address);
		$order->shipping_method = json_encode($data['shipping']['rate']);

		// Set payment data
		$order->payment = $data['payment']['fee'];
		$order->payment_tax = $this->app->zoocart->tax->getTaxes($data['payment']['fee'], null, null, $this->tax_address);
		$order->payment_method = $data['payment']['method'];

		// Set another related data
		$order->notes = $data['notes'];
		$order->state = $this->app->zoocart->getConfig()->get('new_orderstate', 1);
		$order->discount = $data['discount']['sum'];
		$order->discount_info = empty($data['discount']['info']) ? '' : json_encode($data['discount']['info']);
		$currency = $this->app->zoocart->currency->getDefaultCurrency();
		$order->currency = json_encode($currency);
		$order->weight = $this->getTotalWeight();

		// Set amounts
		$order->net = $this->getNetTotal();
		$order->tax = $this->getTaxes();
		$order->subtotal = $this->getSubTotal();
		$order->tax_total = $this->getTaxes();
		$order->total = $this->getTotal();

		// Set order items
		$orderitems = array();
		$cartitems = $this->getItems();
		foreach ($cartitems as $cartitem) {

			$item = $cartitem->getItem();
			$orderitem = $this->app->object->create('Orderitem');
			$orderitem->bind($cartitem);

			$orderitem->id = 0;
			$orderitem->name = $item->name;
			$orderitem->elements = $item->elements;
			$orderitem->quantity = $cartitem->quantity;
			$orderitem->weight = $cartitem->getWeight();
			$orderitem->price = $orderitem->getPrice();
			$orderitem->tax = $cartitem->getItemTax($this->tax_address);
			$orderitem->total = $cartitem->getItemTotal($this->tax_address);

			$orderitems[] = $orderitem;
		}

		// Set order items:
		$order->setItems($orderitems);

		// save order
		$this->app->zoocart->table->orders->save($order);

		return $order->id ? $order : null;
	}

	/**
	 * Validate the cart
	 *
	 * @param   array   Non-cart related data container
	 *
	 * @return  array   Result of the validation with updated data
	 */
	public function validate($data)
	{
		// init vars
		$success = true;
		$response = array('errors' => array(), 'notices' => array());

		// validate cartitems
		$cartitems = $this->getItems();
		$check_quant = $this->settings->get('check_quantities', false);

		foreach ($cartitems as $id => $cartitem)
		{
			// set variations
			if (isset($data['items'][$id]['variations'])) {
				$cartitem->variations = json_encode($data['items'][$id]['variations']);
			}

			// set quantities
			if (isset($data['items'][$id]['quantity']))
			{
				$quantity = (int)$data['items'][$id]['quantity'];

				// check stock
				if($check_quant) {
					// calculate stock
					$stock = $this->app->zoocart->quantity->checkQuantity($cartitem->getItem()) + $cartitem->quantity;

					if ($quantity > $stock) {
						// adjust quantity to available stock
						$quantity = $stock;
						// and notice
						$response['notices'][] = JText::sprintf('PLG_ZOOCART_STOCK_QUANTITY_UPDATED_TO_LIMIT', $stock);
					} 
				}

				$cartitem->quantity = $quantity;
			}

			// save changes
			$this->table->save($cartitem);

			// get new price
			if($this->app->zoocart->tax->checkTaxEnabled()) {
				$price = $this->app->zoocart->price->getItemGrossPrice($cartitem->getItem(), null, $this->tax_address);
			} else {
				$price = $this->app->zoocart->price->getItemNetPrice($cartitem->getItem());
			}

			// set totals
			$response['items'][$id] = array( $cartitem->quantity, $price, ($price * $cartitem->quantity), $cartitem->getWeight() );
		}

		// validate discounts
		if($this->settings->get('discounts_allowed'))
		{
			// apply total discount
			if(isset($data['discounts']['sum']))
				$this->setDiscount($data['discounts']['sum']);
		}

		// set totals
		$response['totals'] = array(
			'discounts' => $this->discount,
			'subtotal' => $this->getSubtotal(),
			'taxes' => $this->getTaxes(),
			'total' => $this->getTotal()
		);

		// set end return results
		$response['success'] = $success;

		return $response;
	}
}