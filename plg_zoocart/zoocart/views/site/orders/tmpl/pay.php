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
$this->app->document->addScript('zlfw:vendor/zlux/js/addons/notify.js');
$this->app->document->addScript('zoocart:assets/js/zoocart.js');

// set renderer
$this->address_renderer = $this->app->renderer->create('address')->addPath(array($this->app->path->path('component.site:'), $this->app->path->path('zoocart:')));

// init vars
$this->resource = $this->order;
$require_address = $this->app->zoocart->getConfig()->get('require_address', 1);

// get payment
$payment_plugin = $this->app->zoocart->payment->getPaymentPlugins($this->order->payment_method);
$payment_params = $this->app->data->create($payment_plugin->params);
$pname = strlen($payment_params->get('title')) ? $payment_params->get('title') : ucfirst($this->order->payment_method);

// get shipping
if($this->order->shipping_method != 'null') { // why is null a string? TODO, check this
	$shipping_plugin = $this->app->zoocart->shipping->getShippingPlugins(json_decode($this->order->shipping_method)->plugin);
	$shipping_params = $this->app->data->create($shipping_plugin->params);
	$sname = strlen($shipping_params->get('title')) ? $shipping_params->get('title') : ucfirst(json_decode($this->order->shipping_method)->name);
}

?>

<div id="zoocart-container" class="zx">

	<!-- Payment form -->
	<?php echo $this->payment_html; ?>

	<!-- Title -->
	<h2><?php echo JText::_('PLG_ZOOCART_ORDER_SUMMARY').' #'. $this->order->id; ?></h2>
	<hr />
		
	<!-- Items -->
	<?php echo $this->partial('items', array('items' => $this->order->getItems())); ?>

	<!-- Address -->
	<?php if($require_address):?>
	<div class="addresses uk-grid" data-uk-grid-match="{target:'.uk-panel'}">
		<div class="billing-details uk-width-1-2">
			<div class="uk-panel uk-panel-box uk-panel-header">
				<h3 class="uk-panel-title"><?php echo JText::_('PLG_ZOOCART_ADDRESS_BILLING');?></h3>
				<?php $address = $this->resource->getBillingAddress(); ?>
				<div data-address-id="<?php echo $address->id; ?>">
					<?php echo $this->address_renderer->render('address.billing', array('item' => $address)); ?>
				</div>
			</div>
		</div>

		<div class="shipping-details uk-width-1-2">
			<div class="uk-panel uk-panel-box uk-panel-header">
				<h3 class="uk-panel-title"><?php echo JText::_('PLG_ZOOCART_ADDRESS_SHIPPING');?></h3>
				<?php $address = $this->resource->getShippingAddress(); ?>
				<div data-address-id="<?php echo $address->id; ?>">
					<?php echo $this->address_renderer->render('address.billing', array('item' => $address)); ?>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<!-- Payment/Shipping -->
	<?php $width = ($this->order->shipping_method && $this->order->shipping_method != 'null') ? 5 : 10; ?>
	<div class="payment-shipping uk-grid" data-uk-grid-match="{target:'.uk-panel'}">
		<div class="payments uk-width-<?php echo $width ?>-10">
			<div class="uk-panel uk-panel-box uk-panel-header">
				<h3 class="uk-panel-title"><?php echo JText::_('PLG_ZOOCART_PAYMENT_METHOD');?></h3>
				<span class="payment-name"><?php echo $pname; ?></span>
			</div>
		</div>

		<?php if ($this->order->shipping_method && $this->order->shipping_method != 'null') : ?>
		<div class="shippings uk-width-1-2">
			<div class="uk-panel uk-panel-box uk-panel-header">
				<h3 class="uk-panel-title"><?php echo JText::_('PLG_ZOOCART_SHIPPING_METHOD');?></h3>
				<span class="shipping-name"><?php echo $sname; ?></span>
			</div>
		</div>
		<?php endif; ?>
	</div>
	
</div>