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

<div class="zx-zoocart-checkout-terms uk-text-center">

	<!-- terms url -->
	<?php if ($url = $this->app->zoocart->getConfig()->get('terms_url', '')) : ?>
	<div class="zx-zoocart-checkout-terms-link uk-margin-bottom">
		<a href="<?php echo JRoute::_($url); ?>" target="_blank"><?php echo JText::_('PLG_ZOOCART_TERMS_AND_CONDITION'); ?></a>
		<i class="uk-icon-external-link-square"></i>
	</div>
	<?php endif; ?>

	<!-- checbox -->
	<div class="zx-zoocart-checkout-terms-agree">
		<button type="button" class="uk-button uk-button-mini">
			<i class="uk-icon-square-o"></i>
			<?php echo JText::_('PLG_ZOOCART_AGREE_TERMS'); ?>
		</button>
		<input type="hidden" name="terms" value="0" />
	</div>
</div>