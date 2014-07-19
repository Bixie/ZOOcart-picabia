<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/*
	Class: CartController
		The controller class for the cart
*/
class CartController extends SiteResourceController {

	/**
	 * @var null Cart object
	 */
	protected $cart = null;

	public function __construct($default = array()) {

		$this->resource_name = 'cartitems';

		$this->resource_class = 'Cartitem';

		parent::__construct($default);

		// set shorcuts
		$this->zconfig = $this->app->zoocart->getConfig();
		$this->req = $this->app->request;
		$this->cart = $this->app->object->create('Cart');
	}

	/**
	 * display
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// init vars
		$this->db = $this->app->database;
		$this->user = JFactory::getUser();

		// set renderer
		$this->renderer = $this->app->renderer->create('item')->addPath(array($this->app->path->path('component.site:'), $this->app->path->path('zoocart:')));

		// addresses
		if($this->app->zoocart->getConfig()->get('require_address', 1))
		{
			// set renderers
			$this->address_renderer = $this->app->renderer->create('address')->addPath(array($this->app->path->path('component.site:'), $this->app->path->path('zoocart:')));
			$this->address_submission = $this->app->renderer->create('addresssubmission')->addPath(array($this->app->path->path('component.site:'), $this->app->path->path('zoocart:')));

			// get and organize addresses in group
			$addresses = $this->app->zoocart->table->addresses->getByUser($this->user->id);
			$this->addresses['billing'] = array();
			$this->addresses['shipping'] = array();

			foreach( $addresses as $address ) {
				if ($address->type == 'billing') {
					$this->addresses['billing'][] = $address;
				} else {
					$this->addresses['shipping'][] = $address;
				}
			}
		}

		// Shipping methods
		$this->shipping_rates = $this->app->zoocart->shipping->getShippingRates(array(
			'items' => $this->cart->getItems(),
			'address' => $this->app->zoocart->table->addresses->getDefaultAddress($this->user->id, 'shipping')
		));
		
		// Display
		$view = $this->getView();
		$view->setLayout('default')->display();
	}

	/**
	 * login
	 */
	public function login()
	{
		// check for request forgeries
		$this->app->zlfw->checkToken();

		// init vars
		$success = false;
		$response = array('errors' => array(), 'notices' => array());

		// workaround to get the login response message if failed. Works only in j3, discarted for now
		// JEventDispatcher::getInstance()->register('onUserLoginFailure', array($this, 'onUserLoginFailure'));

		// get credentials
		$username = trim($this->app->request->getString('username'));
		$password = trim($this->app->request->getString('password'));
		$remember = $this->app->request->getBool('remember');

		// login
		if (strlen($username) && strlen($password)) {
			$success = JFactory::getApplication()->login(array('username' => $username, 'password' => $password, 'remember' => $remember));
		}

		if(!$success) {
			// there is no reliable way to know the error message, we assume the most common one
			$response['errors'][] = JText::_('JLIB_LOGIN_AUTHENTICATE');
		}

		$response['success'] = $success;
		echo json_encode($response);
		return;
	}

	/**
	 * register
	 */
	public function register()
	{
		// check for request forgeries
		$this->app->zlfw->checkToken();

		// init vars
		$success = false;
		$response = array('errors' => array(), 'notices' => array());

		$data = array();
		$data['username'] = trim($this->app->request->getString('username'));
		$data['password'] = trim($this->app->request->getString('password'));
		$data['password2'] = trim($this->app->request->getString('password2'));
		$data['email'] = trim($this->app->request->getString('email'));
		$data['name'] = trim($this->app->request->getString('name'));

		$errors = array();
		if (!strlen($data['username'])) {
			$errors[] = JText::_('PLG_ZLFRAMEWORK_USERNAME_REQUIRED');
		}
		if (!strlen($data['password'])){
			$errors[] = JText::_('PLG_ZLFRAMEWORK_PASSWORD_REQUIRED');
		}
		if (!strlen($data['email'])) {
			$errors[] = JText::_('PLG_ZLFRAMEWORK_EMAIL_REQUIRED');
		}
		if ($data['password'] != $data['password2']) {
			$errors[] = JText::_('PLG_ZLFRAMEWORK_PASSWORDS_MUST_MATCH');
		}

		if (!count($errors)) {
			$password = $data['password'];

			$user = new JUser();
			$user->id = 0;
			$user->bind($data);
			$user->groups = array(JComponentHelper::getParams('com_users')->get('new_usertype', 2));

			$success = $user->save();

			if(!$success) {
				$errors = $user->getErrors();
			} else {
				JFactory::getApplication()->login(array('username' => $data['username'], 'password' => $password));
			}
		}

		// set and return results
		$response['success'] = $success;
		$response['errors'] = $errors;

		echo json_encode($response);
		return;
	}
	
