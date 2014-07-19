<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class zoocartPaymentHelper extends AppHelper {

	/**
	 * Get a list of the ZOOcart Shipping Plugins
	 *
	 * @param null $name
	 * @return array The list
	 */
	public function getPaymentPlugins($name = null) {
		return JPluginHelper::getPlugin('zoocart_payment',$name);
	}

	/**
	 * Get the return URL
	 * 
	 * @return string The url
	 */
	public function getReturnUrl() {
		$uri = JUri::getInstance();
		$url = $uri->toString(array('scheme', 'host', 'port'));
		return $url . str_replace('//', '/', $this->app->zl->link(array('controller' => 'orders', 'task' => 'message'), false));
	}

	/**
	 * Get the cancel URL
	 * 
	 * @return string The url
	 */
	public function getCancelUrl() {
		$uri = JUri::getInstance();
		$url = $uri->toString(array('scheme', 'host', 'port'));
		return $url . str_replace('//', '/', $this->app->zl->link(array('controller' => 'orders', 'task' => 'cancel'), false));
	}

	/**
	 * Get the callback URL
	 * 
	 * @return string The url
	 */
	public function getCallbackUrl($payment_method, $format='raw') {
		$uri = JUri::getInstance();
		$url = $uri->toString(array('scheme', 'host', 'port'));
		return $url . str_replace('//', '/', $this->app->zl->link(array('controller' => 'orders', 'task' => 'callback', 'format' => $format, 'payment_method' => $payment_method), false));
	}

	/**
	 * Get Payment from HTTP request
	 * 
	 * @return object The Payment object
	 */
	public function getFromRequest()
	{
		// init vars
		$success = true;
		$response = array('errors' => array(), 'notices' => array(), 'fee' => 0);
		$payment = $this->app->request->get('payment_method', 'string');

		// validate
		if(strlen($payment))
		{
			if(JPluginHelper::getPlugin('zoocart_payment', $payment)) {
				JPluginHelper::importPlugin('zoocart_payment', $payment);
				$dispatcher = JDispatcher::getInstance();
				$fee = array_sum($dispatcher->trigger('getPaymentFee', array('order' => null)));
				$response['fee'] = $fee;

			} else {
				$success = false;
			}
		}
		
		$response['success'] = $success;
		return $response;
	}
}