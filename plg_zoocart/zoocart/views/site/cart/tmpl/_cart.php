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
$tbody = array();
$name_col_width = 'uk-width-7-10';
$settings = $this->app->zoocart->getConfig();
$this->display_desc = $this->display_quant = false;
$zoocart = $this->app->zoocart;

// get default billing address
$address = $this->app->zoocart->table->addresses->getDefaultAddress($this->user->id, 'billing');

// check if quantity should be displayed, it does if there is no subscription item type
foreach($this->cart->getItems() as $cartitem) {
	if (!$this->display_quant && $zoocart->getItemType($cartitem->getItem()) != 'subscription') $this->display_quant = true;
}

// first render the items in order to know if the description column should be displayed or not
foreach($this->cart->getItems() as $cartitem) {
	$tbody[] = $this->renderer->render('item.cart', array('view' => $this, 'item' => $cartitem->getItem(), 'cartitem' => $cartitem));
}

// get name column width
if($this->display_desc && $this->display_quant) {
	$name_col_width = 'uk-width-4-10';
} else if (!$this->display_desc && !$this->display_quant) {
	$name_col_width = 'uk-width-8-10';
} else if($this->display_desc) {
	$name_col_width = 'uk-width-5-10';
} else if($this->display_quant) {
	$name_col_width = 'uk-width-7-10';
}

// get shipping fee
$shipping_fee = 0;
if ($settings->get('autoselect_shipping', true) && count($this->shipping_rates) == 1) {
	foreach ($this->shipping_rates as $plugin => $rates) {
		if (count($rates) == 1) {
			$rate = array_shift($rates);
			$shipping_fee = $rate['price'];
			$this->app->zoocart->cart->sumFee($shipping_fee);
		} else break;
	}
}

?>

