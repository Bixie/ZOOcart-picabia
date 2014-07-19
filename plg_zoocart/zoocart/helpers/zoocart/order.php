<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class zoocartOrderHelper extends AppHelper {
	
	/**
	 * Send notification email
	 *
	 * @param object $order The Order object
	 * @param string $layout The layout
	 *
	 * @return boolean True on success
	 * @deprecated since BETA23 use zoocartEmailHelper::send() instead
	 */
	public function sendNotificationMail($order, $layout) {
		// workaround to make sure JSite is loaded
		$this->app->loader->register('JSite', 'root:includes/application.php');

		// init vars
		$website_name = $this->app->system->application->getCfg('sitename');
		$order_link = JURI::root().$this->app->zl->link(array('controller' => 'orders', 'task' => 'view', 'id' => $order->id));

		$user = $this->app->user->get($order->user_id);
		$recipients = array();
		$recipients[$user->email] = $user->name;

		// send email to $recipients
		foreach ($recipients as $email => $name) {

			if (empty($email)) {
				continue;
			}

			$mail = $this->app->mail->create();
			$mail->setSubject(JText::sprintf('PLG_ZOOCART_ORDER_STATE_CHANGE', $order->id));
			$file ='zoocart:views/admin/orders/tmpl/'.$layout;
			$mail->setBodyFromTemplate($file, compact(
				'order', 'website_name', 'email', 'name', 'order_link'
			));
			$mail->addRecipient($email);
			$mail->Send();
		}

		return true;
	}

	/**
	 * Send admin mail notifications
	 *
	 * @param object $order The Order object
	 * @param string $layout The layout
	 *
	 * @return boolean True on success
	 * @deprecated since BETA23 use zoocartEmailHelper::sendAdmin() instead
	 */
	public function sendAdminNotificationMail($order, $layout) {
		define('ADMIN_USERGROUPS', '8');

		// workaround to make sure JSite is loaded
		$this->app->loader->register('JSite', 'root:includes/application.php');

		// init vars
		$website_name = $this->app->system->application->getCfg('sitename');
		//Link to admin panel:
		$order_link = JURI::root().'administrator/index.php?option=com_zoolanders&controller=orders&task=edit&cid[]='.$order->id;

		// getAll SuperAdmin users:
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query  ->select('u.email')
		        ->from('#__users AS u')
		        ->join('INNER','#__user_usergroup_map AS ugm ON ugm.user_id=u.id')
		        ->where(array(
			                'ugm.group_id IN('.ADMIN_USERGROUPS.')',
			                'u.sendEmail=1',
			                'u.block=0'
		                ));

		$db->setQuery($query);
		$admins = $db->loadObjectList();

		// send email to $recipients
		if(!empty($admins))
		{
			foreach ($admins as $admin) {

				if (empty($admin->email)) {
					continue;
				}

				$mail = $this->app->mail->create();
				$mail->setSubject(JText::sprintf('PLG_ZOOCART_NEW_INCOMING_ORDER', $order->id));
				$file ='zoocart:views/admin/orders/tmpl/'.$layout;
				$mail->setBodyFromTemplate($file, compact(
					'order', 'website_name', 'order_link'
				));
				$mail->addRecipient($admin->email);
				$mail->Send();
			}
		}
	}

	/**
	 * Get the Order States list field
	 *
	 * @param string $name The field control name
	 * @param array $selected The selected values
	 *
	 * @return string The HTML form field
	 */
	public function orderStatesList($name, $selected) {

		// init vars
		$attribs = '';
		$options = array();

		// populate options
		foreach ($this->app->zoocart->table->orderstates->all() as $orderstate) {
			$options[] = $this->app->html->_('select.option', $orderstate->id, JText::_($orderstate->name));
		}

		return $this->app->html->_('select.genericlist', $options, $name, $attribs, 'value', 'text', $selected);
	}

	/**
	 * Check if the Order is payed
	 * 
	 * @param object $order The Order object
	 * 
	 * @return boolean
	 */
	public function isPayed($order) {
		
		$config = $this->app->zoocart->getConfig();
		$order_id = isset($order->getState()->id)?$order->getState()->id:0;

		$yet_to_pay = in_array($order_id, array($config->get('new_orderstate'), $config->get('payment_failed_orderstate')));
		
		return !$yet_to_pay;
	}

	/**
	 * Get log message for chosen param
	 *
	 * @param $key
	 * @param $old
	 * @param $new
	 *
	 * @return string
	 */
	public function getLogPhrase($key, $old, $new){
	 	$phrase = '';

		switch($key){
			case 'user_id': $before  = JUser::getInstance($old)->name;
							$after   = JUser::getInstance($new)->name;
							$phrase  = JText::sprintf('PLG_ZOOCART_HISTORY_CHANGED_STRING', JText::_('PLG_ZLFRAMEWORK_USER'), $before, $after);
					break;
			case 'notes': $phrase = JText::sprintf('PLG_ZOOCART_HISTORY_UPDATED', JText::_('PLG_ZOOCART_NOTES'));
					break;
			case 'notes': $phrase = JText::sprintf('PLG_ZOOCART_HISTORY_UPDATED', JText::_('PLG_ZOOCART_NOTES'));
				    break;
			case 'billing_address': $phrase = JText::sprintf('PLG_ZOOCART_HISTORY_WAS_CHANGED', JText::_('PLG_ZOOCART_ADDRESS_BILLING'));
				break;
			case 'shipping_address': $phrase = JText::sprintf('PLG_ZOOCART_HISTORY_WAS_CHANGED', JText::_('PLG_ZOOCART_ADDRESS_SHIPPING'));
				break;
			case 'payment_method': $phrase = JText::sprintf('PLG_ZOOCART_HISTORY_WAS_CHANGED', JText::_('PLG_ZOOCART_PAYMENT_METHOD'));
				break;
			case 'shipping_method': $phrase = JText::sprintf('PLG_ZOOCART_HISTORY_WAS_CHANGED', JText::_('PLG_ZOOCART_SHIPPING_METHOD'));
				break;
			case 'state':
						$oldstate = $this->app->zoocart->table->orderstates->get($old);
						$newstate = $this->app->zoocart->table->orderstates->get($new);
						$before  = empty($oldstate)?JText::sprintf('PLG_ZOOCART_ORDERSTATE_N', $old):$oldstate->name;
						$after   = empty($newstate)?JText::sprintf('PLG_ZOOCART_ORDERSTATE_N', $new):$newstate->name;
						$phrase  = JText::sprintf('PLG_ZOOCART_HISTORY_CHANGED_STRING', JText::_('PLG_ZOOCART_ORDER_STATE'), JText::_($before), JText::_($after));
				break;

			default: $phrase = JText::sprintf('PLG_ZOOCART_HISTORY_CHANGED_STRING', ucfirst($key), $old, $new);
		}

		return $phrase;
	}
}
