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

// Transform user groups and countries:
$this->resource->user_groups = explode(',', $this->resource->user_groups);
$this->resource->countries = explode(',', $this->resource->countries);

?>

<div id="zoocart-rate">

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
				<form id="adminForm" action="index.php" class="uk-form" method="post" name="adminForm" accept-charset="utf-8">

					<?php
					// name
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_NAME',
						'description' => 'PLG_ZOOCART_RATE_NAME_DESCR',
						'field' => $this->app->html->text('name', $this->resource->name, 'required')
					));
					// price
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_PRICE',
						'description' => 'PLG_ZOOCART_RATE_PRICE_DESCR',
						'field' => $this->app->html->text('price', $this->resource->price, 'required data-parsley-type="number"')
					));
					// type
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_TYPE',
						'description' => 'PLG_ZOOCART_RATE_TYPE_DESCR',
						'field' => $this->app->zoocart->shipping->shippingRateTypes('type', $this->resource->type)
					));
					// price from
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_PRICE_FROM',
						'description' => 'PLG_ZOOCART_RATE_PRICEFROM_DESCR',
						'field' => $this->app->html->text('price_from', $this->resource->price_from)
					));
					// price to
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_PRICE_TO',
						'description' => 'PLG_ZOOCART_RATE_PRICETO_DESCR',
						'field' => $this->app->html->text('price_to', $this->resource->price_to)
					));
					// quantity from
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_QUANTITY_FROM',
						'description' => 'PLG_ZOOCART_RATE_QTYFROM_DESCR',
						'field' => $this->app->html->text('quantity_from', $this->resource->quantity_from)
					));
					// quantity to
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_QUANTITY_TO',
						'description' => 'PLG_ZOOCART_RATE_QTYTO_DESCR',
						'field' => $this->app->html->text('quantity_to', $this->resource->quantity_to)
					));
					// weight from
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_WEIGHT_FROM',
						'description' => 'PLG_ZOOCART_RATE_WEIGHTFROM_DESCR',
						'field' => $this->app->html->text('weight_from', $this->resource->weight_from, 'class="inputbox"')
					));
					// weight to
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_WEIGHT_TO',
						'description' => 'PLG_ZOOCART_RATE_WEIGHTTO_DESCR',
						'field' => $this->app->html->text('weight_to', $this->resource->weight_to, 'class="inputbox"')
					));
					// countries
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_ADDRESS_COUNTRIES',
						'description' => 'PLG_ZOOCART_RATE_COUNTRIES_DESCR',
						'field' => $this->app->html->countrySelectList($this->app->country->getIsoToNameMapping(), 'countries[]', $this->resource->countries, true)
					));
					// states
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_ADDRESS_STATES',
						'description' => 'PLG_ZOOCART_RATE_STATES_DESCR',
						'field' => $this->app->html->text('states', $this->resource->states)
					));
					// cities
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_ADDRESS_CITIES',
						'description' => 'PLG_ZOOCART_RATE_CITIES_DESCR',
						'field' => $this->app->html->text('cities', $this->resource->cities)
					));
					// zips
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_ADDRESS_ZIPS',
						'description' => 'PLG_ZOOCART_RATE_ZIPS_DESCR',
						'field' => $this->app->html->text('zips', $this->resource->zips)
					));
					// user groups
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_USER_GROUPS',
						'description' => 'PLG_ZOOCART_RATE_GROUPS_DESCR',
						'field' => $this->app->zoocart->userGroupsList('user_groups[]', $this->resource->user_groups, 'multiple="multiple"')
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
			// init script
			$('#zoocart-rate' )
				.zx('zoocartForm');
		});
	</script>
</div>
