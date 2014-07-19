<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

defined('_JEXEC') or die('Restricted access');

	$this->app->html->_('behavior.tooltip');
	$this->app->document->addStylesheet('zoocart:assets/css/admin.css');

	// Keepalive behavior
	JHTML::_('behavior.keepalive');

	// filter output
	JFilterOutput::objectHTMLSafe($this->resource, ENT_QUOTES, array('params', 'billing_address', 'shipping_address'));

	$renderer = $this->app->renderer->create('address')->addPath(array($this->app->path->path('component.site:'), $this->app->path->path('zoocart:')));

?>

	<div class="row noprint">
		<button type="button" id="print" onclick="window.print();"><?php echo JText::_('PLG_ZOOCART_PRINT'); ?></button>
		<a href="<?php echo $this->component->link(array('controller' => $this->controller, 'task' => 'edit', 'cid[]' => $this->resource->id)); ?>"><?php echo JText::_('PLG_ZLFRAMEWORK_CANCEL'); ?></a>
	</div>

		<div class="col col-left width-50">

			<fieldset class="creation-form">
				<legend><?php echo JText::_('PLG_ZLFRAMEWORK_DETAILS'); ?></legend>

				<div class="element element-id">
					<strong><?php echo JText::_('PLG_ZLFRAMEWORK_ID'); ?></strong>
					<div>
						<div class="row">
							<?php echo $this->resource->id; ?>
						</div>
					</div>
				</div>

				<div class="element element-id">
					<strong><?php echo JText::_('PLG_ZLFRAMEWORK_USER'); ?></strong>
					<div>
						<div class="row">
							<?php echo $this->app->user->get($this->resource->user_id)->name; ?>
						</div>
					</div>
				</div>

				<div class="element element-address">
					<strong><?php echo JText::_('PLG_ZOOCART_ADDRESS_BILLING'); ?></strong>
					<div>
						<div class="row">
							<?php echo $renderer->render('address.billing', array('item' => $this->resource->getBillingAddress())); ?>
						</div>
					</div>
				</div>

				<div class="element element-address">
					<strong><?php echo JText::_('PLG_ZOOCART_ADDRESS_SHIPPING'); ?></strong>
					<div>
						<div class="row">
							<?php echo $renderer->render('address.shipping', array('item' => $this->resource->getShippingAddress())); ?>
						</div>
					</div>
				</div>

				<div class="element element-created-on">
					<strong><?php echo JText::_('PLG_ZLFRAMEWORK_CREATED_ON'); ?></strong>
					<div>
						<div class="row">
							<?php echo $this->app->html->_('date', $this->resource->created_on, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset()); ?>
						</div>
					</div>
				</div>


				<div class="element element-modified-on">
					<strong><?php echo JText::_('PLG_ZLFRAMEWORK_MODIFIED_ON'); ?></strong>
					<div>
						<div class="row">
							<?php echo $this->app->html->_('date', $this->resource->modified_on, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset()); ?>
						</div>
					</div>
				</div>

				<div class="element element-payment">
					<strong><?php echo JText::_('PLG_ZOOCART_PAYMENT_METHOD'); ?></strong>
					<div>
						<div class="row">
							<?php echo ucfirst($this->resource->payment_method); ?>
						</div>
					</div>
				</div>

				<div class="element element-notes">
					<strong><?php echo JText::_('PLG_ZOOCART_CUSTOMER_NOTES'); ?></strong>
					<div>
						<div class="row">
							<?php echo $this->resource->notes; ?>
						</div>
					</div>
				</div>
				
			</fieldset>

		</div>

		<div class="col col-right width-50">

			<fieldset class="creation-form">
				<legend><?php echo JText::_('Actions'); ?></legend>
				<div class="element element-state">
					<strong><?php echo JText::_('PLG_ZOOCART_ORDER_STATE'); ?></strong>
					<div>
						<div class="row">
							<?php echo JText::_($this->app->zoocart->table->orderstates->get($this->resource->state)->name); ?>
						</div>
					</div>
				</div>
				
			</fieldset>

			<fieldset class="creation-form">
				<legend><?php echo JText::_('PLG_ZLFRAMEWORK_ITEMS'); ?></legend>
				<?php echo $this->partial('items', array('items' => $this->resource->getItems())); ?>
				
			</fieldset>

			<fieldset class="creation-form">
				<legend><?php echo JText::_('PLG_ZOOCART_PAYMENTS'); ?></legend>
				<?php echo $this->partial('payments', array('payments' => $this->resource->getPayments())); ?>
				
			</fieldset>

		</div>

