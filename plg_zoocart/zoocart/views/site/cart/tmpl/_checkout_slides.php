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
$slides = array();
$settings = $this->app->zoocart->getConfig();
$enable_shipping = $settings->get('enable_shipping', true);
$require_address = $settings->get('require_address', 1);
$discounts = $settings->get('discounts_allowed', false);
$terms = $settings->get('accept_terms', 1);
$opened = $this->app->zoocart->getConfig()->get('checkout_opened', 1);

?>

<div id="zx-zoocart-checkout">

	<div data-zx-zoocart-slides>

		<!-- PREPARE THE SLIDES CONTENT -->

		<!-- address -->
		<?php if($require_address) : ?>
		<?php ob_start(); ?>

		<div class="uk-grid">

			<!-- billing -->
			<div class="uk-width-1-1 uk-width-medium-1-2">
				<div class="uk-text-primary uk-text-center uk-margin-bottom">
					<?php echo JText::sprintf('PLG_ZOOCART_CHECKOUT_STEP_INPUT_BILL_ADDRESS', 'uk-text-bold'); ?>
				</div>
				<?php echo $this->partial('checkout_addresses', array('type' => 'billing')); ?>
			</div>

			<!-- shipping -->
			<div class="uk-width-1-1 uk-width-medium-1-2">
				<div class="uk-text-primary uk-text-center uk-margin-bottom">
					<?php echo JText::sprintf('PLG_ZOOCART_CHECKOUT_STEP_INPUT_SHIP_ADDRESS', 'uk-text-bold'); ?>
				</div>
				<?php echo $this->partial('checkout_addresses', array('type' => 'shipping')); ?>
			</div>
		</div>

		<?php $slides[] = array(
			'id' => 'slide-address',
			'content' => ob_get_contents()
		); ?>
		<?php ob_end_clean(); ?>
		<?php endif; ?>

		<!-- shipping method -->
		<?php $slides[] = array(
			'id' => 'slide-shipping-method',
			'comment' => JText::sprintf('PLG_ZOOCART_CHECKOUT_STEP_CHOOSE_SHIP_METHOD', 'uk-text-bold'),
			'content' => $this->partial('checkout_shipping')
		); ?>

		<!-- payment method -->
		<?php $slides[] = array(
			'id' => 'slide-payment-method',
			'comment' => JText::sprintf('PLG_ZOOCART_CHECKOUT_STEP_CHOOSE_PAY_METHOD', 'uk-text-bold'),
			'content' => $this->partial('checkout_payment')
		); ?>

		<!-- confirmation -->
		<?php ob_start(); ?>
		<!-- resume TODO -->

		<!-- notes -->
		<form id="zoocart-notes-form" class="uk-form" name="zoocart-notes">
			<textarea rows="3" class="uk-width-large-1-1" name="notes" id="notes" placeholder="<?php echo JText::_('PLG_ZOOCART_CUSTOMER_NOTES_PLACEHOLDER');?>"></textarea>
		</form>

		<!-- terms -->
		<div class="uk-form">
			<label for="form_terms_conditions">
				<input type="checkbox" id="form_terms_conditions" name="terms" value="1" id="terms" required data-parsley-mincheck="1"/>
				<?php if ($url = $this->app->zoocart->getConfig()->get('terms_url', '')) : ?>
				<a href="<?php echo JRoute::_($url); ?>" target="_blank">
					<?php echo JText::_('PLG_ZOOCART_AGREE_TERMS'); ?> 
				</a>
				<?php else : ?>
				<?php echo JText::_('PLG_ZOOCART_AGREE_TERMS'); ?> 
				<?php endif; ?>
			</label>
		</div>

		<!-- checkout btn -->
		<button type="button" class="uk-button uk-button-success uk-margin-large-top uk-container-center uk-display-block">
			<i class="uk-icon-shopping-cart"></i>&nbsp;&nbsp;&nbsp;<?php echo JText::_('PLG_ZOOCART_CHECKOUT'); ?>
		</button>

		<?php $slides[] = array(
			'id' => 'slide-resume',
			'comment' => JText::sprintf('PLG_ZOOCART_CHECKOUT_STEP_PLACE_ORDER', 'uk-text-bold'),
			'content' => ob_get_contents()
		); ?>
		<?php ob_end_clean(); ?>


		<!-- RENDER THE SLIDES -->

		<!-- nav -->
		<ul class="zx-zoocart-slides-nav uk-subnav uk-subnav-pill uk-text-center" data-uk-switcher>
			<?php $i=1; foreach($slides as $key => $slide) : ?>
			<li class="<?php echo $key == 0 ? 'uk-active' : ''; ?>">
				<a href=""><?php echo $i; ?></a>
			</li>
			<?php $i++; endforeach; ?>
		</ul>

		<!-- slides -->
		<div class="zx-zoocart-slides-container">

			<!-- render the slides -->
			<?php foreach($slides as $slide) : ?>
			<div id="<?php echo $slide['id']; ?>" class="zx-zoocart-slides-slide">
				<div>
					<div class="uk-text-primary uk-text-center uk-margin-bottom">
					
						<?php if(isset($slide['comment'])) echo $slide['comment']; ?>
						
						<div class="zx-zoocart-slides-next uk-text-right">
							<button type="button" class="uk-button uk-button-mini uk-button-primary">
								<?php echo JText::sprintf('PLG_ZLFRAMEWORK_NEXT'); ?>
							</button>
						</div>
					</div>
					<div class="zx-zoocart-slides-slide-content">
						<?php echo $slide['content']; ?>
					</div>
					
				</div>
			</div>
			<?php endforeach; ?>

		</div>
	</div>

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
			'autoselect_shipping': '<?php echo $this->app->zoocart->getConfig()->get('autoselect_shipping', true); ?>'
		});
	});
	</script>

</div>