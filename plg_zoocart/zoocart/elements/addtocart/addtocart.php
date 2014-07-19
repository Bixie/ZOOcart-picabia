<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class ElementAddtocart extends ElementPro {

	/*
		Function: getSearchData
			Get repeatable elements search data.

		Returns:
			String - Search data
	*/
	public function getSearchData() {
		return false;
	}

	/*
		Function: hasValue
			Override. Checks if the element's value is set.

		Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		$value = $this->get('value', $this->config->find('specific._default'));
		return !empty($value);
	}

	/*
		Function: edit
			Renders the repeatable edit form field.

		Returns:
			String - html
	*/
	public function edit() {

		if ($layout = $this->getLayout('edit/edit.php')) {
			return $this->renderLayout($layout,
				array(
					'default' => $this->config->find('specific._default'),
					'value' => $this->get('value', $this->config->find('specific._default'))
				)
			);
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
		return $this;
	}
	
	/*
		Function: render
			Renders the element.

		Parameters:
			$params - AppData render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {
		$params = $this->app->data->create($params);

		// render layout
		if ($layout = $this->getLayout('render/' . $params->find('layout._layout', 'default.php'))) {
			return $this->renderLayout($layout, compact('params'));
		}
	}
	
	/*
		Function: getConfigForm
			Get parameter form object to render input form.

		Returns:
			Parameter Object
	*/
	public function getConfigForm() {
		
		$form = parent::getConfigForm();
		$form->addElementPath($this->app->path->path( 'zoocart:fields'));

		return $form;
	}

	/*
		Function: renderSubmission
			Renders the element in submission.

		Parameters:
			$params - submission parameters

		Returns:
			String - html
	*/
	public function renderSubmission($params = array())
	{
		return $this->edit();
	}
}