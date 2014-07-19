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

<form class="uk-form" id="zx-zoocart-form-authenticate-login">
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

		<!-- remember check -->
		<label for="zoocart_login_remember" class="uk-form-label uk-float-left">
			<input type="checkbox" id="zoocart_login_remember" name="remember" value="1" />
			<?php echo JText::_('PLG_ZLFRAMEWORK_REMEMBER_ME'); ?>
		</label>

		<!-- login button -->
		<button type="submit" class="uk-button uk-button-mini uk-button-primary uk-float-right">
			<i class="uk-icon-sign-in"></i>
			<?php echo JText::_('PLG_ZLFRAMEWORK_LOGIN'); ?>
		</button>
	</div>
</form>