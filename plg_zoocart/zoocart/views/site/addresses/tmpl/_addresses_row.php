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

<strong class="zx-zoocart-address-manager-row-title uk-float-left">
<?php if(isset($renderer)) {
	// set item/layout
	$renderer->setItem($address);
	$renderer->setLayout($address->type);

	// get name value
	$name = trim($renderer->renderPosition('name', array('style' => 'default')));

	// render name
	if(strlen($name)) {
		echo $name;

	// or address resume
	} else {
		$resume = $renderer->render('address.' . $address->type, array('item' => $address));	
		echo strip_tags($resume);
	}
} ?>
</strong>

<!-- btns -->
<div class="uk-button-group uk-float-right">

	<!-- edit -->
	<button type="button" class="zx-zoocart-address-manager-row-edit uk-button uk-button-mini" data-uk-tooltip="{delay:500}" title="<?php echo JText::_('PLG_ZOOCART_ADDRESS_TIP_EDIT'); ?>">
		<i class="uk-icon-pencil"></i>
	</button>

	<!-- default -->
	<button type="button" class="zx-zoocart-address-manager-row-setasdefault uk-button uk-button-mini" data-uk-tooltip="{delay:500}" title="<?php echo JText::_('PLG_ZOOCART_ADDRESS_TIP_MAKE_DEFAULT'); ?>"<?php echo $address->default ? ' disabled' : ''; ?>>
		<i class="uk-icon-<?php echo $address->default ? 'dot-' : ''; ?>circle-o"></i>
	</button>

	<!-- delete -->
	<button type="button" class="zx-zoocart-address-manager-row-delete uk-button uk-button-mini" data-uk-tooltip="{delay:500}" title="<?php echo JText::_('PLG_ZOOCART_ADDRESS_TIP_DELETE'); ?>"<?php echo $address->default ? ' disabled' : ''; ?>>
		<i class="uk-icon-times"></i>
	</button>
</div>