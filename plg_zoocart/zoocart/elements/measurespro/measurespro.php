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
   Class: ElementMeasures
	   The measures element class
*/
class ElementMeasurespro extends ElementRepeatablePro implements iRepeatSubmittable {

	/*
		Function: _getSearchData
			Get repeatable elements search data.

		Returns:
			String - Search data
	*/
	protected function _getSearchData() {
		return $this->get('value', $this->config->get('default'));
	}

	/*
		Function: _renderSubmission
			Renders the element in submission.

	   Parameters:
			$params - AppData submission parameters

		Returns:
			String - html
	*/
	public function _renderSubmission($params = array()) {
		return $this->_edit();
	}

	/*
		Function: _validateSubmission
			Validates the submitted element

	   Parameters:
			$value  - AppData value
			$params - AppData submission parameters

		Returns:
			Array - cleaned value
	*/
	public function _validateSubmission($values, $params)
	{
		// init vars
		$required = $params->get('required');

		// validate
		$value = $this->app->validator->create('string', array('required' => $required))->clean($values->get('value'));
		$unit = $this->app->validator->create('array', array('required' => $required))->clean($values->get('unit'));

		return compact('value', 'unit');
	}

	/*
		Function: _convert
			Converts the specifed mesure unit

	   Parameters:
			$q - the mesure value
			$from - the unit being coverted
			$to - the unit to convert to

		Returns:
			String - the converted value
	*/
	protected function _convert($q, $from, $to)
	{
		// avoid converting to same unit
		if ($from == $to) return $q;

		$weight_array = array("g", "oz", "lb", "kg");
		$distance_array = array("cm", "in", "ft", "yd", "m", "km", "mi");
		$volume_array = array("l", "gal");
		$temperature_array = array("F", "C");

		$w = in_array($from, $weight_array)&&in_array($to, $weight_array);
		$d = in_array($from, $distance_array)&&in_array($to, $distance_array);
		$v = in_array($from, $volume_array)&&in_array($to, $volume_array);
		$m = in_array($from, $temperature_array)&&in_array($to, $temperature_array);

		$c = array("oz g" => $q*28.35, "lb g" => $q*453.59, "kg g" => $q*1000,
		"g oz" => $q/28.35, "kg oz" => $q*35.274, "lb oz" => $q*16,
		"g lb" => $q/453.59, "oz lb" => $q/16, "kg lb" => $q*2.205,
		"g kg" => $q/1000, "oz kg" => $q/35.274, "lb kg" => $q/2.205,
		"in cm" => $q*2.54, "ft cm" => $q*30.48, "yd cm" => $q*91.44, "mi cm" => $q*160934,
		"cm in" => $q/2.54, "ft in" => $q*12, "yd in" => $q*36, "mi in" => $q*63360,
		"cm ft" => $q/30.48, "in ft" => $q/12, "yd ft" => $q*3, "mi ft" => $q*5280,
		"cm yd" => $q/91.44, "in yd" => $q/36, "ft yd" => $q/3, "mi yd" => $q*1760,
		"cm mi" => $q/160934, "in mi" => $q/63360, "ft mi" => $q/5280, "yd mi" => $q/1760,
		"l quarts" => $q*1.057, "l gal" => $q/3.785,
		"quarts l" => $q/1.057, "quarts gal" => $q/4,
		"gal l" => $q*3.785, "gal quarts" => $q*4,
		"C F" => ((9/5)*$q)+32, "F C" => 5/9*($q-32));

		if($w||$d||$v||$m)
		{
		    $x = $c[$from." ".$to];
		    return $x;

		}
		else
		{
		return 'Improper Conversion';
		}
	}

	/*
		Function: loadAssets
			Load elements css/js assets.

		Returns:
			Void
	*/
	public function loadAssets() {

		parent::loadAssets();
		$this->app->zlfw->zlux->loadMainAssets(true);
		$this->app->document->addScript('elements:measurespro/measurespro.js');
		return $this;
	}
}