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
   Class: ElementVariations
   The Variations element class
*/
class ElementVariations extends ElementRepeatablePro implements iSubmittable {
	
	/* The fieldset object */
	protected $_fieldset;

	/*
	   Function: Constructor
	*/
	public function __construct() {

		// call parent constructor
		parent::__construct();

		// register Fieldset class
		$this->app->loader->register('Fieldset', "classes:fieldset.php");

		// and path
		if($path = $this->app->path->path('elements:variations/fieldsets')) {
			$this->app->path->register($path, 'fieldsets');
		}

		// set callbacks
		$this->registerCallback('getElementEdit');
		$this->registerCallback('getVariationsContent');
	}

	/*
		Function: getAttrVariants
			Get an matrix array of all posible attributes options combinations

		Parameters:
			$attributes - Array
	*/
	public function getAttrVariants($attributes = null)
	{
		// get the attributes
		$attributes = $attributes ? $attributes : $this->config->find('specific.attributes', array());

		// prepare the rows
		$rows = array();
		foreach ($attributes as $attr) {
			$row = array();
			foreach ($attr['options'] as $option) {
				$row[] = $option['value'];
			}
			$rows[] = $row;
		}

		// get combinations
		$results = array();
		foreach($rows as $row){
			$new_result = array();
			if(empty($results)){
				foreach($row as $key=>$val){
					$new_result[$val] = false;
				}
			} else{
				foreach($results as $key=>$val){
					foreach($row as $value){
						$new_result[$key.'.'.$value] = false;
					}
				}
			}
			$results = $new_result;
		}

		return $results;
	}

	/*
		Function: valuesToInputs
			Convert passed values to inputs
		
		Returns:
			Array
	*/
	function valuesToInputs($values, $ctrl='')
	{
		$fields = array();
		if(count($values)) foreach($values as $key => $val)
		{
			if(is_array($val)) {
				$fields = array_merge($fields, $this->valuesToInputs($val, $ctrl.'['.$key.']'));
			} else if(strlen($val)) {
				$fields[] = '<input type="hidden" name="'.$ctrl.'['.$key.']" value="'.$val.'">';
			}			
		}
		return $fields;
	}

	/*
		Function: getElementEdit
			Get element edit layout

		Returns:
			String - Layout HTML
	*/
	public function getElementEdit($identifier, $index=null)
	{
		// init vars
		$script = '';
		$dom = new DOMDocument();

		// create a dump item object to avoid altering the current one
		$item = $this->app->object->create('Item');
		$item->application_id = $this->getItem()->application_id;
		$item->type = $this->getItem()->getType()->id;

		// set element to the right index
		if($index) $this->seek($index);

		// set element data
		$item->elements->set($identifier, $this->get($identifier));

		// get element
		$element = $item->getElement($identifier);

		// get content
		if(@$dom->loadHTML($element->edit()))
		{
			// save to common var and remove from doc any script
			while (($r = $dom->getElementsByTagName("script")) && $r->length) {
				$script .= $r->item(0)->nodeValue;
				$r->item(0)->parentNode->removeChild($r->item(0));
			}

			// remove HTML doc parts and save
			$html = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $dom->saveHTML()));

			// replace the element div id
			$html = str_replace('id="'.$identifier.'"', 'id="'.$identifier.'_variation_'.$this->index().'"', $html);

			// replace the script element div id
			$script = str_replace('#'.$identifier, '#'.$identifier.'_variation_'.$this->index(), $script);

			// replace the fields name values
			$html = str_replace(
				'elements['.$identifier.']',
				$this->getControlName($identifier),
				$html
			);