	/**
	 * addToCart
	 */
	public function addToCart()
	{
		// check for request forgeries
		$this->app->zlfw->checkToken();

		// init vars
		$response = array('errors' => array(), 'notices' => array());

		$item_id = $this->app->request->get('item_id', 'int');
		$quantity = $this->app->request->get('quantity', 'float');
		$module = $this->app->request->get('module', 'bool');
		$avoid_readd = $this->app->request->get('avoid_readd', 'bool');
		$check_quantities = $this->zconfig->get('check_quantities', false);

		$variations = $this->app->request->get('variations', 'array', array());
		$variations = !empty($variations) ? json_encode($variations) : '';

		if(!$item_id) {
			return;
		}

		$item = $this->app->table->item->get($item_id);

		$result = $this->cart->add($item, $quantity, $variations, compact('check_quantities', 'avoid_readd'));

		// get updated stock
		if($check_quantities) {
			$response['stock'] = $this->app->zoocart->quantity->checkQuantity($item);
		}

		// get module updated html
		if($module) $response['module'] = $this->_getCartModule();

		// set current cart quantity
		$response['incart_quant'] = empty($result->cartitem) ? 0 :$result->cartitem->quantity;

		// return response
		$response['success'] = $result->success;

		echo json_encode($response);

		return;
	}

	/**
	 * Renew subs action
	 */
	public function renew()
	{
		// check for request forgeries
		$this->app->zlfw->checkToken(true);

		$sub_id = $this->app->request->get('subid', 'int');
		$subs = $this->app->zoocart->table->subscriptions->get($sub_id);

		$avoid_readd = $this->app->request->get('avoid_readd', 'bool');
		$check_quantities = $this->zconfig->get('check_quantities', false);

		$renewal_data = $this->app->zoocart->subscription->getRenewalData($subs);

		if(empty($renewal_data)){
			$this->setRedirect($this->app->zl->link(array('controller'=>'cart'), false), JText::_('PLG_ZOOCART_ERROR_UNABLE_RENEW_SUBS'), 'error');
			return;
		}

		$item_id = $renewal_data->item_id;

		if(!$item_id) {
			$this->setRedirect($this->app->zl->link(array('controller'=>'cart'), false), JText::sprintf('PLG_ZOOCART_ERROR_UNABLE_ACCESS_RESOURCE', $item_id), 'error');
			return;
		}

		$item = $this->app->table->item->get($item_id);

		$this->cart->add($item, $renewal_data->quantity, $renewal_data->variations, compact('check_quantities', 'avoid_readd'));

		$this->display();
	}

	/**
	 * validateCheckout
	 */
	public function validateAndPlaceOrder()
	{
		// check for request forgeries
		$this->app->zlfw->checkToken();

		// init vars
		$success = true;
		$response = array('errors' => array(), 'notices' => array());

		// user validation
		if (!$this->app->user->get()->id) {
			$response['success'] = false;
			$response['errors'][] = JText::_('PLG_ZLFRAMEWORK_PLEASE_LOGIN_FIRST');

			echo json_encode($response);
			return;
		}

		// payment/shipping validation
		$payment = $this->app->request->get('payment_method', 'string');
		if(!strlen($payment))
		{
			$success = false;
			$response['errors'][] = JText::_('PLG_ZOOCART_ERROR_SELECT_PAYMENT_METHOD');
		}

		if($this->zconfig->get('enable_shipping', true)) {
			$shipping = $this->app->request->getString('shipping_plugin');
			$method = $this->app->request->getString('shipping_method');
			if(!strlen($shipping) || !strlen($method))
			{
				$success = false;
				$response['errors'][] = JText::_('PLG_ZOOCART_ERROR_SELECT_SHIPPING_METHOD');
			}
		}

		if(!$success) {
			$response['success'] = false;
			echo json_encode($response);
			return;
		}

		// Cart validation
		$cart_valid = $this->_validateCart();
		$success = $success && $cart_valid['success'];
		$response = array_merge_recursive($response, $cart_valid);

		if(!$success) {
			$response['success'] = false;
			echo json_encode($response);
			return;
		}

		// Place order
		$order_valid = $this->_placeOrder();
		$success = $success && $order_valid['success'];
		$response = array_merge_recursive($response, $order_valid);

		// set and return results
		$response['success'] = $success;
		echo json_encode($response);

		return;
	}

