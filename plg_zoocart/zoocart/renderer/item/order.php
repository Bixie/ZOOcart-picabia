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

?>

<tr data-orderitem-id="<?php echo $orderitem->id; ?>">

	<!-- Name -->
	<td class="name">
		<?php if ($this->checkPosition('name')) : ?>
			<?php echo $this->renderPosition('name'); ?>
		<?php else : ?>
			<?php echo $orderitem->name; ?>
		<?php endif; ?>

		<?php if ($item_type == 'subscription' || $this->checkPosition('variations')) : ?>
		<div class="cartitem-details uk-margin-small-left">

			<!-- Subscription -->
			<?php if ($item_type == 'subscription' && !empty($orderitem->subscription)) : ?>
				<?php $subs = json_decode($orderitem->subscription); ?>
				<small>
					<?php echo JText::sprintf('PLG_ZOOCART_SUBSCRIPTION_LINE', $subs->duration) .
						($this->checkPosition('variations') ? ', ' : ''); ?>
				</small>
			<?php endif;?>

			<!-- Variations -->
			<?php if ($this->checkPosition('variations')) : ?>
				<?php echo $this->renderPosition('variations'); ?>
			<?php endif; ?>

		</div>
		<?php endif; ?>
	</td>

	<!-- Description - if assigned enable description display -->
	<?php if ($this->checkPosition('description')) : if (!$view->display_desc) $view->display_desc = true; ?>
	<td class="description">
		<?php echo $this->renderPosition('description'); ?>
	</td>
	<?php endif; ?>

	<!-- Quantity -->
	<?php if ($view->display_quant) : ?>
	<td class="quantity uk-text-center">
		<?php echo $orderitem->quantity; ?>
	</td>
	<?php endif; ?>

	<!-- Unit price -->
	<td class="price uk-text-right">
		<?php
		if ($this->app->zoocart->getConfig()->get('show_price_with_tax', 1)) {
			// total includes taxes and dividing we can get the unit_price with taxes
			$price = $orderitem->total / $orderitem->quantity;
		} else {
			$price = $orderitem->price;
		}

		echo $this->app->zoocart->currency->format($price);
		?>
	</td>

	<!-- Total -->
	<td class="price-total uk-text-right">
		<?php
		if ($this->app->zoocart->getConfig()->get('show_price_with_tax', 1)) {
			$price = $orderitem->total;
		} else {
			$price = $orderitem->total - $orderitem->tax;
		}

		echo $this->app->zoocart->currency->format($price);
		?>
	</td>
</tr>