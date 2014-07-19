<?php
/**
 * @package		ZOOcart
 * @author		ZOOlanders http://www.zoolanders.com
 * @copyright	Copyright (C) JOOlanders, SL
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<table class="uk-table uk-table-striped uk-table-condensed">
	<thead>
	<tr>
		<th><?php echo JText::_('PLG_ZLFRAMEWORK_ITEM_NAME'); ?></th>
		<th><?php echo JText::_('PLG_ZOOCART_SUBSCRIPTION'); ?></th>
		<th><?php echo JText::_('PLG_ZLFRAMEWORK_QUANTITY'); ?></th>
		<th><?php echo JText::_('PLG_ZLFRAMEWORK_UNIT_PRICE'); ?></th>
		<th><?php echo JText::_('PLG_ZLFRAMEWORK_TOTAL'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($items as $item) :?>
		<tr>
			<td><a href="<?php echo JRoute::_('index.php?option=com_zoo&view=item&task=edit&cid[]='.$item->getItem()->id);?>" target="_blank"><?php echo $item->getItem()->name; ?></a></td>
			<td>
				<?php $subs = $this->app->zoocart->table->subscriptions->getRelatedSubscription($item->order_id, $item->getItem()->id);
				if(!empty($subs)):?>
					<a href="<?php echo $this->app->zl->link(array('controller'=>'subscriptions', 'task'=>'edit', 'cid[]'=>$subs->id), false); ?>" class="zc-badge">Sub ID: <?php echo $subs->id; ?></a>
				<?php endif; ?>
			</td>
			<td><?php echo $item->quantity; ?></td>
			<td><?php echo $this->app->zoocart->currency->format($item->price); ?></td>
			<td><?php echo $this->app->zoocart->currency->format($item->price * $item->quantity); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
	<tfoot>
	<tr class="payment">
		<td colspan="4">
			<strong><?php echo JText::_('PLG_ZOOCART_PAYMENT_FEE'); ?></strong>
		</td>
		<td id="zoocart-cart-payment"><strong><?php echo $this->app->zoocart->currency->format($this->resource->payment);?></strong></td>
	</tr>
	<tr class="shipping">
		<td colspan="4">
			<strong><?php echo JText::_('PLG_ZOOCART_SHIPPING_FEE'); ?></strong>
		</td>
		<td id="zoocart-cart-shipping"><strong><?php echo $this->app->zoocart->currency->format($this->resource->shipping);?></strong></td>
	</tr>
	<tr class="subtotal">
		<td colspan="4">
			<strong><?php echo JText::_('PLG_ZLFRAMEWORK_SUBTOTAL'); ?></strong>
		</td>
		<td id="zoocart-cart-subtotal"><strong><?php echo $this->app->zoocart->currency->format($this->resource->getSubtotal());?></strong></td>
	</tr>
	<tr class="taxes">
		<td colspan="4">
			<strong><?php echo JText::_('PLG_ZOOCART_TAXES'); ?></strong>
		</td>
		<td id="zoocart-cart-taxes"><strong><?php echo $this->app->zoocart->currency->format($this->resource->getTaxTotal());?></strong></td>
	</tr>
	<tr class="total">
		<td colspan="4">
			<strong><?php echo JText::_('PLG_ZLFRAMEWORK_TOTAL'); ?></strong>
		</td>
		<td id="zoocart-cart-total"><strong><?php echo $this->app->zoocart->currency->format($this->resource->getTotal());?></strong></td>
	</tr>
	</tfoot>
</table>