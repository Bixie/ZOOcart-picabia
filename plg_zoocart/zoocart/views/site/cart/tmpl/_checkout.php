<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// init vars
$panels = array();
$enable_shipping = $this->app->zoocart->getConfig()->get('enable_shipping', true);
$require_address = $this->app->zoocart->getConfig()->get('require_address', 1);
$terms = $this->app->zoocart->getConfig()->get('accept_terms', 1);

// load assets
$this->app->document->addScript('zlfw:vendor/zlux/js/addons/animate.js');

?>

<!-- Address -->
<?php if($require_address) : ?>

	<!-- billing -->
	<?php $panels['address-billing']['title'] = JText::sprintf('PLG_ZOOCART_CHECKOUT_STEP_INPUT_BILL_ADDRESS', 'uk-text-bold'); ?>
	<?php $panels['address-billing']['content'] = $this->partial('checkout_addresses', array('type' => 'billing')); ?>

	<!-- shipping -->
	<?php $panels['address-shipping']['title'] = JText::sprintf('PLG_ZOOCART_CHECKOUT_STEP_INPUT_SHIP_ADDRESS', 'uk-text-bold'); ?>
	<?php $panels['address-shipping']['content'] = $this->partial('checkout_addresses', array('type' => 'shipping')); ?>
	
<?php endif; ?>

<!-- Shipping -->
<?php if($enable_shipping) : ?>
	<?php $panels['shippings']['title'] = JText::sprintf('PLG_ZOOCART_CHECKOUT_STEP_CHOOSE_SHIP_METHOD', 'uk-text-bold'); ?>
	<?php $panels['shippings']['content'] = $this->partial('checkout_shipping', array('shipping_rates' => $this->shipping_rates)); ?>
<?php endif; ?>

<!-- Payment -->
<?php $panels['payments']['title'] = JText::sprintf('PLG_ZOOCART_CHECKOUT_STEP_CHOOSE_PAY_METHOD', 'uk-text-bold'); ?>
<?php $panels['payments']['content'] = '<div class="zx-zoocart-checkout-payment uk-text-center">'.$this->partial('fieldset_payment').'</div>'; ?>

<!-- Notes -->
<?php $panels['notes']['title'] = JText::sprintf('PLG_ZOOCART_CHECKOUT_STEP_NOTES', 'uk-text-bold'); ?>
<?php ob_start(); ?>
<div class="uk-form">
	<textarea rows="2" class="uk-width-large-1-1" name="notes"></textarea>
</div>
<?php $panels['notes']['content'] = ob_get_contents(); ob_end_clean(); ?>

<!-- Terms -->
<?php if($terms) : ?>
	<?php $panels['terms']['title'] = JText::sprintf('PLG_ZOOCART_CHECKOUT_STEP_TERMS', 'uk-text-bold'); ?>
	<?php $panels['terms']['content'] = $this->partial('checkout_terms'); ?>
<?php endif; ?>

<!-- render -->
<div id="zx-zoocart-checkout">

	<!-- panels -->
	<?php $i=0; $total = count($panels); foreach($panels as $name => $panel) : ?>

		<?php if(!($i & 1)) /* odd */ echo '<div class="uk-grid" data-uk-grid-match="{target:\'.uk-panel\'}">'; ?>
		
			<?php $width_class = ($i+1 == $total && ($total & 1)) ? '1-1' : '1-2'; ?>
			<div class="<?php echo $name; ?> uk-width-large-<?php echo $width_class; ?>">
				<div class="uk-panel uk-panel-box uk-panel-header zx-zoocart-checkout-fieldset">
					<div class="uk-text-primary uk-margin-bottom uk-text-center zx-zoocart-checkout-fieldset-title">
						<?php echo $panel['title']; ?>
					</div>
					<?php echo $panel['content']; ?>
				</div>
			</div>

		<?php if($i & 1 || $i == $total - 1) /* even */ echo '</div>'; ?>
		
	<?php $i++; endforeach; ?>

	<!-- checkout button -->
	<button type="button" class="uk-button uk-button-success uk-float-right uk-margin-top zx-zoocart-checkout-placeorder">
		<i class="uk-icon-shopping-cart"></i>&nbsp;&nbsp;&nbsp;<?php echo JText::_('PLG_ZOOCART_CHECKOUT'); ?>
	</button>

	<!-- script -->
	<script type="text/javascript">
	jQuery(document).ready(function($){
		// add js language strings
		$.zx.lang.push({
			"ZC_TERMS_REQUIRED": "<?php echo JText::_('PLG_ZOOCART_TERMS_REQUIRED') ?>",
			"ZC_PAYMENT_METHOD_APPLIED": "<?php echo JText::_('PLG_ZOOCART_SUCCESS_PAYMENT_METHOD_APPLIED') ?>",
			"ZC_SHIPPING_METHOD_APPLIED": "<?php echo JText::_('PLG_ZOOCART_SUCCESS_SHIPPING_METHOD_APPLIED') ?>"
		});

		// init checkout script
		$('#zx-zoocart-checkout').zx('zoocartCheckout', {
			'taxesAddressType': '<?php echo $this->app->zoocart->getConfig()->get('billing_address_type'); ?>',
			'enable_shipping': '<?php echo $this->app->zoocart->getConfig()->get('enable_shipping', true); ?>',
			'autoselect_shipping': '<?php echo $this->app->zoocart->getConfig()->get('autoselect_shipping', true); ?>'
		});
	});
	</script>

</div>