	/**
	 * validateCart
	 */
	public function validateCart()
	{
		// check for request forgeries
		$this->app->zlfw->checkToken();

		// validate
		$response = $this->_validateCart();

		// if success
		if($response['success'])
		{
			$id = $this->app->request->get('remove_item', 'int', false);
			$module = $this->app->request->get('module', 'bool', false);

			// if remove item requested and module present
			if($id && $module) {
				// get module updated html
				$response['module'] = $this->_getCartModule();
			}
		}

		echo json_encode($response);
		return;
	}

	/**
	 * Validate crt method
	 *
	 * @return array
	 */
	protected function _validateCart()
	{
		// init vars
		$success = true;
		$response = array('errors' => array(), 'notices' => array());
		$fee = 0;

		// remove item request
		if($id = $this->app->request->get('remove_item', 'int'))
		{
			// remove
			$success = $this->cart->remove($this->table->get($id));

			if(!$success)
			{
				$response['errors'][] = JText::_('PLG_ZOOCART_CART_ERROR_ITEM_DELETE');
				$response['success'] = false;

				echo json_encode($response);
				jexit();
			}
		}

		// validate addresses
		if($this->zconfig->get('require_address'))
		{
			// get addressess
			$bill_address = $this->app->zoocart->address->getFromRequest('billing');
			$ship_address = $this->app->zoocart->address->getFromRequest('shipping');

			if($bill_address)
			{
				// validate
				$bill_addr_val = $bill_address->validate();

				// report
				$success = $success && $bill_addr_val['success'];
				$response = array_merge_recursive($response, $bill_addr_val);

				// set if EU
				$response['address']['billing']['country']['isEU'] = isset($bill_address->country) ? $this->app->country->isEU($bill_address->country) : false;

				// validate vat
				if(isset($bill_address->country) && isset($bill_address->vat)) {
					$response['address']['billing']['vat']['vies'] = $this->app->zoocart->tax->isValidVat($bill_address->country, $bill_address->vat);
				}
			}

			if($ship_address)
			{
				// validate
				$ship_addr_val = $ship_address->validate();

				$success = $success && $ship_addr_val['success'];
				$response = array_merge_recursive($response, $ship_addr_val);
			}

			// set tax address
			$tax_address = $this->zconfig->get('billing_address_type', 'billing') == 'billing' ? $bill_address : $ship_address;
		}else{
			$tax_address = null;
		}

		$items = $this->app->request->get('items', 'array', null);

		// get cart total weight
		$response['weight'] = $this->cart->getTotalWeight();

		// add payment fee
		$payment = $this->app->zoocart->payment->getFromRequest();
		if($payment['success']) {
			$fee += $payment['fee'];
			$response['payment_fee'] = $payment['fee'];
		}

		// validate shipping
		if($this->zconfig->get('enable_shipping'))
		{
			// add shipping fee
			$shipping = $this->app->zoocart->shipping->getFromRequest();
			if($shipping['success']) {
				$fee += $shipping['fee'];
				$response['shipping_fee'] = $shipping['fee'];
			}

			// get updated shipping rates
			$filtered_rates = $this->app->zoocart->shipping->getShippingRates(array(
				                                                                  'address' => isset($ship_address) ? $ship_address : $this->app->zoocart->address->getFromRequest('shipping'),
				                                                                  'items' => $this->cart->getItems()
			                                                                  ));
			$response['shipping_rates'] = $this->app->zlfw->renderLayout($this->app->path->path('zoocart:views/site/cart/tmpl/_checkout_shipping.php'), array('shipping_rates' => $filtered_rates));
		}

		// validate subtotal
		$this->cart->setFee($fee);
		$this->cart->setTaxAddress($tax_address);
		$discounts = array('sum' => 0);

		// validate discounts
		if($this->zconfig->get('discounts_allowed'))
		{
			// coupon
			$coupon = $this->app->request->get('coupon_code', 'string');
			$coupon = $this->app->zoocart->discount->validateCoupon($coupon);
			if($coupon['success'])
			{
				$response['discounts']['coupon']['success'] = true;
				$discount = $coupon['discount'];
				$response['discounts']['coupon']['report'] = JText::sprintf('PLG_ZOOCART_COUPON_REPORT_SUCCESSFUL', $discount->getAmount());

				$new_price = $discount->discount($this->cart->getSubtotal());
				$discounts['sum'] = $this->cart->getSubtotal() - $new_price;
				$response['discounts']['total'] = $discounts['sum'];
			} else {
				$response['discounts']['coupon']['success'] = false;
				$response['discounts']['coupon']['report'] = implode(' ', $coupon['errors']);
			}
		}

		$cart_valid = $this->cart->validate(compact('items', 'discounts'));
		$response = array_merge_recursive($response, $cart_valid);

		// set end return results
		$response['success'] = $success && $response['success'];
		return $response;
	}

