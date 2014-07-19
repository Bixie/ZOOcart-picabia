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

<div id="zoocart-tax">

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
				<form id="adminForm" class="uk-form uk-form-stacked" action="index.php" method="post" name="adminForm" accept-charset="utf-8">

					<?php
					// country
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_ADDRESS_COUNTRY',
						'description' => 'PLG_ZOOCART_TAX_ADDRESS_COUNTRY_DESCR',
						'field' => $this->app->html->countrySelectList($this->app->country->getIsoToNameMapping(), 'country', $this->resource->country, false)
					));

					// state
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_ADDRESS_STATE',
						'description' => 'PLG_ZOOCART_TAX_ADDRESS_STATE_DESCR',
						'field' => $this->app->html->text('state', $this->resource->state, 'class="inputbox"')
					));

					// city
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_ADDRESS_CITY',
						'description' => 'PLG_ZOOCART_TAX_ADDRESS_CITY_DESCR',
						'field' => $this->app->html->text('city', $this->resource->city, 'class="inputbox"')
					));

					// zip
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_ADDRESS_ZIP',
						'description' => 'PLG_ZOOCART_TAX_ADDRESS_ZIP_DESCR',
						'field' => $this->app->html->text('zip', $this->resource->zip, 'class="inputbox"')
					));

					// tax rate
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZOOCART_TAX_RATE',
						'description' => 'PLG_ZOOCART_TAX_RATE_DESCR',
						'field' => $this->app->html->text('taxrate', $this->resource->taxrate, 'class="inputbox" required data-parsley-type="number"')
					));

					// tax class
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZOOCART_TAX_CLASS',
						'description' => 'PLG_ZOOCART_TAX_CLASS_DESCR',
						'field' => $this->app->zoocart->html->_('zoo.taxClassesList', 'tax_class_id', $this->resource->tax_class_id)
					));

					// status
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_STATUS',
						'description' => 'PLG_ZOOCART_TAX_ENABLE_DESCR',
						'field' => $this->lists['select_enabled']
					));

					// vies
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZOOCART_VIES',
						'description' => 'PLG_ZOOCART_TAX_VIES_DESCR',
						'field' => $this->lists['select_vies']
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
			$('#zoocart-tax').zx('zoocartForm');
		});
	</script>
</div>