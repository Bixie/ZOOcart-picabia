<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class Address {

	public $id;

	public $type;

	public $user_id;

	public $params;

	public $elements;

	public $default;

	public $app;

	protected $_elements;

 	/**
 	 * Class Constructor
 	 */
	public function __construct() {

		$this->app = App::getInstance('zoo');

		// decorate data as object
		$this->params = $this->app->parameter->create($this->params);

		// decorate data as object
		$this->elements = $this->app->data->create($this->elements);

	}

	public function __get($name){

		$billing_info = $this->app->zoocart->address->getBillingInfo();

		if(array_key_exists($name, $billing_info)){
			if($element = $this->getElement($billing_info[$name])) {
				$value = $element->get('value');
				if(!$value){
					$value = $element->get($name);
				}
				if($value){
					if(is_array($value)){
						$value = array_shift($value);
					}
					return $value;
				}
				return null;
			}
		}

		$properties = get_object_vars($this);
		if(array_key_exists($name,$properties))
			return $this->$name;

		return null;
	}

	public function __isset($name){
		return $this->__get($name);
	}

	/**
	 * Get an element object out of this item
	 *
	 * @param  string $identifier The element identifier
	 *
	 * @return Element             The element object
	 */
	public function getElement($identifier) {

		if (isset($this->_elements[$identifier])) {
			return $this->_elements[$identifier];
		};

		if ($element = $this->getType()->getElement($identifier)) {
			$element->setItem($this);
			$this->_elements[$identifier] = $element;
			return $element;
		}

		return null;
	}

	/**
	 * Get the list of elements
	 *
	 * @return array The element list
	 */
	public function getElements() {

		// get types elements
		if ($type = $this->getType()) {
			foreach ($type->getElements() as $element) {
				if (!isset($this->_elements[$element->identifier])) {
					$element->setItem($this);
					$this->_elements[$element->identifier] = $element;
				}
			}
			$this->_elements = $this->_elements ? $this->_elements : array();
		}

		return $this->_elements;
	}

	public function getParams() {
		return $this->params;
	}

	/**
	 * Get the item Type
	 *
	 * @return Type The Address Type
	 */
	public function getType() {
		$type = $this->app->zoocart->address->getAddressType();

		return $type;
	}

	/**
	 * Bind the Item Data
	 */
	public function bind($data, $skip_id = false) {
		$data = (array)$data;

		// the app object must be ignored
		unset($data['app']);

		foreach($data as $k => $v){
			if(isset($this->$k) && !($skip_id && $k == 'id')) {
				$this->$k = $v;
			}
		}

		// bind element data
		$this->elements = $this->app->data->create($this->elements);
		foreach ($this->getElements() as $id => $element) {
			if (isset($this->elements[$id])) {
				$element->bindData($this->elements[$id]);
			} else {
				$element->bindData();
			}
		}
	}

	/**
	 * Validate the address
	 *
	 * @return array Response
	 */
	public function validate()
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
		$layout = $this->type . '-form';
		$elements_config = $this->app->zoocart->address->getAddressConfig($layout);
		$billing_info = $this->app->zoocart->address->getBillingInfo();
		$element_fields = $elements_config->get('fields');
		

		if ($element_fields) foreach ($element_fields as $element_data)
		{
			try {
				if ($element = $this->getElement($element_data['element'])) {

					// get params
					$params = $this->app->data->create($element_data);

					// validate submission
					$element->validateSubmission($this->app->data->create($this->getElement($element->identifier)->data()), $params);

					// validate specific billing fields
					$billing_type = $this->app->zoocart->getConfig()->get('billing_address_type', 'billing');
					if(($this->type == $billing_type) && $key = array_search($element->identifier, $billing_info))
					{
						$method = "_validate" . ucfirst(strtolower($key));
						if(method_exists($this, $method)) {
							$check = $this->app->zoocart->address->$method($this, $element->identifier);
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
}

class AddressException extends AppException {}