	/**
	 * placeOrder
	 */
	public function placeOrder()
	{
		// check for request forgeries
		$this->app->zlfw->checkToken();

		echo json_encode($this->_placeOrder());

		return;
	}

	/**
	 * Implementation of place order process:
	 *
	 * @return array
	 */
	protected function _placeOrder()
	{
		// init vars
		$success = true;
		$response = array('errors' => array(), 'notices' => array());

		// Data agregation vars
		$billing = array();
		$discount = array();

		// vars from request:
		$notes = $this->app->request->getString('notes','');

		$discount['sum'] = 0;
		$discount['info'] = '';

		$discounts = $this->app->zoocart->discount->validateCoupon($this->app->request->get('coupon_code', 'string'));

		if($discounts['success'] && !empty($discount) && !empty($discount['discount_info'])) {
			$discount['sum'] = $discount['discount'];
			$discount['info'] = json_encode($discount['discount_info']);
		}

		// get addresses
		$billing['address'] = $this->app->zoocart->address->getFromRequest('billing');
		// save billing address
		$billing['address']->user_id = $this->app->user->get()->id;
		$this->app->zoocart->table->addresses->save($billing['address']);

		// get payment fee
		$payment = $this->app->zoocart->payment->getFromRequest();
		$payment['method'] = $this->app->request->getString('payment_method', '');
		$payment['fee'] = $payment['fee'];

		// get shipping fee
		$shipping = $this->app->zoocart->shipping->getFromRequest();
		$shipping['fee'] = $shipping['fee'];
		$shipping['address'] = $this->app->zoocart->address->getFromRequest('shipping');

		// save shipping address only if not same as billing
		$addresses = $this->app->request->get('address', 'array', null);

		if (isset($addresses['shipping']['same_as_billing'])) {
			$shipping['address']->user_id = $this->app->user->get()->id;
			$this->app->zoocart->table->addresses->save($shipping['address']);
		}

		// Save Order
		$order = $this->cart->createOrder(compact(
			                                  'billing',
			                                  'shipping',
			                                  'notes',
			                                  'discount',
			                                  'payment'
		                                  ));

		$success = $success && !empty($order);

		if (!$success) {
			$response['errors'][] = JText::_('PLG_ZOOCART_ERROR_ORDER_SAVE_FAILED');
		}

		// cleanup cart
		if ($success) {
			$this->cart->clear();
			$response['pay_url'] = $this->component->link(array('controller' => 'orders', 'task' => 'pay', 'id' => $order->id), false);
		}

		// set and return
		$response['success'] = $success;
		return $response;
	}

	/**
	 * Validate variation fields
	 */
	protected function _validateVariations(){
		// Empty
	}

	/**
	 * _getCartModule
	 */
	protected function _getCartModule()
	{
		jimport('joomla.application.module.helper');

		// get module
		$module = JModuleHelper::getModule('mod_zoocart');
		$params = $this->app->data->create($module->params);

		ob_start();
		$zoo = $this->app;
		$items = $zoo->zoocart->table->cartitems->getByUser($zoo->user->get()->id);
		include(JModuleHelper::getLayoutPath('mod_zoocart', basename($params->find('layout._layout', 'default'), '.php')));
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}

/*
	Class: CartControllerException
*/
class CartControllerException extends AppException {}