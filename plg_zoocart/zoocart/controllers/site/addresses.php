<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/*
	Class: AddressesController
		The controller class for addresses
*/
class AddressesController extends SiteResourceController {

	public function __construct($default = array()) {

		$this->resource_name = 'addresses';

		$this->resource_class = 'Address';

		parent::__construct($default);
	}

	/**
	 * Display method override
	 *
	 * @param bool $cachable
	 * @param bool $urlparams
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// make sure user is logued in
		if(!$this->app->user->get()->id){
			$this->setRedirect($this->app->link(array('option'=>'com_users','view'=>'login','return'=>base64_encode(JURI::current())),false),'PLG_ZOOCART_ERROR_LOGIN_REQUIRED','notice');
			return;
		}

		// get Joomla application
		$this->joomla = $this->app->system->application;

		// get addresses
		$this->resources = array('billing' => array(), 'shipping' => array());
		foreach($this->getResources() as $address) {
			$this->resources[$address->type][] = $address;
		}

		// display
		$this->getView()->setLayout('default')->display();
	}

	/**
	 * edit
	 */
	public function edit()
	{		
		// get request vars
		$id  = $this->app->request->get('id', 'int');
		$user = $this->app->user->get();
		$access = $this->app->user->isJoomlaAdmin($user) || $this->app->user->isJoomlaSuperAdmin($user);
		$edit = $id > 0;
		$error = false;
		$mtype = 'message';

		// get item
		if ($edit) {
			if (!$this->resource = $this->table->get($id)) {
				$msg = JText::sprintf('PLG_ZOOCART_ERROR_UNABLE_ACCESS_RESOURCE', $id);
				$mtype = 'error';
				$error = true;
			}
		} else {
			$this->resource = $this->app->object->create($this->resource_class);
		}

		if($this->resource->user_id!=$user->id && !$access)
		{
			$this->app->error->raiseError(403, JText::_('PLG_ZOOCART_ADDRESS_CANNOT_EDIT'));
			return;
		}

		if (!$error && $edit && $this->resource->user_id != $this->app->user->get()->id) {
			$msg = JText::_('PLG_ZOOCART_ADDRESS_CANNOT_EDIT');
			$mtype = 'error';
			$error = true;
		}

		if (!$error) {
			// display view
			$this->getView()->setLayout('edit')->display();
		} else {
			$this->setRedirect($this->component->link(array('controller' => $this->controller, 'task' => 'display')), $msg,$mtype);
		}
	}

	/**
	 * save
	 */
	public function save()
	{
		// check for request forgeries
		$this->app->zlfw->checkToken();

		// init vars
		$success = true;
		$response = array('errors' => array(), 'notices' => array());
		$id = $this->app->request->get('id', 'integer', null);
		$type = $this->app->request->getString('type', 'billing');
		$post = $this->app->request->get('post:', 'array');
		$user_id = $this->app->user->get()->id;

		// get address
		if ($id) {
			$address = $this->table->get($id);
		} else {
			$address = $this->app->request->get('elements', 'array');
			$address = $this->app->zoocart->address->getFromValues($address, $type);
		}

		// check user
		if (!empty($address->user_id) && $address->user_id != $user_id) {
			$response['errors'][] = JText::_('PLG_ZOOCART_ERROR_CANNOT_SAVE_ADDRESS');
			$response['success'] = false;
			echo json_encode($response);
			return;
		}
		
		// make sure user is set
		$address->user_id = $user_id ? $user_id : 0;

		// validate
		$validated = $this->app->zoocart->address->validate($address, $type);
		$success = $success && $validated['success'];
		$response = array_merge_recursive($response, $validated);

		if($success){

			// bind
			self::bind($address, $post);
			
			// save
			$this->table->save($address);

			// reset the elements data structure
			$address->elements = json_decode($address->elements);
			$address->elements = $this->app->data->create($address->elements);

			// prepare the renderer
			$renderer = $this->app->renderer->create('address')->addPath(array($this->app->path->path('component.site:'), $this->app->path->path('zoocart:')));
			$renderer->setItem($address);
			$renderer->setLayout($address->type);

			// get title
			$title = trim($renderer->renderPosition('name', array('style' => 'default')));

			// get preview
			$preview = $renderer->render('address.' . $address->type, array('item' => $address));

			// if no title, use preview
			if(!strlen($title)) {
				$title = strip_tags($preview);
			}

			// set address data
			$response['address']['title'] = $title;
			$response['address']['preview'] = $preview;
			$response['address']['id'] = $address->id;
			
		} else {
			$response['errors'][] = JText::_('PLG_ZOOCART_ERROR_CANNOT_SAVE_ADDRESS');
		}

		echo json_encode($response);
		return;
	}

