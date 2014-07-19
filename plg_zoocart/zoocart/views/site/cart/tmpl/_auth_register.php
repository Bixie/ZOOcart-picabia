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

<form class="uk-form" id="zx-zoocart-form-authenticate-register">
	<div class="uk-form-row">
		<div class="uk-form-icon uk-width-1-1">
			<i class="uk-icon-male"></i>
			<input type="text" class="uk-width-1-1" name="name" value="" placeholder="<?php echo JText::_('PLG_ZLFRAMEWORK_FULL_NAME'); ?>" />
		</div>
	</div>
	<div class="uk-form-row">
		<div class="uk-form-icon uk-width-1-1">
			<i class="uk-icon-envelope"></i>
			<input type="text" class="uk-width-1-1" name="email" value="" placeholder="<?php echo JText::_('PLG_ZLFRAMEWORK_EMAIL'); ?>" />
		</div>
	</div>
	<div class="uk-form-row">
		<div class="uk-form-icon uk-width-1-1">
			<i class="uk-icon-user"></i>
			<input type="text" class="uk-width-1-1" name="username" value="" placeholder="<?php echo JText::_('PLG_ZLFRAMEWORK_USERNAME'); ?>" />
		</div>
	</div>
	<div class="uk-form-row">
		<div class="uk-form-icon uk-width-1-1">
			<i class="uk-icon-lock"></i>
			<input type="password" class="uk-width-1-1" name="password" value="" placeholder="<?php echo JText::_('PLG_ZLFRAMEWORK_PASSWORD'); ?>" />
		</div>
	</div>
	<div class="uk-form-row">
		<div class="uk-form-icon uk-width-1-1">
			<i class="uk-icon-lock"></i>
			<input type="password" class="uk-width-1-1" name="password2" value="" placeholder="<?php echo JText::_('PLG_ZLFRAMEWORK_PASSWORD_CONFIRM'); ?>"/>
		</div>
	</div>
	<div class="uk-form-row">
		<button type="submit" class="uk-button uk-button-mini uk-button-primary uk-float-right">
			<i class="uk-icon-sign-in"></i>
			<?php echo JText::_('PLG_ZLFRAMEWORK_REGISTER'); ?>
		</button>
	</div>
</form>