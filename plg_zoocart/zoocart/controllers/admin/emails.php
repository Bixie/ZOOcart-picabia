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
 * Class EmailsController
 * Operating with zoocart email templates
 */
class EmailsController extends AdminResourceController {

	/**
	 * Class constructor
	 *
	 * @param array $default
	 */
	public function __construct($default = array()) {

		$this->resource_name = 'emails';
		$this->resource_class = 'Email';

		parent::__construct($default);

		// Override default add action handler:
		$this->registerTask('add', 'add');
	}

    /**
     * Save function overrride
     */
    public function save(){

        // check for request forgeries
	    $this->app->zlfw->checkToken();
	    $success = false;

        $post = $this->app->request->get('post:', 'array', array());
        $cid        = $this->app->request->get('cid.0', 'int');

        try {

            // get object
            if ($cid) {
                $email = $this->table->get($cid);
            } else {
	            $email = $this->app->object->create($this->resource_class);
            }

            $template = JRequest::getVar('template', '', 'default', 'string', JREQUEST_ALLOWRAW );
            $post['template'] = $template;

            // bind item data
            self::bind($email, $post);

            // save item
            $this->table->save($email);
	        $success = true;

		        // set redirect message
            $msg = JText::_($this->resource_class . ' Saved');

        } catch (AppException $e) {

            // raise notice on exception
            $this->app->error->raiseNotice(0, JText::sprintf('PLG_ZOOCART_ERROR_SAVING', $this->resource_class).' ('.$e.')');
            $this->_task = 'apply';
            $msg = null;
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
                $link .= '&task=edit&cid[]='.$email->id;
                break;
            case 'saveandnew' :
                $link .= '&task=add';
                break;
        }

        $this->setRedirect($link, $msg);
    }

	/**
	 * Edit email template action
	 */
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

			// Check data from the request:
			$this->resource->type = $this->app->request->get('type','string', $this->app->zoocart->email->getDefaultType());
			$this->resource->language = $this->app->request->get('language', 'string', JLanguageHelper::detectLanguage());

			// Load appropriate template
			$tmpl = $this->app->zoocart->email->loadTemplate($this->resource->type, $this->resource->language);

			$this->resource->subject = $tmpl->subject;
			$this->resource->template = $tmpl->body;

		}

		JToolbarHelper::title(JText::_('PLG_ZOOCART_CONFIG_' . strtoupper($this->resource_name)) . ' <small><small>[ '.($edit ? JText::_('PLG_ZLFRAMEWORK_EDIT') : JText::_('PLG_ZLFRAMEWORK_NEW')).' ]</small></small>');
		$this->getEditToolbar();

		$this->beforeEditDisplay();

		// display view
		$this->getView()->setLayout('edit')->display();
	}

	/**
	 * Add new record pre-initialization
	 */
	public function add(){
		// set toolbar items
		JToolbarHelper::title(JText::_('PLG_ZOOCART_CONFIG_' . strtoupper($this->resource_name)).' ['.JText::_('PLG_ZOOCART_NEW_EMAIL').']');

		// set toolbar items
		$this->app->toolbar->cancel();

		// Display
		$this->getView()->setLayout('addnew')->display();
	}
}

/**
 * Class EmailsControllerException
 */
class EmailsControllerException extends AppException {}