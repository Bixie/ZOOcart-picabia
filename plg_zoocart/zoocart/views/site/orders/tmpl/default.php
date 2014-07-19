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

// set renderer
$address_renderer = $this->app->renderer->create('address')->addPath(array($this->app->path->path('component.site:'), $this->app->path->path('zoocart:')));

// init vars
$config = $this->app->zoocart->getConfig();

?>

<div id="zoocart-container" class="zx">

	<?php echo $this->partial('informer'); ?>

	<?php if (count($this->resources)): ?>

	<form id="zoocart-site-default" class="uk-form zoocart-orders" action="<?php echo $this->component->link(); ?>" method="post" accept-charset="utf-8">

		<table class="uk-table uk-table-striped uk-table-condensed uk-table-bordered zoocart-table-orders">
			<thead>
				<tr>
					<th class="id">
						<?php echo JText::_('PLG_ZOOCART_ORDER_ID'); ?>
					</th>
					<th class="billing_address">
						<?php echo JText::_('PLG_ZOOCART_ADDRESS_BILLING'); ?>
					</th>
					<th class="shipping_address">
						<?php echo JText::_('PLG_ZOOCART_ADDRESS_SHIPPING'); ?>
					</th>
					<th class="net">
						<?php echo JText::_('PLG_ZLFRAMEWORK_NET_TOTAL'); ?>
					</th>
					<th class="total">
						<?php echo JText::_('PLG_ZLFRAMEWORK_TOTAL'); ?>
					</th>
					<th class="state">
						<?php echo JText::_('PLG_ZOOCART_ORDER_STATE'); ?>
					</th>
					<th class="payment_method">
						<?php echo JText::_('PLG_ZOOCART_PAYMENT_METHOD'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php for ($i=0, $n=count($this->resources); $i < $n; $i++) : ?>

				<?php
					$row = $this->resources[$i];
					switch ($row->getState()->id) {
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
				<tr>
					<td class="id">
						<a href="<?php echo $this->component->link(array('controller' => $this->controller, 'task' => 'view', 'id' => $row->id));  ?>"><?php echo $row->id; ?></a>
					</td>
					<td class="billing_address">
						<?php echo $address_renderer->render('address.billing', array('item' => $row->getBillingAddress())); ?>
					</td>
					<td class="shipping_address">
						<?php echo $address_renderer->render('address.shipping', array('item' => $row->getShippingAddress())); ?>
					</td>
					<td class="net">
						<?php echo $this->app->zoocart->currency->format($row->getSubtotal()); ?>
					</td>
					<td class="total">
						<?php echo $this->app->zoocart->currency->format($row->getTotal()); ?>
					</td>
					<td class="state">
						<div class="uk-badge-dropdown" data-uk-dropdown>
							<span class="uk-badge uk-badge-<?php echo $suffix; ?>">
								<?php echo JText::_($row->getState()->name); ?>
							</span>
							<div class="uk-dropdown uk-dropdown-flip"><?php echo JText::_($row->getState()->description); ?></div>
						</div>
					</td>
					<td class="payment_method">
						<?php echo ucfirst($row->payment_method); ?>
					</td>
				</tr>
			<?php endfor; ?>
			</tbody>
		</table>

		<?php if ($pagination = $this->pagination->render($this->pagination_link)) : ?>
		<ul class="uk-pagination">
			<?php echo $pagination; ?>
		</ul>
		<?php endif; ?>

		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo $this->app->html->_('form.token'); ?>

	</form>

	<?php else: ?>
		<?php echo JText::_('PLG_ZOOCART_CONFIG_NO_ORDERS_YET'); ?>
	<?php endif; ?>
</div>