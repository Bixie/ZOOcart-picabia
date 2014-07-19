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
	Class: OrdersController
		The controller class for orders
*/
class OrdersController extends SiteResourceController {

	public function __construct($default = array()) {

		$this->resource_name = 'orders';

		$this->resource_class = 'Order';

		$this->default_order = '`created_on`';

		$this->default_order_dir = 'DESC';
 
		parent::__construct($default);
	}

	public function pay()
	{
		$id = $this->app->request->getInt('id');

		if (!$id) {
			throw new AppException(JText::_('PLG_ZOOCART_ERROR_ORDER_NOT_FOUND'), 404);
		}

		$this->order = $this->table->get($id);
		$config = $this->app->zoocart->getConfig();

		// Access hook:
		$user =  $this->app->user->get();
		$order = $this->app->zoocart->table->orders->get($id);
		$access = $this->app->user->isJoomlaAdmin($user) || $this->app->user->isJoomlaSuperAdmin($user) || ($order->user_id==$user->id);

		if(!$access){
			if($user->guest){
				$this->setRedirect($this->app->link(array('option'=>'com_users','view'=>'login','return'=>base64_encode(JURI::current())),false),'PLG_ZOOCART_ERROR_LOGIN_REQUIRED','notice');
			}else{
				$this->app->error->raiseError(403, JText::_('PLG_ZOOCART_ERROR_NO_PERMISSIONS_TO_ACCESS_ORDER_PAYMENT'));
			}
			return;
		}

		if (in_array($this->order->state, array($config->get('new_orderstate'), $config->get('payment_failed_orderstate')))) { 
			$this->app->session->set('com_zoo.zoocart.pay_order_id', $this->order->id);

			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('zoocart_payment', $this->order->payment_method);

			$this->renderer = $this->app->renderer->create('item')->addPath(array($this->app->path->path('component.site:'), $this->app->path->path('zoocart:')));

			$this->payment_html = implode('', $dispatcher->trigger('render', array('data' => array('order' => $this->order))));
			$this->getView()->setLayout('pay')->display();
		} else {
			// redirect to order view
			$this->app->system->application->enqueueMessage(JText::_('PLG_ZOOCART_ERROR_ORDER_ALREADY_PAYED'), 'notice');
			$this->setRedirect($this->component->link(array('controller' => $this->controller, 'task' => 'view', 'id' => $id)));
			return;
		}
	}

	public function callback()
	{
		// check for request forgeries
		$this->app->zlfw->checkToken();

		$data = $this->app->request->get('request:', 'array');
		$order_id = (int) $this->app->session->get('com_zoo.zoocart.pay_order_id');
		$payment_method = $this->app->request->get('payment_method', '');
		
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('zoocart_payment', $payment_method);

		$result = $dispatcher->trigger('callback', array('data' => $data));
		$result = $this->app->data->create(array_shift($result));

		$payment = $this->app->object->create('Payment');
		$payment->payment_method = $payment_method;
		$payment->status = $result->get('status', 0);
		$payment->order_id = ($id = $result->get('order_id', 0)) ? $id : $order_id;
		$payment->transaction_id = $result->get('transaction_id', '');
		$payment->data = json_encode($data);
		$payment->total = $result->get('total', 0);

		$this->app->zoocart->table->payments->save($payment);
		/*redirect if plugin needs that*/
		if ($result->get('redirect',false)) {
			$this->setRedirect($result->get('redirect'));
		} else {
		   $this->message();
		}

		return;
	}

	public function message()
	{
		// check for request forgeries
		$this->app->zlfw->checkToken();

		$data = $this->app->request->get('request:', 'array');
		$order_id = $this->app->session->get('com_zoo.zoocart.pay_order_id');
		$order = $this->app->zoocart->table->orders->get($order_id);

		$data['order'] = $order;

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('zoocart_payment', $order->payment_method);

		$result = $dispatcher->trigger('message', array('data' => $data));

		$this->message = implode('', $result);
		$this->getView()->setLayout('message')->display();
	}

	public function cancel() {
		$this->order_id = $this->app->session->get('com_zoo.zoocart.pay_order_id');
		$this->getView()->setLayout('cancel')->display();
	}

	/**
	 * changePaymentMethod
	 */
	public function changePaymentMethod()
	{
		// check for request forgeries
		$this->app->zlfw->checkToken();

		// init vars
		$success = true;
		$response = array('errors' => array(), 'notices' => array());

		$method = $this->app->request->getString('payment_method');
		$order_id = $this->app->request->getInt('order_id');

		// get order
		$order = $this->table->get($order_id);
		$plugin = JPluginHelper::getPlugin('zoocart_payment', $method);

		if(!$method || !$plugin) {
			$response['success'] = false;
			$response['payment_method'] = $order->payment_method;
			echo json_encode($response);
			return;
		}

		// decode plugins params
		$plugin->params = $this->app->data->create($plugin->params);

		// save order
		$order->payment_method = $method;
		$this->table->save($order);

		// update response
		$response['success'] = $success;
		$response['payment_method'] = $method;
		$response['payment_title'] = strlen($plugin->params->get('title')) ? $plugin->params->get('title') : ucfirst($plugin->name);

		echo json_encode($response);
		return;
	}

	/**
	 * Base view method override
	 *
	 */
	public function view() {

		// get request vars
		$cid  = $this->app->request->get('id', 'int');

		if (!$this->resource = $this->table->get($cid)) {
			$this->app->error->raiseError(404, JText::sprintf('PLG_ZOOCART_ERROR_ORDER_NOT_FOUND', $cid));
			return;
		}

		$user = JFactory::getUser();
		$order = $this->app->zoocart->table->orders->get($cid);
		$access = $this->app->user->isJoomlaAdmin($user) || $this->app->user->isJoomlaSuperAdmin($user) || ($order->user_id==$user->id);

		if(!$access){
			if($user->guest){
				$this->setRedirect($this->app->link(array('option'=>'com_users','view'=>'login','return'=>base64_encode(JURI::current())),false),'PLG_ZOOCART_ERROR_LOGIN_REQUIRED','notice');
			}else{
				$this->app->error->raiseError(403, JText::_('PLG_ZOOCART_ERROR_NO_PERMISSIONS_TO_ACCESS_ORDER'));
			}
			return;
		}

		$this->renderer = $this->app->renderer->create('item')->addPath(array($this->app->path->path('component.site:'), $this->app->path->path('zoocart:')));

		// display view
		$this->getView()->setLayout('view')->display();
	}
}

/*
	Class: OrderControllerException
*/
class OrdersControllerException extends AppException {}