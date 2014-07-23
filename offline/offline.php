<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgZoocart_PaymentOffline extends JPaymentDriver {

	public function render($data = array()) {
		$app = App::getInstance('zoo');
		$data['order']->state = $app->zoocart->getConfig()->get('payment_pending_orderstate', 4);
		
		$items = $data['order']->getItems();
		$itemnames = array();
		foreach ($items as $item) {
			$itemnames[] = $item->name;
		}
		$data['order']->order_items = implode('<br/>',$itemnames);
	
		$address = $data['order']->getBillingAddress();
		$elements = $address->getElements();
		$names = array();
		$allData = array();
		$email = '';
		foreach($elements as $key => $element) {
			$key = $element->config->get('billing', '');
			$value = $element->get('value');
			if ($key == 'name') {
				$names[] = $value;
			}
			if ($key == 'personal_id') {
				$email = $value;
			}
			if (!empty($key)) {
				$allData[] = $element->config->get('name', '').': '.$value;
			}
		}
		$data['order']->user_data = implode('<br/>',$allData);
		$name = ($names[3]?$names[3]:$names[2]).' '.$names[4];
		$user = JFactory::getUser();
		if (!empty($email) && $user->email != $email) {
			$success = false;
			//bestaat er een user met die mail?
			$db = JFactory::getDbo();
			$db->setQuery("SELECT * FROM #__users WHERE block = 0 AND (email = '$email' OR username = '$email')");
			$existing = $db->loadObject();
			if ($existing->id) {
				//wijzig order (kan niet, gebruiker heeft dan geen rechten meer om order te zien)
				// $data['order']->user_id = $existing->id;
				$user->set('name',$name);
			} else {
				//wijzig user
				$user->set('username',$email);
				$user->set('email',$email);
				$user->set('name',$name);
			}
			$success = $user->save();
			if(!$success) {
				$errors = $user->getErrors();
				echo implode($errors);
			}
			
			//resend mail
			$app->zoocart->email->send('order_new', $data['order']);
		}
		
		$app->zoocart->table->orders->save($data['order']);

		return parent::render($data);
	}
}