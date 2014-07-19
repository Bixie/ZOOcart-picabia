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

<div data-zx-zoocart-slides class="zx-zoocart-authenticate">

	<!-- text -->
	<div class="uk-text-primary uk-text-center">
		<?php echo JText::_('PLG_ZOOCART_CHECKOUT_STEP_AUTHENTICATE'); ?>
	</div>

	<!-- nav -->
	<ul class="zx-zoocart-slides-nav uk-subnav uk-subnav-pill uk-text-center uk-margin-large-bottom" data-uk-switcher>
		<li class="uk-active"><a href=""><?php echo JText::_('PLG_ZLFRAMEWORK_LOGIN'); ?></a></li>
		<li><a href=""><?php echo JText::_('PLG_ZLFRAMEWORK_REGISTER'); ?></a></li>
	</ul>

	<!-- slides -->
	<div class="zx-zoocart-slides-container">

		<!-- login -->
		<div class="zx-zoocart-slides-slide">
			<div class="zx-zoocart-slides-slide-content">
				<div class="uk-container-center">
					<?php echo $this->partial('auth_login'); ?>
				</div>
			</div>
		</div>

		<!-- register -->
		<div class="zx-zoocart-slides-slide">
			<div class="zx-zoocart-slides-slide-content">
				<div class="uk-container-center">
					<?php echo $this->partial('auth_register'); ?>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
	jQuery(document).ready(function($){

		// init script
		$('.zx-zoocart-authenticate').zx('zoocartAuthenticate');
	});
	</script>

</div>