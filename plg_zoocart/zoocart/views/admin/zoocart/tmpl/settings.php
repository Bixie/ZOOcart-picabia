<?php
/**
 * @package		ZOOcart
 * @author		ZOOlanders http://www.zoolanders.com
 * @copyright	Copyright (C) JOOlanders, SL
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// get settings params
$settings = $this->app->zoocart->getConfig();

?>

<!-- main menu -->
<?php echo $this->partial('zlmenu'); ?>

<!-- informer -->
<?php echo $this->partial('informer'); ?>

<div id="zoocart-settings" class="tm-main uk-panel uk-panel-box">
	<form id="adminForm" class="uk-form uk-form-stacked" action="index.php" method="post" name="adminForm" accept-charset="utf-8">

		<div class="uk-grid">
			<div class="uk-width-medium-1-10">
				<!-- Tab menu -->
				<?php echo $this->partial('settings_tab', array('current' => $this->active_tab)); ?>
			</div>
			<div class="uk-width-medium-9-10">
				<!-- Tab container -->
				<ul id="settings-tab-content" class="uk-switcher">

					<!-- GENERAL -->
					<li>
						<?php
						// default country
						echo $this->partial('fieldrow', array(
							'title' => 'PLG_ZOOCART_CONFIG_DEFAULT_COUNTRY',
							'description' => 'PLG_ZOOCART_CONFIG_DEFAULT_COUNTRY_DESC',
							'field' => $this->app->html->countrySelectList($this->app->country->getIsoToNameMapping(), 'zoocart[default_country]', $settings->get('default_country'), false)
						));

						// terms toggle
						echo $this->partial('fieldrow', array(
							'title' => 'PLG_ZOOCART_CONFIG_ACCEPT_TERMS',
							'description' => 'PLG_ZOOCART_CONFIG_ACCEPT_TERMS_DESC',
							'field' => $this->app->html->booleanlist('zoocart[accept_terms]','',(int)$settings->get('accept_terms'))
						));

						// terms url
						echo $this->partial('fieldrow', array(
							'title' => 'PLG_ZOOCART_CONFIG_TERMS_URL',
							'description' => 'PLG_ZOOCART_CONFIG_TERMS_URL_DESC',
							'field' => $this->app->html->text('zoocart[terms_url]',$settings->get('terms_url'),'size="20" style="max-width:300px;"')
						));

						// checkout open state
						echo $this->partial('fieldrow', array(
							'title' => 'PLG_ZOOCART_CONFIG_CHECKOUT_OPENED',
							'description' => 'PLG_ZOOCART_CONFIG_CHECKOUT_OPENED_DESC',
							'field' => $this->app->html->booleanlist('zoocart[checkout_opened]','',(int)$settings->get('checkout_opened'))
						));
						?>
					</li>

					<!-- CURRENCY -->
					<li>
						<!-- currencies -->
						<?php $link = $this->app->zl->link(array('controller' => 'currencies')); ?>
						<a href="<?php echo $link ?>" class="uk-button uk-button-small uk-float-right">
							<i class="uk-icon-edit"></i>
							<span><?php echo JText::_('Currencies'); ?></span>
						</a>

						<!-- default currency -->
						<?php 
						echo $this->partial('fieldrow', array(
							'title' => 'PLG_ZOOCART_CONFIG_DEFAULT_CURRENCY',
							'description' => 'PLG_ZOOCART_CONFIG_DEFAULT_CURRENCY_DESC',
							'field' => $this->app->zoocart->currency->currenciesList('zoocart[default_currency]',$settings->get('default_currency'))
						));
						?>
					</li>
					
					<!-- SHIPPING -->
					<li>
						<!-- rates -->
						<?php $link = $this->app->zl->link(array('controller' => 'shippingrates')); ?>
						<a href="<?php echo $link ?>" class="uk-button uk-button-small uk-float-right">
							<i class="uk-icon-edit"></i>
							<span><?php echo JText::_('Rates'); ?></span>
						</a>

						<!-- shipping toggle -->
						<?php 
							echo $this->partial('fieldrow', array(
								'title' => 'PLG_ZOOCART_CONFIG_ENABLED_SHIPPING',
								'description' => 'PLG_ZOOCART_CONFIG_ENABLED_SHIPPING_DESC',
								'field' => $this->app->html->booleanlist('zoocart[enable_shipping]','',(int)$settings->get('enable_shipping'))
							));
						?>

						<!-- autochoose -->
						<?php 
							echo $this->partial('fieldrow', array(
								'title' => 'PLG_ZOOCART_CONFIG_SHIPPING_AUTOCHOICE',
								'description' => 'PLG_ZOOCART_CONFIG_SHIPPING_AUTOCHOICE_DESC',
								'field' => $this->app->html->booleanlist('zoocart[shipping_autochoice]','',(int)$settings->get('shipping_autochoice'))
							));
						?>
					</li>

					<!-- DISCOUNTS -->
					<li>
						<!-- discount toggle -->
						<?php 
							echo $this->partial('fieldrow', array(
								'title' => 'PLG_ZOOCART_CONFIG_ALLOW_DISCOUNTS',
								'description' => 'PLG_ZOOCART_CONFIG_ALLOW_DISCOUNTS_DESC',
								'field' => $this->app->html->booleanlist('zoocart[discounts_allowed]','',(int)$settings->get('discounts_allowed'))
							));
						?>
					</li>

					<!-- ORDERS -->
					<li>
						<?php $link = $this->app->zl->link(array('controller' => 'orderstates')); ?>
						<a href="<?php echo $link ?>" class="uk-button uk-button-small uk-float-right">
							<i class="uk-icon-edit"></i>
							<span><?php echo JText::_('PLG_ZOOCART_CONFIG_ORDERSTATES'); ?></span>
						</a>
						<?php
						// orderstate update quantities
						echo $this->partial('fieldrow', array(
							'title' => 'PLG_ZOOCART_CONFIG_UPDATE_QUANTITIES_ORDERSTATE',
							'description' => 'PLG_ZOOCART_CONFIG_UPDATE_QUANTITIES_ORDERSTATE_DESC',
							'field' => $this->app->zoocart->order->orderStatesList('zoocart[quantity_update_state]',$settings->get('quantity_update_state'))
						));

						// orderstate new
						echo $this->partial('fieldrow', array(
							'title' => 'PLG_ZOOCART_CONFIG_NEW_ORDERSTATE',
							'description' => 'PLG_ZOOCART_CONFIG_NEW_ORDERSTATE_DESC',
							'field' => $this->app->zoocart->order->orderStatesList('zoocart[new_orderstate]',$settings->get('new_orderstate'))
						));

						// orderstate payment recieved
						echo $this->partial('fieldrow', array(
							'title' => 'PLG_ZOOCART_CONFIG_PAYMENT_RECEIVED_ORDERSTATE',
							'description' => 'PLG_ZOOCART_CONFIG_PAYMENT_RECEIVED_ORDERSTATE_DESC',
							'field' => $this->app->zoocart->order->orderStatesList('zoocart[payment_received_orderstate]',$settings->get('payment_received_orderstate'))
						));

						// orderstate payment pending
						echo $this->partial('fieldrow', array(
							'title' => 'PLG_ZOOCART_CONFIG_PAYMENT_PENDING_ORDERSTATE',
							'description' => 'PLG_ZOOCART_CONFIG_PAYMENT_PENDING_ORDERSTATE_DESC',
							'field' => $this->app->zoocart->order->orderStatesList('zoocart[payment_pending_orderstate]',$settings->get('payment_pending_orderstate'))
						));

						// orderstate payment failed
						echo $this->partial('fieldrow', array(
							'title' => 'PLG_ZOOCART_CONFIG_PAYMENT_FAILED_ORDERSTATE',
							'description' => 'PLG_ZOOCART_CONFIG_PAYMENT_FAILED_ORDERSTATE_DESC',
							'field' => $this->app->zoocart->order->orderStatesList('zoocart[payment_failed_orderstate]',$settings->get('payment_failed_orderstate'))
						));

						// orderstate payment canceled
						echo $this->partial('fieldrow', array(
							'title' => 'PLG_ZOOCART_CONFIG_CANCELED_ORDERSTATE',
							'description' => 'PLG_ZOOCART_CONFIG_CANCELED_ORDERSTATE_DESC',
							'field' => $this->app->zoocart->order->orderStatesList('zoocart[canceled_orderstate]',$settings->get('canceled_orderstate'))
						));

						// orderstate completed
						echo $this->partial('fieldrow', array(
							'title' => 'PLG_ZOOCART_CONFIG_FINISHED_ORDERSTATE',
							'description' => 'PLG_ZOOCART_CONFIG_FINISHED_ORDERSTATE_DESC',
							'field' => $this->app->zoocart->order->orderStatesList('zoocart[finished_orderstate]',$settings->get('finished_orderstate'))
						));
						?>
					</li>

					<!-- QUANTITIES -->
					<li>
						<?php
						// check quantities
						echo $this->partial('fieldrow', array(
							'title' => 'PLG_ZOOCART_CONFIG_CHECK_QUANTITIES',
							'description' => 'PLG_ZOOCART_CONFIG_CHECK_QUANTITIES_DESC',
							'field' => $this->app->html->booleanlist('zoocart[check_quantities]','',(int)$settings->get('check_quantities'))
						));

						// update quantities
						echo $this->partial('fieldrow', array(
							'title' => 'PLG_ZOOCART_CONFIG_UPDATE_QUANTITIES',
							'description' => 'PLG_ZOOCART_CONFIG_UPDATE_QUANTITIES_DESC',
							'field' => $this->app->html->booleanlist('zoocart[update_quantities]','',(int)$settings->get('update_quantities'))
						));
						?>
					</li>

					<!-- ADDRESS -->
					<li>
						<!-- types -->
						<?php $link = $this->app->zl->link(array('controller' => 'addresses')); ?>
						<a href="<?php echo $link ?>" class="uk-button uk-button-small uk-float-right">
							<i class="uk-icon-edit"></i>
							<span><?php echo JText::_('PLG_ZOOCART_CONFIG_ADDRESS_TYPES'); ?></span>
						</a>

						<?php
						// require address
						echo $this->partial('fieldrow', array(
							'title' => 'PLG_ZOOCART_CONFIG_REQUIRE_ADDRESS',
							'description' => 'PLG_ZOOCART_CONFIG_REQUIRE_ADDRESS_DESC',
							'field' => $this->app->html->booleanlist('zoocart[require_address]', '', (int)$settings->get('require_address', 1))
						));

						// billing address type
						$addr_options = array(
							array('text'=>'PLG_ZOOCART_BILLING','value'=>'billing'),
							array('text'=>'PLG_ZOOCART_SHIPPING','value'=>'shipping')
						);
						echo $this->partial('fieldrow', array(
							'title' => 'PLG_ZOOCART_CONFIG_BILLING_ADDRESS_TYPE',
							'description' => 'PLG_ZOOCART_CONFIG_BILLING_ADDRESS_TYPE_DESC',
							'field' => $this->app->html->genericList($addr_options, 'zoocart[billing_address_type]', '', 'value', 'text', $settings->get('billing_address_type'),false, true)
						));
						?>
					</li>

					<!-- TAXES -->
					<li>
						<div class="uk-panel uk-float-right">
							<!-- rules -->
							<a href="<?php echo $this->app->zl->link(array('controller' => 'taxclasses')); ?>" class="uk-button uk-button-small">
								<i class="uk-icon-edit"></i>
								<span><?php echo JText::_('Tax Classes'); ?></span>
							</a>
							<!-- rules -->
							<a href="<?php echo $this->app->zl->link(array('controller' => 'taxes')); ?>" class="uk-button uk-button-small">
								<i class="uk-icon-edit"></i>
								<span><?php echo JText::_('Tax Rules'); ?></span>
							</a>
						</div>
						<?php
						// default taxes
						echo $this->partial('fieldrow', array(
							'title' => 'PLG_ZOOCART_CONFIG_DEFAULT_TAX_CLASS',
							'description' => 'PLG_ZOOCART_CONFIG_DEFAULT_TAX_CLASS_DESC',
							'field' => $this->app->zoocart->html->_('zoo.taxClassesList', 'zoocart[default_tax_class]', $settings->get('default_tax_class'))
						));

						// price taxes show
						echo $this->partial('fieldrow', array(
							'title' => 'PLG_ZOOCART_CONFIG_SHOW_PRICE_WITH_TAX',
							'description' => 'PLG_ZOOCART_CONFIG_SHOW_PRICE_WITH_TAX_DESC',
							'field' => $this->app->html->booleanlist('zoocart[show_price_with_tax]','',(int)$settings->get('show_price_with_tax'))
						));

						// vies validation
						echo $this->partial('fieldrow', array(
							'title' => 'PLG_ZOOCART_CONFIG_VIES_VALIDATION',
							'description' => 'PLG_ZOOCART_CONFIG_VIES_VALIDATION_DESC',
							'field' => $this->app->html->booleanlist('zoocart[vies_validation]','',(int)$settings->get('vies_validation'))
						));

						// vies validation hard
						echo $this->partial('fieldrow', array(
							'title' => 'PLG_ZOOCART_CONFIG_VIES_VALIDATION_HARD',
							'description' => 'PLG_ZOOCART_CONFIG_VIES_VALIDATION_HARD_DESC',
							'field' => $this->app->html->booleanlist('zoocart[vies_validation_hard]','',(int)$settings->get('vies_validation_hard'))
						));
						?>
					</li>

					<!-- EMAILS -->
					<li>
						<!-- templates -->
						<?php $link = $this->app->zl->link(array('controller' => 'emails')); ?>
						<a href="<?php echo $link ?>" class="uk-button uk-button-small uk-float-right">
							<i class="uk-icon-edit"></i>
							<span><?php echo JText::_('PLG_ZOOCART_EMAIL_TEMPLATES'); ?></span>
						</a>
					</li>
				</ul>
			</div>
		</div>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="" />

		<?php echo $this->app->html->_('form.token'); ?>
	</form>
</div>