			$response['success'] = true;
			$response['html'] = $html;
			$response['script'] = $script;
			return json_encode($response);
		}
	}

	/*
		Function: getIndexByVariations
			Find the Variations related element instance

		Params
			$variations JSON or Array

		Returns:
			Int - The instance index
	*/
	public function getIndexByVariations($variations)
	{
		$variations = is_string($variations) ? json_decode($variations, true) : $variations;

		// iterate over element data
		$this->rewind();
		foreach($this as $self) {
			$inst_hash = md5(serialize(array_filter($this->get('attributes', array()))));
			$vari_hash = md5(serialize($variations));
			
			// if there are variations for the passed set of attributes values
			if($inst_hash == $vari_hash) {
				return $this->index();
			}
		}

		return false;
	}

	/*
		Function: applyVariations
			Override the Item Data with the Variations one

		Params
			$variations JSON or Array

		Returns:
			Array - The updated elements ids
	*/
	public function applyVariations($variations = null)
	{
		// if no variation use default
		if ($variations == null && !$variations = $this->getDefaultVariation()) {
			return false;
		}

		// init vars
		$variations = is_string($variations) ? json_decode($variations, true) : $variations;
		$item = $this->getItem();
		$overrided_elements = array();
		$index = $this->getIndexByVariations($variations);

		if ($index !== false) {
		
			// for each overrided data
			foreach($item->elements->find($this->identifier.'.'.$index) as $id => $data) {

				// skip attribute data
				if($id == 'attributes') continue;

				// override item content with variations
				$item->elements->set($id, $data);

				// set variation data to the item as reference
				$item->variations = json_encode($variations);

				// add element id
				$overrided_elements[] = $id;
			}

			return $overrided_elements;
		}

		return false;
	}

	/*
		Function: getFilteredAttributes
			Get the attributes with options not used filtered out

		Returns:
			Array - The filtered attributes
	*/
	public function getFilteredAttributes()
	{
		// get available attributes
		$attributes = $this->config->find('specific.attributes', array());

		// filter out the not used attr options
		foreach ($attributes as &$attr) {
			if (isset($attr['options'])) foreach ($attr['options'] as $key => $option) {

				$used = false;
				// iterate each instance data
				foreach ($this->getItem()->elements->get($this->identifier, array()) as $value) {

					// check if is is used
					if (isset($value['attributes']) && in_array($option['value'], $value['attributes'])) {
						$used = true;
						continue 2; // is used, skip current option check
					}
				}
				
				// if not used, remove it
				if (!$used) unset($attr['options'][$key]);
			}
		}

		return $attributes;
	}

	/*
		Function: getDefaultVariation
			Get the default variation

		Returns:
			Array - The default variation
	*/
	public function getDefaultVariation()
	{
		$variations = array();
		foreach ($this->_item->elements->get($this->identifier, array()) as $value) {
			if (isset($value['default']) && isset($value['attributes'])) {
				$variations = $value['attributes'];
				break;
			}
		}

		// if not default chosen use first instance
		if (empty($variations) && $this->_item->elements->find("{$this->identifier}.0", false)) {
			$variations = $this->_item->elements->find("{$this->identifier}.0.attributes", array());
		}

		return $variations;
	}

	/*
		Function: getVariationsContent
			Get the Variations rendered content

		Returns:
			String - Layout HTML
	*/
	public function getVariationsContent($layout, $attrs)
	{
		// init vars
		$item = $this->getItem();

		// apply variations
		$this->applyVariations($attrs);

		// get all overriding elements
		$overrided_elements = $this->config->find('specific.elements', array());
		foreach($this->getItem()->getElementsByType('pricepro') as $element)
			$overrided_elements[] = $element->identifier;
		foreach($this->getItem()->getElementsByType('quantity') as $element)
			$overrided_elements[] = $element->identifier;

		// get content
		$response = array();
		$dom = new DOMDocument();
		$content = $this->app->zlfw->renderView($item, $layout);
		if(@$dom->loadHTML('<meta http-equiv="content-type" content="text/html; charset=utf-8">'.$content))
		{
			// find zoocart content
			$xpath = new DOMXPath($dom);
			foreach($xpath->query("//*[@data-zoocart-hash]") as $node) {
				if(in_array($node->getAttribute('data-zoocart-id'), $overrided_elements)) {
					$response['variations'][$node->getAttribute('data-zoocart-hash')] = $dom->saveXML($node);
				}
			}
		}

		$response['success'] = true;
		return json_encode($response);
	}

	/*
		Function: _hasValue
			Checks if the repeatables element's file is set

		Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	protected function _hasValue($params = array())
	{
		if ($fieldset = $this->getFieldset()) {
			return $fieldset->hasValue($params);
		} else {
			return false;
		}
	}

	/*
	   Function: edit
		   Renders the edit form field.

	   Returns:
		   String - html
	*/
	public function edit()
	{
		// render layout
		if ($layout = $this->getLayout('edit/edit.php')){
			return $this->renderLayout($layout);
		}
	}

	/*
	   Function: _edit
		   Renders the repeatable edit form field.

	   Returns:
		   String - html
	*/
	protected function _edit()
	{
		// render layout
		if ($fieldset = $this->getFieldset()) {
			return $fieldset->edit();
		}
	}

	/*
		Function: _getSearchData
			Get repeatable elements search data.

		Returns:
			String - Search data
	*/
	protected function _getSearchData() {}
	
	/*
		Function: getRenderedValues
			renders the element content

		Returns:
			array
	*/
	public function getRenderedValues($params=array(), $mode=false, $opts=array()) 
	{
		// get results
		$result = parent::getRenderedValues($params, $mode, $opts);
	
		if (empty($result)) return null; // if no results abort
		
		return $result;
	}

	/*
		Function: _render
			Renders the repeatable element.

	   Parameters:
			$params - AppData render parameter

		Returns:
			String - html
	*/
	protected function _render($params = array())
	{
		// render layout or value
		$main_layout = $params->find('layout._layout', 'default.php');
		if($layout = $this->getLayout('render/'.$main_layout)){
			return $this->renderLayout($layout, compact('params'));
		}
	}

	/*
		Function: loadAssets
			Load elements css/js assets.

		Returns:
			Void
	*/
	public function loadAssets()
	{
		parent::loadAssets();

		// ZLUX
		$this->app->zlfw->zlux->loadMainAssets(true);

		// element assets
		$this->app->document->addScript('elements:repeatablepro/repeatablepro.js');
		$this->app->document->addStylesheet('elements:variations/assets/css/style.css');
		$this->app->document->addScript('elements:variations/assets/js/script.js');
		
		return $this;
	}

	/*
		Function: renderSubmission
			Renders the element in submission.

	   Parameters:
			$params - AppData submission parameters

		Returns:
			String - html
	*/
	public function renderSubmission($params = array()) {
		$this->loadAssets($params);
		return $this->_renderRepeatable('_renderSubmission', $params);
	}
	
	/*
		Function: _renderSubmission
			Renders the element in submission.

	   Parameters:
			$params - submission parameters

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
		// validate if class well registered
		if ($fieldset = $this->getFieldset()) {
			return $fieldset->validateSubmission($values, $params);
		}
	}

	/*
		Function: getFieldset
			Get an fieldset from the element

	   Parameters:
			$type - String The fieldset type

		Returns:
			Fieldset - The fieldset requested
	*/
	public function getFieldset()
	{
		if(!$this->_fieldset) {
			$this->_fieldset = $this->createFieldset('variation');
		}

		return $this->_fieldset;
	}

	/*
		Function: createFieldset
			Creates fieldset of given type

	   Parameters:
			$type - String The type to create

		Returns:
			Fieldset - The created fieldset
	*/
	public function createFieldset($type)
	{
		// load fieldset class
		$fieldsetClass = 'Fieldset'.$type;
		if (!class_exists($fieldsetClass)) {
			$this->app->loader->register($fieldsetClass, "fieldsets:$type/$type.php");
		}

		if (!class_exists($fieldsetClass)) {
			return false;
		}

		$testClass = new ReflectionClass($fieldsetClass);

		if ($testClass->isAbstract()) {
			return false;
		}

		return new $fieldsetClass($this->app, $this);
	}
}