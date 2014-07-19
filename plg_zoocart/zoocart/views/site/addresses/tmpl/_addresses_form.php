<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// set Address renderer
if(!isset($renderer)) {
	$renderer = $this->app->renderer->create('addresssubmission')->addPath(array($this->app->path->path('component.site:'), $this->app->path->path('zoocart:')));
}

// load address
if($id) {
	$address = $this->app->zoocart->table->addresses->get($id);

// or create empty Address object
} else {
	$address = $this->app->object->create('Address');
	$address->type = $type;
}

// render the form
echo $renderer->render('address.' . $address->type . '-form', array('item' => $address));

?>

<!-- render the edit buttons if ajax call -->
<?php if($this->app->zlfw->request->isAjax()) : ?>
<div class="uk-margin-top">
	<button type="button" class="zx-zoocart-address-manager-form-abort uk-button uk-button-mini uk-float-left" data-uk-tooltip="{delay:500}" title="<?php echo JText::_('PLG_ZOOCART_ADDRESS_TIP_ABORT'); ?>">
		<i class="uk-icon-reply"></i>
	</button>
	<button type="button" class="zx-zoocart-address-manager-form-save uk-button uk-button-mini uk-float-right" data-uk-tooltip="{delay:500}" title="<?php echo JText::_('PLG_ZOOCART_ADDRESS_TIP_SAVE'); ?>">
		<i class="uk-icon-check"></i>
		<?php echo JText::_('PLG_ZLFRAMEWORK_SAVE'); ?>
	</button>
</div>
<?php endif; ?>