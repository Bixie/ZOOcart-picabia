<?php
/**
 * @package		ZOOcart
 * @author		ZOOlanders http://www.zoolanders.com
 * @copyright	Copyright (C) JOOlanders, SL
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

JHtml::_('behavior.modal');

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$this->app->html->_('behavior.tooltip');

// init vars
$settings = $this->app->zoocart->getConfig();

$this->app->document->addScriptDeclaration("function jSelectUser_user(uid, name){
	document.getElementById('user_id').value = uid;
	document.getElementById('user_name').innerHTML = name;
	SqueezeBox.close();
}");

// Keepalive behavior
JHTML::_('behavior.keepalive');

// filter output
JFilterOutput::objectHTMLSafe($this->resource, ENT_QUOTES, array('params', 'billing_address', 'shipping_address', 'shipping_method'));

$renderer = $this->app->renderer->create('address')->addPath(array($this->app->path->path('component.site:'), $this->app->path->path('zoocart:')));

$uid = ($this->resource && $this->resource->user_id)?$this->resource->user_id:$uid = $this->app->user->get()->id;
$user = JFactory::getUser($uid);
if(!$user)
	$uid = $this->app->user->id;

$uname = $this->app->user->get($uid)->name;
$config = $this->app->zoocart->getConfig();

// Detect if shipping enabled:
$enable_shipping = $config->get('enable_shipping', 0);
?>

<div id="zoocart-orders">
	<!-- main menu -->
		<?php echo $this->partial('zlmenu'); ?>
	<!-- informer -->
		<?php echo $this->partial('informer'); ?>
	<div class="tm-main uk-panel uk-panel-box">
		<form id="adminForm" action="index.php" class="uk-form" method="post" name="adminForm" accept-charset="utf-8">
			<div class="uk-grid">
					<div class="uk-width-1-2">
						<!-- DETAILS: -->
					<div class=" uk-panel uk-panel-box">
						<fieldset data-uk-margin>
							<legend><?php echo JText::_('PLG_ZLFRAMEWORK_DETAILS'); ?></legend>
							<table class="uk-table">
								<tbody>
								<tr>
									<td><strong><?php echo JText::_('PLG_ZLFRAMEWORK_ID'); ?></strong></td>
									<td><?php echo $this->resource->id; ?></td>
								</tr>
								<tr>
									<td><strong><?php echo JText::_('PLG_ZLFRAMEWORK_USER'); ?></strong></td>
									<td>
										<a class="modal uk-button uk-button-mini uk-button-link" id="user_name" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="<?php echo JRoute::_('index.php?option=com_users&view=users&layout=modal&tmpl=component&field=user',false);?>"><?php echo $uname; ?></a>
										<input type="hidden" id="user_id" name="user_id" value="<?php echo $uid; ?>"/>
									</td>
								</tr>
								<?php if($config->get('discounts_allowed', 0) && $this->resource->discount_info):
									$discount = json_decode(htmlspecialchars_decode($this->resource->discount_info));
									?>
									<tr>
										<td><strong><?php echo JText::_('PLG_ZOOCART_DISCOUNT_DETAILS'); ?></strong></td>
										<td>
											<div class="discount-summ uk-text-bold">
												<?php echo JText::_('PLG_ZOOCART_DISCOUNT');?>
												<?php echo $this->app->zoocart->currency->format($this->resource->discount); ?>
											</div>
											<div class="discount-info ">
												<span class="uk-badge uk-badge-success"><?php echo JText::_('PLG_ZOOCART_DISCOUNT_INFO');?>:</span>
												<?php if($this->app->table->discounts->get($discount->id)):?>
													<a href="<?php echo $this->app->zl->link(array('controller'=>'discounts','task'=>'edit','cid[]'=>(int)$discount->id),false);?>">
														<?php echo $discount->name; ?>
													</a>
												<?php else: ?>
													<span class="grey"><?php echo $discount->name; ?></span>
												<?php endif; ?>
												(-<?php echo (1==$discount->type?$discount->discount.'%':$this->app->zoocart->currency->format($discount->discount));?>)
											</div>
										</td>
									</tr>
								<?php endif; ?>
								<tr>
									<td><strong><?php echo JText::_('PLG_ZLFRAMEWORK_CREATED_ON'); ?></strong></td>
									<td>
										<?php echo $this->app->html->_('date', $this->resource->created_on, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset()); ?>
									</td>
								</tr>
								<?php if($this->resource->modified_on):?>
									<tr>
										<td><strong><?php echo JText::_('PLG_ZLFRAMEWORK_MODIFIED_ON'); ?></strong></td>
										<td>
											<?php echo $this->app->html->_('date', $this->resource->modified_on, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset()); ?>
										</td>
									</tr>
								<?php endif; ?>
								<?php if($this->resource->notes):?>
									<tr>
										<td><strong><?php echo JText::_('PLG_ZOOCART_CUSTOMER_NOTES'); ?></strong></td>
										<td>
											<p class="uk-text-muted"><?php echo $this->resource->notes; ?></p>
										</td>
									</tr>
								<?php endif; ?>
								</tbody>
							</table>
						</fieldset>
					</div>
						<!-- ITEMS: -->
					<div class=" uk-panel uk-panel-box">
						<fieldset>
							<legend><?php echo JText::_('PLG_ZLFRAMEWORK_ITEMS'); ?></legend>
							<?php echo $this->partial('items', array('items' => $this->resource->getItems())); ?>
						</fieldset>
					</div>
						<!-- ACTIONS: -->
					<div class=" uk-panel uk-panel-box">
						<fieldset>
							<legend><?php echo JText::_('PLG_ZLFRAMEWORK_ACTIONS'); ?></legend>
							<div class="uk-form-row">
								<label><?php echo JText::_('PLG_ZOOCART_ORDER_STATE'); ?> </label>
								<?php echo $this->app->zoocart->orderstatesList('state', $this->resource->state, '', false); ?>
							</div>
							<div class="uk-form-row">
								<label><input type="checkbox" checked="checked" name="notify_user" value="1" /> <?php echo JText::_('PLG_ZOOCART_ORDER_STATE_CHANGE_NOTIFY_USER'); ?></label>
							</div>
						</fieldset>
					</div>
					</div>
					<div class="uk-width-1-2">
						<?php if($config->get('require_address', 0)): ?>
						<!-- ADDRESSES: -->
						<div class="uk-panel uk-panel-box">
							<div class="uk-grid">
									<div class="uk-width-1-<?php echo $enable_shipping?'2':'1'; ?>">
									<fieldset>
										<legend><?php echo JText::_('PLG_ZOOCART_BILLING'); ?></legend>
										<div class="uk-panel uk-panel-box uk-panel-box-primary uk-margin-bottom">
											<h3 class="uk-panel-title"><?php echo JText::_('PLG_ZOOCART_ADDRESS_BILLING'); ?></h3>
											<div><?php echo $renderer->render('address.billing', array('item' => $this->resource->getBillingAddress())); ?></div>
										</div>
											<div class="uk-panel uk-panel-box uk-panel-box-primary">
											<h3 class="uk-panel-title"><?php echo JText::_('PLG_ZOOCART_PAYMENT_METHOD'); ?></h3>
											<div>
												<span class="uk-badge uk-badge-success uk-badge-notification"><?php echo ucfirst($this->resource->payment_method); ?></span>
												<?php if(!$this->app->zoocart->order->isPayed($this->resource)): ?>
													<a href="javascript:void(0);" class="change-payment-method-toggle"><?php echo JText::_('PLG_ZLFRAMEWORK_CHANGE'); ?></a>
													<div class="change-payment-method">
														<?php
														$plugins = $this->app->zoocart->payment->getPaymentPlugins();
														foreach($plugins as $plugin) : ?>
															<input type="radio" name="payment_method" value="<?php echo $plugin->name;?>" <?php echo ($plugin->name == $this->resource->payment_method) ? 'checked="checked"' : ''; ?>/> <?php echo JText::_(ucfirst($plugin->name));?>
														<?php endforeach; ?>
													</div>
												<?php endif; ?>
											</div>
										</div>
										</fieldset>
									</div>
									<?php if($enable_shipping):?>
								<div class="uk-width-1-2">
									<fieldset>
										<legend><?php echo JText::_('PLG_ZOOCART_SHIPPING'); ?></legend>
										<div class="uk-panel uk-panel-box uk-panel-box-primary uk-margin-bottom">
											<h3 class="uk-panel-title"><?php echo JText::_('PLG_ZOOCART_ADDRESS_SHIPPING'); ?></h3>
											<div><?php echo $renderer->render('address.shipping', array('item' => $this->resource->getShippingAddress())); ?></div>
										</div>
											<div class="uk-panel uk-panel-box uk-panel-box-primary">
											<h3 class="uk-panel-title"><?php echo JText::_('PLG_ZOOCART_SHIPPING_METHOD'); ?></h3>
											<div>
												<?php foreach($this->resource->getShippingMethod() as $key => $value): ?>
													<strong><?php echo ucfirst($key); ?>:</strong> <?php echo ($key == 'price') ? $this->app->zoocart->currency->format($value) : $value; ?><br />
												<?php endforeach; ?>
											</div>
										</div>
									</fieldset>
								</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
						<!-- WEIGHTS: -->
					<?php if($settings->get('show_weights')):?>
					<div class="uk-panel uk-panel-box">
						<fieldset>
							<legend><?php echo JText::_('PLG_ZOOCART_WEIGHT'); ?></legend>
								<?php echo $this->resource->weight; ?>
							</fieldset>
					</div>
					<?php endif; ?>
						<!-- DISCOUNTS: -->
					<?php if($this->resource->discount_info) :
						$discount = json_decode(htmlspecialchars_decode($this->resource->discount_info));
					?>
					<div class="uk-panel uk-panel-box">
						<fieldset>
							<legend><?php echo JText::_('PLG_ZOOCART_DISCOUNT_DETAILS'); ?></legend>
									<!-- total -->
								<div>
								<?php echo JText::_('PLG_ZOOCART_DISCOUNT');?> <span class="amount"><?php echo $this->app->zoocart->currency->format($this->resource->discount); ?></span>
								</div>
								<!-- info -->
								<div class="discount-info">
									<?php if($this->app->table->discounts->get($discount->id)):?>
										<a href="<?php echo $this->app->zl->link(array('controller'=>'discounts','task'=>'edit','cid[]'=>(int)$discount->id),false);?>">
											<?php echo $discount->name; ?>
										</a>
									<?php else: ?>
										<span class="grey"><?php echo $discount->name; ?></span>
									<?php endif; ?>
									(-<?php echo (1==$discount->type?$discount->discount.'%':$this->app->zoocart->currency->format($discount->discount));?>)
								</div>
							</fieldset>
					</div>
					<?php endif; ?>
						<!-- PAYMENTS: -->
					<div class="uk-panel uk-panel-box">
						<fieldset>
							<legend><?php echo JText::_('PLG_ZOOCART_PAYMENTS'); ?></legend>
							<?php echo $this->partial('payments', array('payments' => $this->resource->getPayments())); ?>
						</fieldset>
					</div>
						<!-- HISTORY: -->
					<div class=" uk-panel uk-panel-box">
						<fieldset>
							<legend><?php echo JText::_('PLG_ZLFRAMEWORK_HISTORY'); ?></legend>
							<?php echo $this->partial('history', array('order'=>$this->resource, 'histories' => $this->app->zoocart->table->orderhistories->getGroupedByTime($this->resource->id))); ?>
						</fieldset>
					</div>
					</div>
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="id" value="<?php echo $this->resource->id; ?>" />
				<input type="hidden" name="cid[]" value="<?php echo $this->resource->id; ?>" />
					<?php echo $this->app->html->_('form.token'); ?>
				</div>
		</form>
	</div>
	<script type="text/javascript">
		jQuery(document).ready(function($){
			$('.change-payment-method-toggle').click(function(){
			$('.change-payment-method').toggle('slow');
				});
		});
	</script>

</div>