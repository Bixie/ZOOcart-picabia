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

<div data-zx-zoocart-address-manager="{type:'<?php echo $type; ?>'}">

	<!-- Title -->
	<h3 class="uk-h4">
		<?php echo JText::_('PLG_ZOOCART_ADDRESS_' . strtoupper($type)); ?>
	</h3>

	<!-- addresses list -->
	<ul class="zx-zoocart-address-manager-rows uk-list uk-list-line">
		<?php foreach($addresses as $address) : ?>
		<li data-id="<?php echo $address->id; ?>">
			<?php echo $this->partial('addresses_row', array('address' => $address, 'renderer' => $renderer)); ?>
		</li>
		<?php endforeach; ?>
	</ul>

	<!-- empty row, for new address creation -->
	<?php $address = $this->app->object->create('AddressType', array('address')); ?>
	<li class="zx-zoocart-address-manager-row uk-hidden">
		<?php echo $this->partial('addresses_row', array('address' => $address)); ?>
	</li>

	<!-- empty message -->
	<div class="zx-zoocart-address-manager-empty uk-text-center uk-margin-bottom<?php echo count($addresses) ? ' uk-hidden' : ''; ?>">
		<?php echo JText::_('PLG_ZOOCART_ADDRESS_NO_ADDRESSES_YET'); ?>
	</div>

	<!-- add address button -->
	<button type="button" class="zx-zoocart-address-manager-row-add uk-button uk-button-mini uk-float-right" data-uk-tooltip="{delay:500}" title="<?php echo JText::_('PLG_ZOOCART_ADDRESS_TIP_ADD_' . strtoupper($type)); ?>">
		<i class="uk-icon-plus"></i>&nbsp;&nbsp;<?php echo JText::_('PLG_ZLFRAMEWORK_ADDRESS'); ?>
	</button>

</div>