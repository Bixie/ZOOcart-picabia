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

// set address renderer
$this->address_renderer = $this->app->renderer->create('address')->addPath(array($this->app->path->path('component.site:'), $this->app->path->path('zoocart:')));

// init vars
$settings = $this->app->zoocart->getConfig();
$config = $this->app->zoocart->getConfig();
$yet_to_pay = !$this->app->zoocart->order->isPayed($this->resource);
$require_address = $settings->get('require_address', 1);

// get order state
switch ($this->resource->getState()->id) {
	case $config->get('payment_received_orderstate'): 
	case $config->get('finished_orderstate'): 
			$suffix = 'success';
			break;
	case $config->get('payment_failed_orderstate'): 
	case $config->get('canceled_orderstate'): 
			$suffix = 'danger'; 
			break;
	case $config->get('payment_pending_orderstate'): 
			$suffix = 'warning'; 
			break;
	default: $suffix = 'default';
			break;
}

?>

<div id="zoocart-container" class="zx">

	<?php echo $this->partial('informer'); ?>

	<!-- Pay button  -->
	<?php if ($yet_to_pay) : ?>
	<a class="uk-float-right uk-button uk-button-success uk-button-mini zx-zoocart-order-pay" href="<?php echo $this->component->link(array('controller' => 'orders', 'task' => 'pay', 'id' => $this->resource->id)); ?>">
		<i class="uk-icon-money"></i>
		<?php echo JText::_('PLG_ZOOCART_ORDER_PAY'); ?>
	</a>
	<?php endif;?>

	<!-- History button -->
	<a href="#zx-zoocart-order-history" class="uk-float-right uk-button uk-button-mini uk-margin-right" data-uk-modal>
		<i class="uk-icon-history"></i>
		<?php echo JText::_('PLG_ZOOCART_ORDER_HISTORY'); ?>
	</a>

	<!-- Title -->
	<h2>	
		<?php echo JText::_('PLG_ZOOCART_ORDER').' #'. $this->resource->id; ?>
		<!-- State badge -->
		<span class="uk-badge uk-badge-<?php echo $suffix; ?>">
			<?php echo JText::_($this->resource->getState()->name); ?>
		</span>
	</h2>

	<hr />
		
	<!-- Items -->
	<?php echo $this->partial('items', array('items' => $this->resource->getItems())); ?>

	<!-- Addresses  -->
	<?php if($require_address) : ?>
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

	<!-- Payment/Shipping methods -->
	<div class="uk-grid" data-uk-grid-match="{target:'.uk-panel'}">

		<!-- payment -->
		<div class="uk-width-1-2">
			<div class="uk-panel uk-panel-box uk-panel-header">

				<!-- name -->
				<h3 class="uk-panel-title"><?php echo JText::_('PLG_ZOOCART_PAYMENT_METHOD');?></h3>
				
				<!-- fieldset -->
				<?php if($yet_to_pay) : ?>
				<div class="zx-zoocart-order-payment uk-text-center">
					<?php echo $this->partial('fieldset_payment', array('selected' => $this->resource->payment_method)); ?>
				</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- shipping -->
		<?php if ($settings->get('enable_shipping', false) && 
					$this->resource->shipping_method && $this->resource->shipping_method != 'null') : ?>
		<div class="uk-width-1-2">
			<div class="uk-panel uk-panel-box uk-panel-header">
				<h3 class="uk-panel-title"><?php echo JText::_('PLG_ZOOCART_SHIPPING_METHOD');?></h3>
				<span class="shipping-name"><?php echo json_decode($this->resource->shipping_method)->name; ?></span>
			</div>
		</div>
		<?php endif; ?>
	</div>

	<!-- Modal - Order History -->
	<div id="zx-zoocart-order-history" class="uk-modal">
		<div class="uk-modal-dialog">
			<a class="uk-modal-close uk-close"></a>

			<!-- content -->
			<h3><?php echo JText::_('PLG_ZOOCART_ORDER_HISTORY'); ?></h3>
			<table class="uk-table uk-table-bordered">
				<thead>
				<tr>
					<th><?php echo JText::_('PLG_ZLFRAMEWORK_DATE'); ?></th>
					<th><?php echo JText::_('PLG_ZOOCART_CHANGES'); ?></th>
					<th><?php echo JText::_('PLG_ZLFRAMEWORK_USER'); ?></th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td><?echo $this->app->html->_('date', $this->resource->created_on, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset()); ?></td>
					<td>
						<ul class="uk-list uk-margin-bottom-remove">
							<li><?php echo JText::_('PLG_ZOOCART_ORDER_CREATED'); ?></li>
						</ul>
					</td>
					<td>
						<?php echo $this->app->user->get($this->resource->user_id)->username; ?>
					</td>
				</tr>
				<?php
				$histories = $this->app->zoocart->table->orderhistories->getGroupedByTime($this->resource->id);
				foreach($histories as $history) :?>
					<tr>
						<?php
						// Retrieve all related records:
						$sids = explode(',', $history->records);
						$stories = array();
						if(!empty($sids)){
							foreach($sids as $sid){
								$stories[] = $this->app->zoocart->table->orderhistories->get($sid);
							}
						}
						$date = date("d.m.Y H:i:s", $history->timestamp);
						?>
						<td><?echo $this->app->html->_('date', $history->timestamp, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset()); ?></td>
						<td>
							<ul class="uk-list uk-margin-bottom-remove">
								<?php foreach($stories as $story):?>
									<li><?php echo $this->app->zoocart->order->getLogPhrase($story->property, $story->value_old, $story->value_new); ?></li>
								<?php endforeach;?>
							</ul>
						</td>
						<td><?echo $history->username; ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<!-- end content -->
				
		</div>
	</div>
	
</div>

<script type="text/javascript">
jQuery(document).ready(function($){
	// add js language strings
	$.zx.lang.push({
		"ZC_PAYMENT_METHOD_UPDATED": "<?php echo JText::_('PLG_ZOOCART_SUCCESS_PAYMENT_METHOD_UPDATED') ?>",
		"ZC_SOMETHING_WENT_WRONG": "<?php echo JText::_('PLG_ZLFRAMEWORK_ERROR_SOMETHING_WENT_WRONG') ?>",
	});

	// init script
	$('#zoocart-container').zx('zoocartOrder', {
		order_id: <?php echo $this->resource->id; ?>
	});
});
</script>