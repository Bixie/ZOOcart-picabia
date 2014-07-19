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
$this->app->document->addScript('zlfw:vendor/zlux/js/addons/notify.min.js');
$this->app->document->addScript('zoocart:assets/js/zoocart.js');

// set renderer
$renderer = $this->app->renderer->create('address')->addPath(array($this->app->path->path('component.site:'), $this->app->path->path('zoocart:')));

?>

<div id="zx-zoocart-addresses" class="zx">

	<!-- render informer -->
	<?php echo $this->partial('informer'); ?>
	
	<div class="uk-grid">

		<!-- billing addresses -->
		<div class="uk-width-1-1 uk-width-medium-1-2 uk-margin-bottom">
		<?php echo $this->partial('addresses', array('addresses' => $this->resources['billing'], 'type' => 'billing', 'renderer' => $renderer)); ?>
		</div>

		<!-- shipping addresses -->
		<div class="uk-width-1-1 uk-width-medium-1-2">
		<?php echo $this->partial('addresses', array('addresses' => $this->resources['shipping'], 'type' => 'shipping', 'renderer' => $renderer)); ?>
		</div>

		<script type="text/javascript">
		jQuery(document).ready(function($){
			// add js language strings
			$.zx.lang.push({
				"ZC_ADDRESS_CONFIRM_DELETE": "<?php echo JText::_("PLG_ZOOCART_ADDRESS_CONFIRM_DELETE") ?>"
			});

			// hide tooltip when button pressed
			$('body').on('click', '.uk-button[data-uk-tooltip]', function(){
				$(this).data('tooltip').hide();
			});
		});
		</script>

	</div>
	
</div>