<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// load assets
$this->app->document->addStylesheet('zoocart:assets/css/site.css');

// init vars
$link = $this->component->link(array('controller' => 'orders', 'task' => 'view', 'id' => $this->order_id));

?>

<div id="zoocart-container" class="zx">
	<div class="alert"><?php echo JText::_('PLG_ZOOCART_ORDER_CANCEL_MESSAGE'); ?></div>
	<a class="uk-button uk-button-primary uk-button-small" href="<?php echo $link; ?>"><?php echo JText::_('PLG_ZOOCART_GO_TO_ORDER'); ?></a>
</div>