<div id="zx-zoocart-cart" class="uk-margin-large-bottom">

	<h3 class="title"><?php echo JText::_('PLG_ZOOCART_YOUR_CART'); ?></h3>

	<form class="uk-form" action="<?php echo $this->component->link(); ?>" method="post" name="zoocart-cart-form" accept-charset="utf-8" onsubmit="return false;">

		<!-- cart items -->
		<table class="uk-table uk-table-condensed zx-zoocart-items-table zx-zoocart-cart-items">
			<thead class="uk-hidden-small">
				<tr>
					<th class="uk-grid uk-grid-small uk-grid-preserve">
						<div class="uk-width-7-10">&nbsp;</div> <!-- necessary space -->
						<?php if($this->display_quant) : ?>
						<div class="uk-width-1-10 uk-text-center">
							<span data-uk-tooltip title="<?php echo JText::_('PLG_ZOOCART_CART_TIP_ITEM_QUANTITY'); ?>">
								<?php echo JText::_('PLG_ZLFRAMEWORK_QUANTITY'); ?>
							</span>
						</div>
						<?php endif; ?>
						<?php if((int)$settings->get('show_weights')): ?>
						<div class="uk-width-1-10"><?php echo JText::_('PLG_ZOOCART_WEIGHT'); ?></div>
						<?php endif; ?>
						<div class="uk-width-1-10 uk-text-right">
							<span data-uk-tooltip title="<?php echo JText::_('PLG_ZOOCART_CART_TIP_ITEM_UNIT_PRICE'); ?>">
								<?php echo JText::_('PLG_ZLFRAMEWORK_PRICE'); ?>
							</span>
						</div>
						<div class="uk-width-1-10 uk-text-right">
							<span data-uk-tooltip title="<?php echo JText::_('PLG_ZOOCART_CART_TIP_ITEM_TOTAL_PRICE'); ?>">
								<?php echo JText::_('PLG_ZLFRAMEWORK_TOTAL'); ?>
							</span>
						</div>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php echo implode("\n", $tbody); ?>
			</tbody>
		</table>

		<!-- taxes notice -->
		<div class="uk-text-right uk-text-small">
			<?php $text = $this->app->zoocart->tax->checkTaxEnabled() ? 'PLG_ZOOCART_SALES_TAXES_INCLUDED' : 'PLG_ZOOCART_SALES_TAXES_NOT_INCLUDED';
				echo JText::_($text); ?>
		</div>

		<!-- fees / discounts -->
		<div class="zx-zoocart-cart-extras">

			<!-- shipping fee -->
			<div class="zx-zoocart-cart-totals-shippingfee uk-panel" data-uk-tooltip title="<?php echo JText::_('PLG_ZOOCART_CART_TIP_SHIPPING_FEE'); ?>">
				<span class="uk-panel-title">
					<?php echo JText::_('PLG_ZOOCART_SHIPPING_FEE'); ?>
				</span>
				<span data-value>
					<?php echo $zoocart->currency->format(0); ?>
				</span>
			</div>

			<!-- payment fee -->
			<div class="zx-zoocart-cart-totals-paymentfee uk-panel" data-uk-tooltip title="<?php echo JText::_('PLG_ZOOCART_CART_TIP_PAYMENT_FEE'); ?>">
				<span class="uk-panel-title">
					<?php echo JText::_('PLG_ZOOCART_PAYMENT_FEE'); ?>
				</span>
				<span data-value>
					<?php echo $zoocart->currency->format(0); ?>
				</span>
			</div>

			<!-- weights -->
			<?php if((int)$settings->get('show_weights')) : $value = $this->cart->getTotalWeight(); ?>
			<div class="uk-panel">
				<span class="uk-panel-title">
					<?php echo JText::_('PLG_ZOOCART_TOTAL_WEIGHT'); ?>
				</span>
				<span data-value="<?php echo $value; ?>">
					<?php echo $zoocart->currency->format($value); ?>
				</span>
			</div>
			<?php endif; ?>

			<!-- discount -->
			<?php if($zoocart->getConfig()->get('discounts_allowed', false)) : ?>
			<div class="zx-zoocart-cart-totals-discounts uk-panel" data-uk-tooltip title="<?php echo JText::_('PLG_ZOOCART_CART_TIP_DISCOUNTS'); ?>">
				<span class="uk-panel-title">
					<?php echo JText::_('PLG_ZOOCART_DISCOUNTS'); ?>
				</span>
				<span data-value>
					-<?php echo $zoocart->currency->format(0); ?>
				</span>
			</div>
			<?php endif; ?>

		</div>

		<!-- totals -->
		<div class="zx-zoocart-items-table-totals">

			<!-- subtotal -->
			<?php $value = $this->cart->getSubtotal(); ?>
			<div class="zx-zoocart-items-table-totals-subtotal uk-grid">
				<div class="uk-width-5-6 uk-panel-title">
					<span data-uk-tooltip title="<?php echo JText::_('PLG_ZOOCART_CART_TIP_SUBTOTAL'); ?>">
						<?php echo JText::_('PLG_ZLFRAMEWORK_SUBTOTAL'); ?>
					</span>
				</div>
				<div class="uk-width-1-6" data-value="<?php echo $value; ?>">
					<?php echo $zoocart->currency->format($value); ?>
				</div>
			</div>

			<!-- taxes -->
			<?php $value = $zoocart->cart->getTaxes($this->app->user->get()->id, $address); ?>
			<div class="zx-zoocart-items-table-totals-taxes uk-grid">
				<div class="uk-width-5-6 uk-panel-title">
					<span data-uk-tooltip title="<?php echo JText::_('PLG_ZOOCART_CART_TIP_TAXES'); ?>">
						<?php echo JText::_('PLG_ZOOCART_TAXES'); ?>
					</span>
				</div>
				<div class="uk-width-1-6" data-value="<?php echo $value; ?>">
					<?php echo $zoocart->currency->format($value); ?>
				</div>
			</div>

			<!-- total -->
			<?php $value = $zoocart->cart->getTotal($this->app->user->get()->id, $address); ?>
			<div class="zx-zoocart-items-table-totals-total uk-grid">
				<div class="uk-width-5-6 uk-panel-title">
					<span data-uk-tooltip title="<?php echo JText::_('PLG_ZOOCART_CART_TIP_TOTAL'); ?>">
						<?php echo JText::_('PLG_ZLFRAMEWORK_TOTAL'); ?>
					</span>
				</div>
				<div class="uk-width-1-6" data-value="<?php echo $value; ?>">
					<?php echo $zoocart->currency->format($value); ?>
				</div>
			</div>

		</div>

		<!-- discounts -->
		<?php if($settings->get('discounts_allowed', false)) : ?>
		<div class="zx-zoocart-cart-discounts">
			<div class="uk-text-left uk-form zx-zoocart-cart-coupon">
				<div class="uk-form-icon">
					<i class="uk-icon-ticket"></i>
					<input type="text" class="uk-form-blank uk-form-width-medium" name="coupon_code" value="" placeholder="<?php echo JText::_('PLG_ZOOCART_COUPON_INPUT_INSTRUCTIONS'); ?>" />
					<a href="" class="zx-zoocart-cart-coupon-remove uk-hidden"><i class="uk-icon-times"></i></a>
				</div>
				<div class="zx-zoocart-cart-coupon-report uk-text-small"></div>
			</div>
		</div>
		<?php endif; ?>
	</form>

	<script type="text/javascript">
	jQuery(document).ready(function($){
		// add js language strings
		$.zx.lang.push({
			"ZC_CART_UPDATED": "<?php echo JText::_('PLG_ZOOCART_SUCCESS_CART_UPDATED') ?>"
		});

		$('#zx-zoocart-cart').zx('zoocartCart');
	});
	</script>
</div>