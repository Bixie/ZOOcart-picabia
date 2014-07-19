<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if(isset($renderer)) {
	// set item/layout
	$renderer->setItem($address);
	$renderer->setLayout($address->type);

	// get name value
	$name = trim($renderer->renderPosition('name', array('style' => 'default')));

	// address preview
	$preview = $renderer->render('address.' . $address->type, array('item' => $address));

	// set title
	$title = strlen($name) ? $name : strip_tags($preview);
}

?>

<!-- title -->
<strong class="zx-zoocart-address-manager-row-title uk-float-left">
	<?php echo isset($title) ? $title : ''; ?>
</strong>

<!-- preview -->
<div class="zx-zoocart-address-manager-row-preview uk-hidden">
	<?php echo isset($preview) ? $preview : ''; ?>
</div>

<!-- btns -->
<div class="uk-button-group uk-float-right">

	<!-- edit -->
	<button type="button" class="zx-zoocart-address-manager-row-edit uk-button uk-button-mini" data-uk-tooltip="{delay:500}" title="<?php echo JText::_('PLG_ZOOCART_ADDRESS_TIP_EDIT'); ?>">
		<i class="uk-icon-pencil"></i>
	</button>

	<!-- choose -->
	<button type="button" class="zx-zoocart-address-manager-row-choose uk-button uk-button-mini" data-uk-tooltip="{delay:500}" title="<?php echo JText::_('PLG_ZOOCART_ADDRESS_TIP_CHOOSE'); ?>">
		<i class="uk-icon-<?php echo $address->default ? 'dot-' : ''; ?>circle-o"></i>
	</button>
</div>