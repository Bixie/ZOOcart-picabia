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
$this->display_desc = $this->display_quant = false;
$zoocart = $this->app->zoocart;

// check if quantity should be displayed, it does if there is no subscription item type
foreach($items as $orderitem) {
	if (!$this->display_quant && $zoocart->getItemType($orderitem->getItem()) != 'subscription') $this->display_quant = true;
}

// first render the items in order to know if the description column should be displayed or not
foreach($items as $orderitem) {
	$tbody[] = $this->partial('item', array('orderitem' => $orderitem));
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

?>

<div id="zx-zoocart-order" class="uk-margin-large-bottom">

	<!-- order items -->
	<table class="uk-table uk-table-condensed zx-zoocart-items-table">
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

	<!-- totals -->
	<div class="zx-zoocart-items-table-totals">

		<!-- payment fee -->
		<?php if(!empty($this->resource->payment)) : ?>
		<div class="zx-zoocart-items-table-totals-paymentfee uk-grid">
			<div class="uk-width-5-6 uk-panel-title">
				<span data-uk-tooltip title="<?php echo JText::_('PLG_ZOOCART_CART_TIP_PAYMENT_FEE'); ?>">
					<?php echo JText::_('PLG_ZOOCART_PAYMENT_FEE'); ?>
				</span>
			</div>
			<div class="uk-width-1-6">
				<?php echo $zoocart->currency->format($this->resource->payment);?>
			</div>
		</div>
		<?php endif; ?>

		<!-- shipping fee -->
		<?php if(!empty($this->resource->shipping)) : ?>
		<div class="zx-zoocart-items-table-totals-shippingfee uk-grid">
			<div class="uk-width-5-6 uk-panel-title">
				<span data-uk-tooltip title="<?php echo JText::_('PLG_ZOOCART_CART_TIP_SHIPPING_FEE'); ?>">
					<?php echo JText::_('PLG_ZOOCART_SHIPPING_FEE'); ?>
				</span>
			</div>
			<div class="uk-width-1-6">
				<?php echo $zoocart->currency->format($this->resource->shipping); ?>
			</div>
		</div>
		<?php endif; ?>

		<!-- subtotal -->
		<div class="zx-zoocart-items-table-totals-subtotal uk-grid">
			<div class="uk-width-5-6 uk-panel-title">
				<span data-uk-tooltip title="<?php echo JText::_('PLG_ZOOCART_CART_TIP_SUBTOTAL'); ?>">
					<?php echo JText::_('PLG_ZLFRAMEWORK_SUBTOTAL'); ?>
				</span>
			</div>
			<div class="uk-width-1-6">
				<?php echo $zoocart->currency->format($this->resource->getSubtotal()); ?>
			</div>
		</div>

		<!-- discounts -->
		<?php if(!empty($this->resource->discount)) : ?>
		<div class="zx-zoocart-items-table-totals-discounts uk-grid">
			<div class="uk-width-5-6 uk-panel-title">
				<span data-uk-tooltip title="<?php echo JText::_('PLG_ZOOCART_CART_TIP_DISCOUNTS'); ?>">
					<?php echo JText::_('PLG_ZOOCART_DISCOUNTS'); ?>
				</span>
			</div>
			<div class="uk-width-1-6" data-value="<?php echo $value; ?>">
				<?php echo $zoocart->currency->format($this->resource->discount); ?>
			</div>
		</div>
		<?php endif; ?>

		<!-- taxes -->
		<div class="zx-zoocart-items-table-totals-taxes uk-grid">
			<div class="uk-width-5-6 uk-panel-title">
				<span data-uk-tooltip title="<?php echo JText::_('PLG_ZOOCART_CART_TIP_TAXES'); ?>">
					<?php echo JText::_('PLG_ZOOCART_TAXES'); ?>
				</span>
			</div>
			<div class="uk-width-1-6">
				<?php echo $this->app->zoocart->currency->format($this->resource->getTaxTotal()); ?>
			</div>
		</div>

		<!-- total -->
		<div class="zx-zoocart-items-table-totals-total uk-grid">
			<div class="uk-width-5-6 uk-panel-title">
				<span data-uk-tooltip title="<?php echo JText::_('PLG_ZOOCART_CART_TIP_TOTAL'); ?>">
					<?php echo JText::_('PLG_ZLFRAMEWORK_TOTAL'); ?>
				</span>
			</div>
			<div class="uk-width-1-6">
				<?php echo $this->app->zoocart->currency->format($this->resource->getTotal()); ?>
			</div>
		</div>

	</div>

</div>