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

<div class="zx-zoocart-checkout-address" data-type="<?php echo $type; ?>">

	<?php if (!count($this->addresses[$type])) : ?>

	<!-- sameAsBilling -->
	<?php if ($type == 'shipping') : ?>
	<div class="uk-form-row zx-zoocart-checkout-address-sameasbilling uk-margin-bottom">
		<button type="button" class="uk-button uk-button-mini" data-uk-tooltip="{delay:500}" title="<?php echo JText::_('PLG_ZOOCART_ADDRESS_TIP_SAME_AS_BILLING'); ?>">
			<i class="uk-icon-check-square-o"></i>
			<?php echo JText::_('PLG_ZOOCART_ADDRESS_SAME_AS_BILLING'); ?>
		</button>
		<input type="hidden" name="same_as_billing" value="1" /> 
	</div>
	<?php endif; ?>

	<div class="zx-zoocart-address-manager-form uk-form uk-container-center">

		<!-- new address fields -->
		<div class="zx-zoocart-checkout-address-hidden"<?php if($type == 'shipping') echo ' style="display:none;"'; ?>>
		<?php
			$address = $this->app->object->create('Address');
			$address->type = $type;
			echo $this->address_submission->render('address.' . $address->type . '-form', array('item' => $address));
		?>
		</div>

	</div>

	<?php else : ?>

	<!-- change btn -->
	<div class="uk-float-right">
		<a href="" class="zx-zoocart-checkout-address-change uk-button uk-button-mini uk-text-center" data-uk-tooltip="{delay:500}" title="<?php echo JText::_('PLG_ZOOCART_ADDRESS_TIP_CHANGE'); ?>">
			<?php echo JText::_('PLG_ZLFRAMEWORK_CHANGE'); ?>
		</a>
	</div>

	<!-- chosen address, the default one on start -->
	<div class="zx-zoocart-checkout-address-chosen uk-nbfc">

		<!-- address -->
		<?php 
			$address = $this->app->zoocart->table->addresses->getDefaultAddress($this->user->id, $type);
			echo $this->address_renderer->render('address.' . $type, array('item' => $address));
		?>
		
		<!-- address id -->
		<input type="hidden" name="address_id" value="<?php echo $address->id; ?>" />

	</div>

	<!-- address manager -->
	<div class="zx-zoocart-checkout-address-others uk-margin-top" data-zx-zoocart-address-manager="{type:'<?php echo $type; ?>'}">

		<!-- addresses list -->
		<ul class="zx-zoocart-address-manager-rows uk-list uk-list-line">
			<?php foreach($this->addresses[$type] as $address) : ?>
			<li data-id="<?php echo $address->id; ?>">
				<?php echo $this->partial('checkout_addresses_row', array('address' => $address, 'renderer' => $this->address_renderer)); ?>
			</li>
			<?php endforeach; ?>
		</ul>

		<!-- empty row, for new address creation -->
		<?php $address = $this->app->object->create('AddressType', array('address')); ?>
		<li class="zx-zoocart-address-manager-row uk-hidden">
			<?php echo $this->partial('checkout_addresses_row', array('address' => $address)); ?>
		</li>

		<!-- empty message -->
		<div class="zx-zoocart-address-manager-empty uk-text-center uk-margin-bottom<?php echo count($this->addresses) ? ' uk-hidden' : ''; ?>">
			<?php echo JText::_('PLG_ZOOCART_ADDRESS_NO_ADDRESSES_YET'); ?>
		</div>

		<!-- add address button -->
		<button type="button" class="zx-zoocart-address-manager-row-add uk-button uk-button-mini uk-float-right" data-uk-tooltip="{delay:500}" title="<?php echo JText::_('PLG_ZOOCART_ADDRESS_TIP_ADD_' . strtoupper($type)); ?>">
			<i class="uk-icon-plus"></i>&nbsp;&nbsp;<?php echo JText::_('PLG_ZLFRAMEWORK_ADDRESS'); ?>
		</button>

	</div>
	<?php endif; ?>
</div>