<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Keepalive behavior
JHTML::_('behavior.keepalive');

// Parsley validator setup:
$this->app->document->addScript('zlfw:vendor/jquery/parsley.min.js');
?>

<div id="zoocart-currency">

	<!-- main menu -->
	<?php echo $this->partial('zlmenu'); ?>

	<!-- informer -->
	<?php echo $this->partial('informer'); ?>

	<!-- main content -->
	<div class="tm-main uk-panel uk-panel-box">
		<div class="uk-grid">
			<div class="uk-width-medium-1-10">
				<?php echo $this->partial('settings_tab'); ?>
			</div>
			<div class="uk-width-medium-9-10">
			<form id="adminForm" class="menu-has-level3" action="index.php" method="post" name="adminForm" accept-charset="utf-8">

				<?php
				// name
				echo $this->partial('fieldrow', array(
					'title' => 'PLG_ZLFRAMEWORK_NAME',
					'description' => 'PLG_ZOOCART_CURRENCY_NAME_DESCR',
					'field' => $this->app->html->text('name', $this->resource->name, 'class="inputbox" required data-parsley-type="alphanum"')
				));
				// code
				echo $this->partial('fieldrow', array(
					'title' => 'PLG_ZLFRAMEWORK_CODE',
					'description' => 'PLG_ZOOCART_CURRENCY_CODE_DESCR',
					'field' => $this->app->html->text('code', $this->resource->code, 'class="inputbox" required data-parsley-type="alphanum" data-parsley-maxlength="3" data-parsley-minlength="3" data-parsley-error-message="'.JText::_('PLG_ZOOCART_VALIDATOR_ERROR_CURRENCY_CODE').'"')
				));
				// symbol
				echo $this->partial('fieldrow', array(
					'title' => 'PLG_ZLFRAMEWORK_SYMBOL',
					'description' => 'PLG_ZOOCART_CURRENCY_SYMBOL_DESCR',
					'field' => $this->app->html->text('symbol', $this->resource->symbol, 'class="inputbox"')
				));
				// format
				$format_options = array(
					array('text' => '000X / -000X', 'value' => '%v%s / -%v%s'),
					array('text' => '000 X / -000 X', 'value' => '%v %s / -%v %s'),
					array('text' => 'X000 / X-000', 'value' => '%s%v / %s-%v'),
					array('text' => 'X000 / -X000', 'value' => '%s%v / -%s%v')
				);
				echo $this->partial('fieldrow', array(
					'title' => 'PLG_ZLFRAMEWORK_FORMAT',
					'description' => 'PLG_ZOOCART_CURRENCY_FORMAT_DESCR',
					'field' => $this->app->html->genericList($format_options, 'format', '', 'value', 'text', $this->resource->format, false, true)
				));
				// number of decimals
				echo $this->partial('fieldrow', array(
					'title' => 'PLG_ZOOCART_CONFIG_NUM_DECIMAL',
					'description' => 'PLG_ZOOCART_CURRENCY_DECIMALS_DESCR',
					'field' => $this->app->html->text('num_decimals', $this->resource->num_decimals, 'class="inputbox" required data-parsley-type="integer"')
				));
				// decimals to show
				echo $this->partial('fieldrow', array(
					'title' => 'PLG_ZOOCART_CONFIG_NUM_DECIMAL_TO_SHOW',
					'description' => 'PLG_ZOOCART_CURRENCY_DECIMAL_SHOW_DESCR',
					'field' => $this->app->html->text('num_decimals_show', $this->resource->num_decimals_show, 'class="inputbox" required data-parsley-type="integer"')
				));
				// decimals separator
				echo $this->partial('fieldrow', array(
					'title' => 'PLG_ZOOCART_CONFIG_DECIMAL_SEPARATOR',
					'description' => 'PLG_ZOOCART_CURRENCY_DECIMAL_SEP_DESCR',
					'field' => $this->app->html->text('decimal_sep', $this->resource->decimal_sep, 'class="inputbox"')
				));
				// thousand separator
				echo $this->partial('fieldrow', array(
					'title' => 'PLG_ZOOCART_CONFIG_THOUSAND_SEPARATOR',
					'description' => 'PLG_ZOOCART_CURRENCY_THOUSAND_SEP_DESCR',
					'field' => $this->app->html->text('thousand_sep', $this->resource->thousand_sep, 'class="inputbox"')
				));
				// conversion rate
				echo $this->partial('fieldrow', array(
					'title' => 'PLG_ZOOCART_CONFIG_CONVERSION_RATE',
					'description' => 'PLG_ZOOCART_CURRENCY_RATE_DESCR',
					'field' => $this->app->html->text('conversion_rate', $this->resource->conversion_rate, 'class="inputbox"')
				));
				// status
				echo $this->partial('fieldrow', array(
					'title' => 'PLG_ZLFRAMEWORK_STATUS',
					'description' => 'PLG_ZOOCART_RATE_ENABLE_DESCR',
					'field' => $this->lists['select_enabled']
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
			$('#zoocart-currency').zx('zoocartForm');
		});
	</script>
</div>