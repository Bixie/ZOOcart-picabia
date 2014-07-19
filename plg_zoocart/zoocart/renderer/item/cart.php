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
$zoocart = $this->app->zoocart;
$item_type = $zoocart->getItemType($item);

// get unit price
if ($zoocart->tax->checkTaxEnabled()) {
	$price = $cartitem->getGrossPrice();
} else {
	$price = $cartitem->getNetPrice();
}

$settings = $this->app->zoocart->getConfig();

?>

<tr data-id="<?php echo $cartitem->id; ?>" class="zx-zoocart-cart-item">
	<td class="uk-grid uk-grid-small uk-grid-preserve">
		<div class="zx-zoocart-cart-item-name uk-width-small-1-1 uk-width-medium-7-10">

			<!-- Remove [small viewport] -->
			<a href="#" class="zx-zoocart-cart-item-remove" data-uk-tooltip title="<?php echo JText::_('PLG_ZOOCART_CART_TIP_REMOVE_ITEM'); ?>">
				<i class="uk-icon-times"></i>
			</a>

			<!-- Name -->
			<div>
				<?php if ($this->checkPosition('name')) : ?>
					<?php echo $this->renderPosition('name'); ?>
				<?php else : ?>
					<?php echo $item->name; ?>
				<?php endif; ?>

				<?php if ($item_type == 'subscription' || $this->checkPosition('variations')) : ?>
				<div class="cartitem-details">

					<!-- Subscription -->
					<?php if ($item_type == 'subscription' && !empty($cartitem->subscription)) : ?>
						<?php $subs = json_decode($cartitem->subscription); ?>
						<span class="uk-button uk-button-mini subscription-data uk-display-inline-block">
							<?php echo JText::sprintf('PLG_ZOOCART_SUBSCRIPTION_LINE', $subs->duration); ?>
						</span>
					<?php endif;?>

					<!-- Variations -->
					<?php if ($this->checkPosition('variations')) : ?>
						<?php echo $this->renderPosition('variations'); ?>
					<?php endif; ?>

				</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="uk-width-small-1-1 uk-width-medium-1-10">

			<!-- Quantity -->
			<?php if ($view->display_quant) : ?>
			<div class="zx-zoocart-cart-item-quantity uk-text-center">

				<!-- [small viewport] -->
				<div class="uk-visible-small">
					<a href="#"><i class="uk-icon-minus-square-o uk-icon-small"></i></a>
					<span class="uk-margin-small-left uk-margin-small-right">2x 236,00€</span>
					<a href="#"><i class="uk-icon-plus-square-o uk-icon-small"></i></a>
				</div>

				<!-- ![small viewport] -->
				<div class="uk-hidden-small" data-uk-tooltip title="<?php echo JText::_('PLG_ZOOCART_CART_TIP_INPUT_QUANTITY'); ?>">
					<!-- subscription don't have quantity property, displaying 1 by default -->
					<?php if ($item_type == 'subscription') : ?>1<?php else : ?>
						<input type="number" min="1" class="uk-form-width-mini uk-form-small uk-form-blank uk-text-center" name="items[<?php echo $cartitem->id; ?>][quantity]" value="<?php echo $cartitem->quantity;?>">
					<?php endif; ?>

				</div>
			</div>
			<?php endif; ?>

		</div>

		<!-- Weight -->
		<?php if($settings->get('show_weights')) : $weight = $cartitem->getWeight(); ?>
		<div class="zx-zoocart-cart-item-weight" data-value="<?php echo $weight; ?>">
			<?php echo $weight; ?>
		</div>
		<?php endif; ?>

		<!-- Unit price -->
		<div class="zx-zoocart-cart-item-price uk-hidden-small uk-width-medium-1-10 uk-text-right" data-value="<?php echo $price; ?>">
			<?php echo $zoocart->currency->format($price); ?>
		</div>

		<!-- Total -->
		<?php $total = $price * $cartitem->quantity; ?>
		<div class="zx-zoocart-cart-item-totalprice uk-hidden-small uk-width-medium-1-10 uk-text-right" data-value="<?php echo $total; ?>">
			<?php echo $zoocart->currency->format($total); ?>
		</div>

		<!-- Total [small viewport] -->
		<div class="uk-visible-small uk-width-small-1-1 uk-text-center">
		472,00€
		</div>
	</td>

	<!-- Description - if assigned enable description display -->
	<?php if ($this->checkPosition('description')) : if (!$view->display_desc) $view->display_desc = true; ?>
	<td class="description">
		<?php echo $this->renderPosition('description'); ?>
	</td>
	<?php endif; ?>

</tr>