	/**
	 * validateVat
	 */
	public function validateVat()
	{
		// check for request forgeries
		$this->app->zlfw->checkToken();

		// init vars
		$success = true;
		$response = array('errors' => array(), 'notices' => array());
		$country = $this->app->request->getString('country');
		$vat = $this->app->request->getString('vat');

		// validate vat
		$response['vies'] = $this->app->zoocart->tax->isValidVat($country, $vat);

		// return
		echo json_encode($response);
	}

	/**
	 * validateAddress
	 */
	public function validateAddress()
	{
		// check for request forgeries
		$this->app->zlfw->checkToken();

		// init vars
		$type = $this->app->request->getString('type');

		// get address
		$address = $this->app->request->get('elements', 'array');
		$address = $this->app->zoocart->address->getFromValues($address, $type);

		if($type == 'billing') {
			
			// validate address
			$response = $this->app->zoocart->address->validate($address, $type);

			// set if EU
			$response['address']['country']['isEU'] = isset($address->country) ? $this->app->country->isEU($address->country) : false;

			// validate vat
			if(isset($address->country) && isset($address->vat)) {
				$response['address']['vat']['vies'] = $this->app->zoocart->tax->isValidVat($address->country, $address->vat);
			}

			// return
			echo json_encode($response);
		
		} else {
			echo json_encode($this->app->zoocart->address->validate($address, $type));
		}
	}

	/**
	 * remove
	 */
	public function remove()
	{
		// check for request forgeries
		$this->app->zlfw->checkToken();

		$id = $this->app->request->getInt('id');
		$error = false;
		$msg = JText::_('PLG_ZOOCART_ADDRESS_DELETED');
		
		if (!$id) {
			$msg = JText::_('PLG_ZOOCART_EMPTY_ID_NOT_ALLOWED');
			$error = true;
		}

		$address = $this->table->get($id);
		if (!$address) {
			$msg = JText::sprintf('PLG_ZOOCART_ERROR_UNABLE_ACCESS_RESOURCE', $id);
			$error = true;
		}

		if ($address->user_id != $this->app->user->get()->id) {
			$msg = JText::_('PLG_ZOOCART_ERROR_CANNOT_DELETE_ADDRESS');
			$error = true;
		}

		if ($address->default) {
			$msg = JText::_('PLG_ZOOCART_ERROR_CANNOT_DELETE_DEFAULT_ADDRESS');
			$error = true;
		}

		if (!$error) {
			$this->table->delete($address);
		}

		// return result
		echo json_encode(array(
			'success' => !$error,
			'msg' => $msg
		));
	}

	/**
	 * setDefault
	 */
	public function setDefault()
	{
		// check for request forgeries
		$this->app->zlfw->checkToken();

		// init vars
		$success = true;
		$response = array('errors' => array(), 'notices' => array());
		$id = $this->app->request->getInt('id');

		// check id
		if (!$id) {
			$response['errors'][] = JText::_('PLG_ZOOCART_EMPTY_ID_NOT_ALLOWED');
			$response['success'] = false;
			echo json_encode($response);
			return;
		}

		// get address
		$address = $this->table->get($id);

		// check address
		if (!$address) {
			$response['errors'][] = JText::sprintf('PLG_ZOOCART_ERROR_UNABLE_ACCESS_RESOURCE', $id);
			$response['success'] = false;
			echo json_encode($response);
			return;
		}

		// check user
		if ($address->user_id != $this->app->user->get()->id) {
			$response['errors'][] = JText::_('PLG_ZOOCART_ADDRESS_CANNOT_EDIT');
			$response['success'] = false;
			echo json_encode($response);
			return;
		}
		
		// proceede
		$default_address = $this->table->getDefaultAddress($this->app->user->get()->id, $address->type);
		$default_address->default = 0;
		$this->table->save($default_address);
		$address->default = 1;
		$this->table->save($address);

		// response
		$response['success'] = $success;
		echo json_encode($response);
	}

	/**
	 * getAddressForm
	 */
	public function getAddressForm()
	{
		// check for request forgeries
		$this->app->zlfw->checkToken();
		
		$type = $this->app->request->get('type', 'string', 'billing');
		$id = $this->app->request->get('id', 'integer', null);

		// render layout
		$response['html'] = $this->app->zlfw->renderLayout($this->app->path->path('zoocart:views/site/addresses/tmpl/_addresses_form.php'), compact('type', 'id'));

		$response['success'] = true;

		echo json_encode($response);
		return;
	}
}

/*
	Class: AddressesControllerException
*/
class AddressesControllerException extends AppException {}