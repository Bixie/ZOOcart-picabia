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
 * Class zoocartEmailHelper
 * Zoocart mailing helper
 */
class zoocartEmailHelper extends AppHelper {

	/**
	 * @var array ZOOcart email types
	 */
	protected static $_email_types = array(
		'order_new',
		'order_new_admin',
		'order_state_change'
	);

	/**
	 * Get default mail type
	 *
	 * @return string
	 */
	public function getDefaultType(){
		return array_shift(self::$_email_types);
	}

	/**
	 * Fulfill template string(text with appropriate data)
	 *
	 * @param $string
	 * @param null $data (\Order object )
	 *
	 * @return string output
	 */
	protected function prepareString($string, $data = null){
		$output = $string;
		$pattern = '~\{([\w]*)\}~Uis';
		$matches = array();

		if(!empty($data)){
			$found = preg_match_all($pattern, $string, $matches);

			if($found){
				foreach($matches[1] as $match){

					$lexem = strtolower($match);

					// Retrieve user:
					$user = $this->app->user->get($data->user_id);
					// Retrieve system data:
					$joomla = JFactory::getApplication();

					switch($lexem){
						case 'sitename': $replacement = $joomla->getCfg('sitename','');
							break;
						case 'siteurl': $replacement = JURI::root();
							break;
						case 'order_link': $replacement = @JURI::root().JRoute::_('index.php?option=com_zoolanders&controller=orders&id='.$data->id,false);
							break;
						case 'user': $replacement = @$user->name;
							break;
						case 'username': $replacement = @$user->username;
							break;
						case 'order_number': $replacement = @$data->id;
							break;
						case 'order_state': if(@!empty($data->state)){
												 $orderstate = $this->app->zoocart->table->orderstates->get($data->state);
												 $replacement = !empty($orderstate)?JText::_($orderstate->name):'';
											}
							break;
						default	: $replacement = ''; break;
					}
					$output = str_replace('{'.$match.'}', $replacement, $output);
				}
			}
		}else{
			$output = preg_replace($pattern, '', $string);
		}

		return $output;
	}

	/**
	 * Send mails to user with context
	 *
	 * @param $mails
	 * @param $context
	 * @param int $user_id
	 */
	private function _mailing($type, $mails, $context, $user_id = 0){

		if($user_id)
		{
			$user = $this->app->user->get($user_id);
		}else{
			$user = $this->app->user->get();
		}

		if(!empty($mails)){
			foreach($mails as $mail){

				if(!$mail->published)
				{
					continue;
				}

				// Check against ACL rules:
				if(!$user->block){
					$allowed_groups = explode(',', $mail->groups);
					$user_groups = $user->getAuthorisedGroups();

					if(empty($mail->groups) || array_intersect($allowed_groups, $user_groups)){

						// Prepare email for sending:
						$from = JFactory::getApplication()->getCfg('mailfrom','');
						$fromname = JFactory::getApplication()->getCfg('fromname','');

						$subject = $this->prepareString($mail->subject, $context);
						$body = $this->prepareString($mail->template, $context);

						$mailer = JFactory::getMailer();
						$mailer->sendMail($from, $fromname, $user->email, $subject, $body, true, $mail->cc, $mail->bcc);
					}
				}
			}
		}
		elseif(empty($mails)){

			// No configured mail, non disabled, so load default template for type and send it:
			$tmpl = $this->loadTemplate($type, JLanguageHelper::detectLanguage());

			if(!empty($user) && $user->sendEmail && !$user->block){

				// Prepare email for sending:
				$from = JFactory::getApplication()->getCfg('mailfrom','');
				$fromname = JFactory::getApplication()->getCfg('fromname','');
				$subject = $this->prepareString($tmpl->subject, $context);
				$body = $this->prepareString($tmpl->body, $context);

				$mailer = JFactory::getMailer();
				$mailer->sendMail($from, $fromname, $user->email, $subject, $body, true);
			}
		}

	}

	/**
	 * Send appropriate email notification, using data as data container
	 *
	 * @param $type
	 * @param $data Context
	 */
	public function send($type, $data = null){

		$mails = $this->app->zoocart->table->emails->getByType($type);

		$this->_mailing($type, $mails, $data, $data->user_id);
	}

	/**
	 * Send admin email
	 *
	 * @param $type Context
	 * @param $data
	 */
	public function sendAdmin($type, $data = null){
		// Get all admin users with sendEmail flag On:
		define('ADMIN_USERGROUPS', '8');

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query  ->select('u.id')
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

				// Get provided admin emails by type:
				$mails = $this->app->zoocart->table->emails->getByType($type);

				$this->_mailing($type, $mails, $data, $admin->id);

			}
		}
	}

	/**
	 * Return email types list as selectbox
	 *
	 * @param string
	 * @param string
	 * @param string
	 *
	 * @return string
	 */
	public function emailTypesList($name, $attribs = '', $selected = ''){

		$list = array();

		foreach(self::$_email_types as $type){
			$node = array();
			$node['value'] = $type;
			$node['text'] = JText::_('PLG_ZOOCART_EMAIL_TYPE_'.strtoupper($type));

			$list[] = $node;
		}

		return $this->app->html->genericList($list, $name, $attribs, 'value', 'text', $selected);
	}

	/**
	 * Load email template strings for provided type
	 *
	 * @param strng
	 * @param string
	 *
	 * @return mixed
	 */
	public function loadTemplate($type, $language = ''){
		$set = new stdClass();

		$check_corrupted_pattern = '~^PLG~';

		$subject_template   = 'PLG_ZOOCART_EMAIL_' . strtoupper($type) . '_SUBJECT_TEMPLATE';
		$body_template      = 'PLG_ZOOCART_EMAIL_' . strtoupper($type) .'_BODY_TEMPLATE';

		if($language && JLanguage::exists($language)){
			// Loading templates for chosen language from ini-file:
			$lang = JLanguage::getInstance($language);
			$loaded = $lang->load('plg_system_zoocart', JPATH_ADMINISTRATOR, $language, true, JLanguageHelper::detectLanguage());

			if($loaded)
			{
				$subject_template = $lang->_($subject_template);
				$body_template = $lang->_($body_template);
			}
		}

		// Additional check if template constants were not defined:
		if(preg_match($check_corrupted_pattern, $subject_template)){
			$subject_template = JText::_($subject_template);
		}
		if(preg_match($check_corrupted_pattern, $body_template)){
			$body_template = JText::_($body_template);
		}

		$set->subject = $subject_template;
		$set->body = $body_template;

		return $set;
	}

}
