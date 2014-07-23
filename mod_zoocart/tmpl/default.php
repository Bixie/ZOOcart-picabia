<?php
/**
* @package		ZOOcart Module
* @author    	ZOOlanders http://www.zoolanders.com
* @copyright 	Copyright (C) JOOlanders SL
* @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// init vars
$user = JFactory::getUser();

// get cart items
$items = $zoo->zoocart->table->cartitems->getByUser($zoo->user->get()->id);

?>

<div class="zoocart-smallcart">

	<!-- items -->
	<?php if ($items): ?>

		<!-- cart items -->
		<?php if ($params->find('layout.show_items', true)) : ?>
			<div class="zoocart-smallcart-items">

				<?php foreach ($items as $item): ?>
				<div class="zoocart-smallcart-item">
					<a href="<?php echo $zoo->route->item($item->getItem()); ?>"><?php echo $item->getItem()->name; ?></a> x<?php echo $item->quantity; ?>
				</div>
				<?php endforeach; ?>

				<div class="zoocart-smallcart-prices" hidden>
					<hr />
	
					<!-- totals -->
					<strong><?php echo JText::_('PLG_ZLFRAMEWORK_SUBTOTAL'); ?>:</strong> <?php 
						echo $zoo->zoocart->currency->format($zoo->zoocart->cart->getSubtotal());
					?><br />
					<strong><?php echo JText::_('PLG_ZOOCART_TAXES'); ?>:</strong> <?php 
						echo $zoo->zoocart->currency->format($zoo->zoocart->cart->getTaxes());
					?><br />
					<strong><?php echo JText::_('PLG_ZLFRAMEWORK_TOTAL'); ?>:</strong> <?php 
						echo $zoo->zoocart->currency->format($zoo->zoocart->cart->getTotal());
					?>

				</div>
			</div>
			<hr />
		<?php endif; ?>

		<!-- cart links -->
		<?php if($params->find('layout.show_cart_link', true)): ?>
			<a href="<?php echo $zoo->zl->route->zoocart->cart(); ?>"><?php echo JText::_('PLG_ZOOCART_VIEW_CART_CHECKOUT'); ?></a><br />
		<?php endif; ?>

	<!-- if cart empty -->
	<?php else: ?>
		<div class="zoocart-empty-cart">
		<?php echo JText::_('PLG_ZOOCART_EMPTY_CART'); ?>
		</div>
	<?php endif; ?>

	<!-- orders links -->
	<?php if($params->find('layout.show_orders_link', true) && !$user->guest): ?>
		<a href="<?php echo $zoo->zl->route->zoocart->orders(); ?>"><?php echo JText::_('PLG_ZOOCART_ORDER_MY_ORDERS'); ?></a><br />
	<?php endif; ?>

	<!-- addresses link -->
	<?php if($params->find('layout.show_addresses_link', true) && !$user->guest): ?>
		<a href="<?php echo $zoo->zl->route->zoocart->addresses(); ?>"><?php echo JText::_('PLG_ZOOCART_ADDRESS_MY_ADDRESSES'); ?></a><br />
	<?php endif; ?>

</div>