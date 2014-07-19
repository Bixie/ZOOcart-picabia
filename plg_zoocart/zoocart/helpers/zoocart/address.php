<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class zoocartAddressHelper extends AppHelper {

	static $billing_info = array();

	/**
	 * Get the Item Net price
	 * 
	 * @param object $item The Item
	 * 
	 * @return float The Net price
	 *
	 * @deprecated since 3.1RC Use Address::validate() instead
	 */
	public function validate($address, $type)
	{
		// init vars
		$success = false;
		$response = array('errors' => array(), 'notices' => array());
		
		// if Addresses are not required, return positive  result
		if(!$this->app->zoocart->getConfig()->get('require_address', 1)) {
			$response['success'] = true;
			return $response;
		}

		// vars
		$layout = $type . '-form';
		$elements_config = $this->app->zoocart->address->getAddressConfig($layout);
		$billing_info = $this->getBillingInfo();
		$element_fields = $elements_config->get('fields');
		

		if ($element_fields) foreach ($element_fields as $element_data)
		{
			try {
				if ($element = $address->getElement($element_data['element'])) {

					// get params
					$params = $this->app->data->create($element_data);

					// validate submission
					$element->validateSubmission($this->app->data->create($address->getElement($element->identifier)->data()), $params);

					// validate specific billing fields
					$billing_type = $this->app->zoocart->getConfig()->get('billing_address_type', 'billing');
					if(($type == $billing_type) && $key = array_search($element->identifier, $billing_info))
					{
						$method = "_validate" . ucfirst(strtolower($key));
						if(method_exists($this, $method)) {
							$check = $this->$method($address, $element->identifier);
							$response = array_merge_recursive($response, $check);
						}
					}
				}

			} catch (AppValidatorException $e) {
				$errors[$element->identifier] = (string) $e;
			}
		}

		$success = true;
		if (count($response['errors'])) {
			$success = false;
		}

		$response['success'] = $success;
		return $response;
	}

	/**
	 * Get Billing info
	 * 
	 * @return object The info
	 */
	public function getBillingInfo() {

		if (!self::$billing_info) {
			
			$address = $this->getAddressType();

			self::$billing_info = array(
				'name' => '',
				'address' => '',
				'company' => '',
				'country' => '',
				'state' => '',
				'city' => '',
				'zip' => '',
				'vat' => '',
				'personal_id' => '',
				'phone' => '',
				'other' => ''
			);

			$elements = $address->getElements();
			foreach($elements as $key => $element) {
				$key = $element->config->get('billing', '');
				if ($key) {
					self::$billing_info[$key] = $element->identifier;
				}
			}
		}
		
		return self::$billing_info;
	}

	/**
	 * Get the Address data
	 *
	 * @param object $address The Address Object
	 * 
	 * @return array The data
	 */
	public function getAddressData($address) {
		$billing_info = $this->getBillingInfo();
		$result = array();
		foreach($billing_info as $key => $info) {
			if($key == 'country') {
				$result[$key] = array_shift($address->getElement($info)->get('country'));
			} else {
				$value = $address->getElement($info)->data();
				while(is_array($value)) {
					$value = array_shift($value);
				}
				$result[$key] = $value;
			}
		}

		return $result;
	}

	/**
	 * Get from values
	 *
	 * @param object $address The Address Object
	 * @param string $type The Address Type
	 * 
	 * @return object The From values
	 */
	public function getFromValues($address, $type = 'billing') {
		$addr = $this->app->object->create('Address');
		$addr->type = $type;

		// bind element data
		$addr->elements = $this->app->data->create();
		foreach ($addr->getElements() as $id => $element) {
			if (isset($address[$id])) {
				$element->bindData($address[$id]);
			} else {
				$element->bindData();
			}
		}

		return $addr;
	}

	/**
	 * Get from HTTP request
	 *
	 * @param string $type The Address Type
	 * 
	 * @return object The Address
	 */
	public function getFromRequest($type = 'billing')
	{
		$address = false;
		if($addresses = $this->app->request->get('address', 'array', null))
		{
			// check if we should return the billing address instead
			if (($type == 'shipping') && isset($addresses[$type]['same_as_billing'])) {
				$address = $this->_getAddress('billing');
				$address->type = 'shipping';
				return $address;
			}

			// get address from id
			if(isset($addresses[$type]['id']) && !empty($addresses[$type]['id'])) {
				$address = $this->app->zoocart->table->addresses->get((int)$addresses[$type]['id']);

			// else get it from form fields
			} else if(isset($addresses[$type]['elements'])) {
				$address = $this->getFromValues($addresses[$type]['elements'], $type);
			}
		}

		return $address;
	}

	/**
	 * Get the Address Type
	 * 
	 * @return string The Address type
	 */
	public function getAddressType() {

		// register AddressType
		$this->app->loader->register('AddressType', 'zoocart:classes/addresstype.php');

		$address = $this->app->object->create('AddressType', array('address'));

		if (($file = $address->getConfigFile()) && JFile::exists($file)) {
			$address->config = $this->app->data->create(JFile::read($file));
		} else {
			$address->config = $this->app->data->create();
		}

		$address->name = JText::_('PLG_ZLFRAMEWORK_ADDRESS');
		return $address;
	}

	/**
	 * Get the Address config data
	 * 
	 * @param string $layout The Layout name
	 * 
	 * @return array The Config Data
	 */
	public function getAddressConfig($layout) {
		$address_renderer = $this->app->renderer->create('address')->addPath(array($this->app->path->path('component.site:'), $this->app->path->path('zoocart:')));

		return $address_renderer->getConfigPositions('address', $layout);
	}

	/**
	 * Validate addressat's name
	 * (General algorythm)
	 *
	 * @param   Object  Address type object
	 * @param   string  Element's id
	 *
	 * @return  Array
	 */
	protected function _validateName($address, $element_id)
	{
		// init vars
		$success = true;
		$response = array('errors' => array(), 'notices' => array());
		$pattern = '~^[^\/\\\.,\(\)\+*#\^\$@=%\~\|\{\}]{3,}$~i';
		$elements = $address->getElements();
		$name = $elements[$element_id]->get('value');

		// don't validate empty values
		if(empty($name)) {
			$response['success'] = $success;
			return $response;
		}

		// validate
		$success = (bool)preg_match($pattern,$name);

		if(!$success)
			$errors[] = JText::_('PLG_ZOOCART_ADDRESS_VALIDATION_NAME_WRONG');

		$response['success'] = $success;
		return $response;
	}

	/**
	 * Validate ZIP number
	 * (General algorythm)
	 *
	 * @param   Object  Address type object
	 * @param   string  Element's id
	 *
	 * @return  Array
	 */
	protected function _validateZip($address, $element_id)
	{
		// init vars
		$success = true;
		$response = array('errors' => array(), 'notices' => array());
		$pattern = '~^[0-9\s-]{5,}$~';
		$elements = $address->getElements();
		$zip = $elements[$element_id]->get('value');

		// make sure is fullfilled
		if($zip === '') {
			$response['success'] = false;
			return $response;
		}
		
		// validate
		$success = (bool)preg_match($pattern, $zip);

		if(!$success)
			$errors[] = JText::_('PLG_ZOOCART_ADDRESS_VALIDATION_ZIP_WRONG');

		$response['success'] = $success;
		return $response;
	}

	/**
	 * Validate VAT
	 *
	 * @param   Object  Address type object
	 * @param   string  Element's id
	 *
	 * @return  Array
	 */
	protected function _validateVat($address, $element_id)
	{
		// init vars
		$success = true;
		$response = array('errors' => array(), 'notices' => array());
		$validate = $this->app->zoocart->getConfig()->get('vies_validation', 1);
		$hard_validate = $this->app->zoocart->getConfig()->get('vies_validation_hard', 0);
		
		// abort if validation is off
		if(!$validate) {
			$response['success'] = $success;
			return $response;
		}

		// get country
		$country = isset($address->country) ? $address->country : $this->app->zoocart->getConfig()->get('default_country', '');

		// validate
		if ($this->app->country->isEU($country)) {

			// get vat number
			$vat = isset($address->vat) ? $address->vat : null;

			// check
			$success = $this->app->zoocart->tax->isValidVat($country, $vat);
			
			if(!$success) {
				if ($hard_validate) {
					$response['errors'][$element_id] = JText::_('PLG_ZOOCART_VIES_VAT_ID_NOT_REGISTERED'); 
				} else {
					$success = true;
					$response['notices'][$element_id] = JText::_('PLG_ZOOCART_VIES_VAT_ID_NOT_REGISTERED'); 
				}
			}
		}

		$response['success'] = $success;
		return $response;
	}

	/**
	 * Get the Submission form field
	 *
	 * @param   Object  Element to get field for
	 *
	 * @return  HTML
	 */
	public function getSubmissionField($element, $params)
	{
		$html = array();
		$icon = $this->getSubmissionFieldIcon($element->config->get('billing', ''));
		$label = $params->get('altlabel') ? $params->get('altlabel') : $element->config->get('name');
		$required = $params->get('required', 0);
		
		// switch by element type
		switch($element->getElementType()) {
			case 'text': case 'textpro': case 'textarea': case 'textareapro':
				if ($icon) $html[] = '<div class="uk-form-icon uk-width-1-1"><i class="uk-icon-'.$icon.'"></i>';
				$html[] = '<input type="text" class="uk-width-1-1" name="'.$element->getControlName('value').'" value="'.$element->get('value', '').'" placeholder="'.JText::_($label).'"'.($required ? ' required' : '').' />';
				if ($icon) $html[] = '</div>';
				break;

			case 'country':
				$selectable_countries = $element->config->get('selectable_country', array());

				if (count($selectable_countries)) {
					$countries = $this->app->country->getIsoToNameMapping();
					$keys = array_flip($selectable_countries);
					$countries = array_intersect_key($countries, $keys);

					$options = array();
					$options[] = $this->app->html->_('select.option', '', JText::_('PLG_ZLFRAMEWORK_ADDRESS_COUNTRY'));
					foreach ($countries as $key => $country) {
						$options[] = $this->app->html->_('select.option', $key, JText::_($country));
					}

					if ($icon) $html[] = '<div class="uk-form-icon uk-width-1-1"><i class="uk-icon-'.$icon.'"></i>';
					$html[] = $this->app->html->_('select.genericlist', $options, $element->getControlName('country', true), 'class="uk-width-1-1"'.($required ? ' required' : ''), 'value', 'text', $element->get('country', array()));
					if ($icon) $html[] = '</div>';
				} else {
					$html[] = JText::_("There are no countries to choose from.");
				}
				break;

			default:
				return $element->renderSubmission($params);
				break;
		}

		// return result
		return implode('', $html);
	}

	/**
	 * Get the Submission form field
	 *
	 * @param   Object  Element to get field for
	 *
	 * @return  HTML
	 */
	public function getSubmissionFieldIcon($mapping)
	{
		$icon_map = array(
			'name' => 'bookmark-o',
			'address' => 'building-o',
			'company' => 'suitcase',
			'country' => 'flag-o',
			'state' => 'globe',
			'city' => 'globe',
			'zip' => 'globe',
			'vat' => 'barcode',
			'personal_id' => 'user',
			'phone' => 'phone'
		);

		if (array_key_exists($mapping, $icon_map)) {
			return $icon_map[$mapping];
		} else {
			return false;
		}
	}
}