<?php
/**
 * @package     ZOOcart
 * @author      ZOOlanders http://www.zoolanders.com
 * @copyright   Copyright (C) JOOlanders, SL
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Class InformerHelper
 * Implements backend tips system to help users configure zoocart
 */
class zoocartInformerHelper extends AppHelper {

    /**
     * @var array Array of internal notifications
     */
    protected $_messages = array();

    /**
     * @var null Current application object
     */
    protected $_application = null;

    /**
     * @var Flag for Print Once
     */
    protected static $_once = false;

	/**
	 * @var Side: admin/site
	 */
	protected $_admin = false;

	/**
     * Class instance constructor
     *
     * @param App $app
     * @internal param \Application $Object object
     * @return \InformerHelper
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->_application = $this->app->zoo->getApplication();
	    $this->_admin = JFactory::getApplication()->isAdmin();
        $this->checkCfg();

        return $this;
    }

    /**
     * Pop out first tip from message queue
     *
     * @return string Tip message
     */
    public function popout($area='default')
    {
        $msg = '';

        if(!empty($this->_messages[$area]))
            $msg = array_shift($this->_messages[$area]);

        if('print_once'==$area)
        {
            if(self::$_once)
                return;

            $joomla = JFactory::getApplication();
            $joomla->enqueueMessage($msg,'notice');

            self::$_once = true;
            return;
        }

        return $msg;
    }

    /**
     * Add new tip message to stack
     *
     * @param $message
     * @return object
     */
    public function enqueue($message, $area='default')
    {
        if($message)
            $this->_messages[$area][] = $message;

        return $this;
    }

