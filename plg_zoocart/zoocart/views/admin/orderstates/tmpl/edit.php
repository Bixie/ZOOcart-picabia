<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Keepalive behavior
JHTML::_('behavior.keepalive');

// Parsley validator setup:
$this->app->document->addScript('zlfw:vendor/jquery/parsley.min.js');

?>

<div id="zoocart-orderstate">

	<!-- main menu -->
	<?php echo $this->partial('zlmenu'); ?>

	<!-- informer -->
	<?php echo $this->partial('informer'); ?>

	<!-- main content -->
	<div class="tm-main uk-panel uk-panel-box">
		<div class="uk-grid">
			<div class="uk-width-medium-1-10">
				<?php echo $this->partial('settings_tab', array('current' => 'orders')); ?>
			</div>
			<div class="uk-width-medium-9-10">
				<form id="adminForm" class="uk-form" action="index.php" method="post" name="adminForm" accept-charset="utf-8">

					<?php
					// name
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_NAME',
						'description' => 'PLG_ZOOCART_ORDERSTATE_NAME_DESCR',
						'field' => $this->app->html->text('name', $this->resource->name, 'required')
					));
					// name
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_DESCRIPTION',
						'description' => 'PLG_ZOOCART_ORDERSTATE_DESCR_DESCR',
						'field' => $this->app->html->textarea('description', $this->resource->description, 'cols="20" rows="3"')
					));
					?>
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
					<input type="hidden" name="task" value="" />
					<input type="hidden" name="id" value="<?php echo $this->resource->id; ?>" />
					<input type="hidden" name="cid[]" value="<?php echo $this->resource->id; ?>" />
					<?php echo $this->app->html->_('form.token'); ?>

				</form>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		jQuery(document).ready(function($){
			// init script
			$('#zoocart-orderstate' )
				.zx('zoocartForm');
		});
	</script>
</div>