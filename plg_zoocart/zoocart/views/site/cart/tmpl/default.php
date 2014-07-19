<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// load assets
$this->app->document->addStylesheet('zoocart:assets/css/site.css');
$this->app->document->addScript('zlfw:vendor/zlux/js/addons/notify.js');
$this->app->document->addScript('zoocart:assets/js/zoocart.js');
$this->app->document->addScript('zlfw:assets/js/accounting.min.js');

?>

<div id="zoocart-container" class="zx">

	<?php if(count($this->cart->getItems())):?>

		<!-- Cart -->
		<?php echo $this->partial('cart'); ?>
		
		<!-- Authenticate or Checkout-->
		<?php echo !$this->app->user->get()->id ? $this->partial('auth') : $this->partial('checkout'); ?>

	<?php else : ?>
		<?php echo JText::_('PLG_ZOOCART_EMPTY_CART'); ?>
	<?php endif;?>
</div>

<script type="text/javascript">
jQuery(document).ready(function($){

	// set Currency plugin defaults
	var currency = <?php echo json_encode($this->app->zoocart->currency->getDefaultCurrency()); ?>,
		format = currency.format.split('/');
	$.extend(accounting.settings.currency, {
		symbol: currency.symbol,
		format: {
			pos: $.trim(format[0]),
			neg: format[1] ? $.trim(format[1]) : $.trim(format[0]),
			zero: $.trim(format[0])
		},
		decimal: currency.decimal_sep,
		thousand: currency.thousand_sep,
		precision: currency.num_decimals
	});
});
</script>