    /**
     * Check configuration
     */
    public function checkCfg()
    {   $billing_addr_layouts = array('billing','billing-form');
        $shipping_addr_layouts = array('shipping','shipping-form');
        $item_layouts = array('order','cart');
        $types = array();

	    $mod = $this->_admin?'':'_SITE';

        // Check type config:

        $apps = $this->app->table->application->all();
        if(!empty($apps))
        {
            foreach($apps as $app)
            {
               $types = array_merge($types, $app->getTypes());
            }
        }

        if(!empty($types))
        {
            $qty_check = ($this->app->zoocart->getConfig()->get('check_quantities') || $this->app->zoocart->getConfig()->get('update_quantities'))?false:true;
            $price_check = false;
            $atc_check = false;
            $required_elements = array();

            $price_elements_count = 0;
            $quantity_elements_count = 0;
            $multielem_types = array();
            $reliable_types = array();

            foreach($types as $type)
            {
                // check Price Pro
                $price_elements = $type->getElementsByType('pricepro');
                $price_check = $price_check || !empty($price_elements);
                $price_elements_count = count($price_elements);

                // check Quantity element, only if required
                if(!$qty_check)
                {
                    $quantity_elements = $type->getElementsByType('quantity');
                    $qty_check = !empty($quantity_elements);
                    $quantity_elements_count = count($quantity_elements);
                }

                // check Add To Cart
                $addtocart_elements = $type->getElementsByType('addtocart');
                $atc_check = $atc_check || !empty($addtocart_elements);

                // Check if type has more than one zoocart elements:
                if(($price_elements_count>1) || ($quantity_elements_count>1))
                {
                    $multielem_types[] = '<b>'.ucfirst($type->identifier).'</b>';
                }

                if(!empty($price_elements) || !empty($quantity_elements) || !empty($addtocart_elements))
                    $reliable_types[] = $type;

            }

            // Set list or required elements:
            if(!$price_check)
                $required_elements[] = '<b>'.JText::_('PLG_ZOOCART_INFORMER'.$mod.'_PRICEPRO').'</b>';
            if(!$qty_check)
                $required_elements[] = '<b>'.JText::_('PLG_ZOOCART_INFORMER'.$mod.'_QUANTITYPRO').'</b>';
            if(!$atc_check)
                $required_elements[] = '<b>'.JText::_('PLG_ZOOCART_INFORMER'.$mod.'_ADDTOCART').'</b>';

            if(!$price_check || !$atc_check){
                $this->enqueue(sprintf(JText::_('PLG_ZOOCART_INFORMER'.$mod.'_SET_TYPE_CFG'),implode(', ',$required_elements),$this->app->link(array('controller'=>'manager'),false)));
            }
        }

        // Check tax classes:
        $tc = $this->app->zoocart->table->taxclasses->all();
        if(empty($tc)){
            $this->enqueue(sprintf(JText::_('PLG_ZOOCART_INFORMER'.$mod.'_SET_TAXCLASSES'),$this->app->zl->link(array('controller'=>'taxclasses'),false)));
        }

        // Check currencies:
        $cc = $this->app->zoocart->table->currencies->all();
        if(empty($cc)){
            $this->enqueue(sprintf(JText::_('PLG_ZOOCART_INFORMER'.$mod.'_SET_CURRENCIES'),$this->app->zl->link(array('controller'=>'currencies'),false)));
        }

	    // Check for SOAP extension:
	    if($this->app->zoocart->getConfig()->get('vies_validation',0)){
		    if(!(extension_loaded('soap') && class_exists('SoapClient')))
			    $this->enqueue(JText::_('PLG_ZOOCART_INFORMER'.$mod.'_SOAP_NOT_ENABLED'));
	    }

        // Check default currency:
        if(!$this->app->zoocart->getConfig()->get('default_currency',0))
            $this->enqueue(sprintf(JText::_('PLG_ZOOCART_INFORMER'.$mod.'_SET_DEFAULT_CURRENCY'),$this->app->zl->link(array('controller'=>'zoocart'),false)));

        // Check payment plugins installed and enabled:
        $pplugins = JPluginHelper::isEnabled('zoocart_payment');
        if(empty($pplugins)){
            $this->enqueue(sprintf(JText::_('PLG_ZOOCART_INFORMER'.$mod.'_SET_PAYMENT_PLUGINS'),JRoute::_('index.php?option=com_installer'),JRoute::_('index.php?option=com_plugins')));
        }

        if($this->app->zoocart->getConfig()->get('require_address',1))
        {
            // Check address type configured
            $address = $this->app->zoocart->address->getAddressType();
            $addr = $address->getElements();
            if(empty($addr)){
                $this->enqueue(sprintf(JText::_('PLG_ZOOCART_INFORMER'.$mod.'_SET_ADDRESS'),$this->app->zl->link(array('controller'=>'addresses','task'=>'editelements','cid[]'=>'address'),false)));
            }

            // Check address layouts configured:
            $renderer = $this->app->renderer->create('address')->addPath(JPATH_ROOT.'/plugins/system/zoocart/zoocart');
            $layouts = $billing_addr_layouts;

            if($this->app->zoocart->getConfig()->get('enable_shipping', true))
                $layouts = array_merge($layouts,$shipping_addr_layouts);
            $lc = false;

            foreach($layouts as $layout)
            {
                $config = $renderer->getConfig('address')->get('address.'.$layout);
                $lc = $lc || empty($config);
            }

            if($lc){
                $this->enqueue(sprintf(JText::_('PLG_ZOOCART_INFORMER'.$mod.'_SET_ADDRESS_LAYOUTS'),$this->app->zl->link(array('controller'=>'addresses'),false)));
            }
        }

        // Check shipping plugins installed and enabled and shipping rates configured on shipping option is ON:
        if($this->app->zoocart->getConfig()->get('enable_shipping', true)){
            // Shipping plugins check:
            $splugins = JPluginHelper::isEnabled('zoocart_shipping');
            if(empty($splugins)){
                $this->enqueue(sprintf(JText::_('PLG_ZOOCART_INFORMER'.$mod.'_SET_SHIPPING_PLUGINS'),JRoute::_('index.php?option=com_installer'),JRoute::_('index.php?option=com_plugins')));
            }

            // Shipping rates check:
            $sr = $this->app->zoocart->table->shippingrates->all();
            if(empty($sr)){
                $this->enqueue(sprintf(JText::_('PLG_ZOOCART_INFORMER'.$mod.'_SET_SHIPPING_RATES'),$this->app->zl->link(array('controller'=>'shippingrates'),false)));
            }
        }
    }

    /**
     * Check if Zoocart enabled
     */
    public function checkEnabled()
    {
        $app_id = $this->_application->id;
        return $this->app->zoocart->getConfig($app_id)->get('enable_cart');
    }
}