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
	Class: AdminResourceController
		The admin resource controller
*/
class AdminResourceController extends ResourceController {

	public function __construct($default = array()) {
		parent::__construct($default);

		// Register
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('saveandnew', 'save');

		// load assets
		$this->app->document->addScript('zoocart:assets/js/admin.js');
	}

	public function getView($name = '', $type = '', $prefix = '', $config = array()) {

		$view = parent::getView($name, $type, $prefix, $config);

		return $view;
	}

	public function display($cachable = false, $urlparams = false) 
	{
		parent::display($cachable, $urlparams);

		// init vars
		$JApp = $this->app->system->application;

		// set toolbar items
		JToolbarHelper::title(JText::_('PLG_ZOOCART_CONFIG_' . strtoupper($this->resource_name)));

		// set toolbar items
		$this->app->toolbar->addNew();
		$this->app->toolbar->editList();
		//$this->app->toolbar->custom('docopy', 'copy.png', 'copy_f2.png', 'Copy');
		$this->app->toolbar->deleteList();

		// table ordering and search filter
		$state_prefix       = $this->option.'.'.strtolower($this->getName());
		$filter_order	    = $JApp->getUserStateFromRequest($state_prefix.'.filter_order', 'filter_order', 'id', 'cmd');
		$filter_order_Dir   = $JApp->getUserStateFromRequest($state_prefix.'.filter_order_Dir', 'filter_order_Dir', 'desc', 'word');
		$this->lists['order_Dir'] = $filter_order_Dir;
		$this->lists['order']	  = $filter_order;

		$this->beforeListDisplay();

		// Display
		$this->getView()->setLayout('default')->display();
	}

	public function edit() {

		// disable menu
		$this->app->request->setVar('hidemainmenu', 1);

		// get request vars
		$cid  = $this->app->request->get('cid.0', 'int');
		$edit = $cid > 0;

		// get item
		if ($edit) {
			if (!$this->resource = $this->table->get($cid)) {
				$this->app->error->raiseError(500, JText::sprintf('PLG_ZOOCART_ERROR_UNABLE_ACCESS_RESOURCE', $cid));
				return;
			}
		} else {
			$this->resource = $this->app->object->create($this->resource_class);
		}

		JToolbarHelper::title(JText::_('PLG_ZOOCART_CONFIG_' . strtoupper($this->resource_name)) . ' <small><small>[ '.($edit ? JText::_('PLG_ZLFRAMEWORK_EDIT') : JText::_('PLG_ZLFRAMEWORK_NEW')).' ]</small></small>');
		$this->getEditToolbar();

		$this->beforeEditDisplay();

		// display view
		$this->getView()->setLayout('edit')->display();
	}

	protected function getEditToolbar() {

		$cid  = $this->app->request->get('cid.0', 'int');
		$edit = $cid > 0;

		// set toolbar items
		$this->app->toolbar->apply();
		$this->app->toolbar->save();
		$this->app->toolbar->custom('saveandnew', 'save-new', 'saveandnew', 'PLG_ZLFRAMEWORK_SAVE_AND_NEW', false);
		$this->app->toolbar->cancel('cancel', $edit ? 'PLG_ZLFRAMEWORK_CLOSE' : 'PLG_ZLFRAMEWORK_CANCEL');
		$this->app->zoo->toolbarHelp();
	}

	protected function beforeListDisplay() {

	}

	protected function beforeEditDisplay() {

	}

	public function save() {

		// check for request forgeries
		$this->app->zlfw->checkToken();

		$post = $this->app->request->get('post:', 'array', array());
		$cid        = $this->app->request->get('cid.0', 'int');
		$success = true;

		try {

			// get tax
			if ($cid) {
				$tax = $this->table->get($cid);
			} else {
				$tax = $this->app->object->create($this->resource_class);
			}

			// bind item data
			self::bind($tax, $post);

			// save item
			$this->table->save($tax);

			// set redirect message
			$msg = JText::_($this->resource_class . ' Saved');
			$success = true;

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseNotice(0, JText::sprintf('PLG_ZOOCART_ERROR_SAVING', $this->resource_class).' ('.$e.')');
			$this->_task = 'apply';
			$msg = null;
			$success = false;
		}

		if($this->app->zlfw->request->isAjax()){
			$response['message'] = $msg;
			$response['success'] = $success;
			echo json_encode($response);
			exit();
		}

		$link = $this->component->link(array('controller' => $this->controller),false);
		switch ($this->getTask()) {
			case 'apply' :
				$link .= '&task=edit&cid[]='.$tax->id;
				break;
			case 'saveandnew' :
				$link .= '&task=add';
				break;
		}

		$this->setRedirect($link, $msg);
	}

	protected function onAfterSave() {

	}

	public function remove() {

		// check for request forgeries
		$this->app->zlfw->checkToken();

		// init vars
		$cid = $this->app->request->get('cid', 'array', array());

		if (count($cid) < 1) {
			$this->app->error->raiseError(500, JText::_('PLG_ZOOCART_ERROR_SELECT_A_ITEM_TO_DELETE'));
		}

		try {

			// delete items
			foreach ($cid as $id) {
				$this->table->delete($this->table->get($id));
			}

			// set redirect message
			$msg = JText::_($this->resource_class . ' Deleted');

		} catch (AppException $e) {

			// raise notice on exception
			$this->app->error->raiseWarning(0, JText::_('PLG_ZOOCART_ERROR_DELETING', $this->resource_class).' ('.$e.')');
			$msg = null;

		}

		$this->setRedirect($this->component->link(array('controller' => $this->controller),false), $msg);
	}

	public function cancel(){

		$this->setRedirect($this->component->link(array('controller' => $this->controller),false), JText::_('PLG_ZOOCART_OPERATION_CANCELLED'));

	}

	/**
	 * Set the specified resource order. Ajax request
	 *
	 * @return json
	 */
	public function setResourceOrder()
	{
		// init vars
		$response = array('errors' => array(), 'notices' => array());
		$order = json_decode($this->app->request->get('order', 'string'));

		// Check if resource table contains "ordering" column:
		$success = $this->table->updateOrdering($order);

		if($success){
			$response['message'] = JText::_('PLG_ZOOCART_SUCCESS_ORDERING_CHANGE');
		}else{
			$response['errors'] = JText::_('PLG_ZOOCART_ERROR_ORDERING_CHANGE');
		}

		// set and return results
		$response['success'] = $success;
		echo json_encode($response);
		return;
	}

	/**
	 * Toggle the specified resource stat. Ajax request
	 *
	 * @return json
	 */
	public function toggleResourceState(){
		// init vars
		$success = false;
		$response = array('errors' => array(), 'notices' => array());
		$id = $this->app->request->get('id', 'int');

		if($id){
			$success = (bool)$this->table->toggleState($id);
			$response['new_state'] = $this->table->get($id)->published;
			$response['message'] = JText::_('PLG_ZLFRAMEWORK_MSG_STATUS_UPDATE');
		}

		// set and return results
		$response['success'] = $success;
		echo json_encode($response);
		return;
	}
}