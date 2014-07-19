<?php
/**
* @package		ZL Elements
* @author    	JOOlanders, SL http://www.zoolanders.com
* @copyright 	Copyright (C) JOOlanders, SL
* @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
	Class: FieldsetVariation
		Text FieldsetVariation class
*/
class FieldsetVariation extends Fieldset {

	/*
		Function: Constructor
			Class Constructor.

		Parameters:
			$app - App A reference to an App Object
	*/
	public function __construct($app, $element) {
		parent::__construct($app, $element);

		// set Fieldset name
		$this->name = JText::_('Variation');
 	}

 	/*
		Function: hasValue
			Checks if the fieldset value is set

		Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array())
	{
		$value = $this->get('attributes');

		return !empty($value);
	}

	/*
		Function: render
			Renders the fieldset values

		Returns:
			String - html
	*/
	public function render($params = array()) {
		return $this->get('value');
	}

	/*
		Function: validateSubmission
			Validates the submitted fieldset

		Parameters:
			$value  - AppData value
			$params - AppData submission parameters

		Returns:
			Array - cleaned value
	*/
	public function validateSubmission($values, $params)
	{
		// init vars
		$required = $params->get('required');

		// validate
		$value = $this->app->validator->create('string', array('required' => $required))->clean($values->get('value'));

		// important, validate fieldset type
		$fieldset = $this->getFieldsetType();

		return compact('value', 'fieldset');
	}
}