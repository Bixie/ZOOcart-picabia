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
$this->app->document->addScript('zlfw:vendor/zlux/js/uikit/addons/datepicker.min.js');
// Parsley validator setup:
$this->app->document->addScript('zlfw:vendor/jquery/parsley.min.js');
$this->app->document->addScriptDeclaration("
	// Custom validation rule for date fields:
	window.ParsleyValidator
	  .addValidator('group', function (value, requirement) {
	    return /^[\d]{4}-[\d]{1,2}-[\d]{1,2}((\s[\d]{2}:[\d]{2}):[\d]{2})*$/.test(value);
	  }, 32);
");

// Keepalive behavior
JHTML::_('behavior.keepalive');

// Tmp js:
$this->app->document->addScriptDeclaration("
	jQuery(document).ready(function(){
        jQuery('#type').change(function(){
            if(0==jQuery(this).val()){
                jQuery('#d_value').removeClass('perc');
            }else{
                jQuery('#d_value').addClass('perc');
            }
        });
	});
");

// Transform user groups
$this->resource->usergroups = explode(',', $this->resource->usergroups);

?>

<div id="zoocart-discount">

	<!-- main menu -->
	<?php echo $this->partial('zlmenu'); ?>

	<!-- informer -->
	<?php echo $this->partial('informer'); ?>

	<!-- main content -->
	<div class="tm-main uk-panel uk-panel-box">
		<form id="adminForm" action="index.php" class="uk-form" method="post" name="adminForm" accept-charset="utf-8">

			<?php
			// name
			echo $this->partial('fieldrow', array(
				'title' => 'PLG_ZLFRAMEWORK_NAME',
				'description' => 'PLG_ZOOCART_DISCOUNT_NAME_DESCR',
				'field' => $this->app->html->text('name', $this->resource->name, 'required data-parsley-type="alphanum"')
			));
			// code
			echo $this->partial('fieldrow', array(
				'title' => 'PLG_ZOOCART_DISCOUNT_CODE',
				'description' => 'PLG_ZOOCART_DISCOUNT_CODE_DESCR',
				'field' => $this->app->html->text('code', $this->resource->code, 'required data-parsley-type="alphanum"')
			));
			// discount type
			$type_options = array(
				array('text'=>'PLG_ZLFRAMEWORK_FIXED','value'=>'0'),
				array('text'=>'PLG_ZLFRAMEWORK_PERCENTAGE','value'=>'1')
			);
			echo $this->partial('fieldrow', array(
				'title' => 'PLG_ZOOCART_DISCOUNT_TYPE',
				'description' => 'PLG_ZOOCART_DISCOUNT_TYPE_DESCR',
				'field' => $this->app->html->genericList($type_options, 'type', '', 'value', 'text', $this->resource->type,false,true)
			));
			// discount
			echo $this->partial('fieldrow', array(
				'title' => 'PLG_ZOOCART_DISCOUNT',
				'description' => 'PLG_ZOOCART_DISCOUNT_SUM_DESCR',
				'field' => $this->app->html->text('discount', $this->resource->discount, 'required data-parsley-type="number"')
			));
			// status
			echo $this->partial('fieldrow', array(
				'title' => 'PLG_ZLFRAMEWORK_STATUS',
				'description' => 'PLG_ZOOCART_DISCOUNT_STATUS_DESCR',
				'field' => $this->app->html->booleanlist('published', '', (int)$this->resource->published)
			));
			// valid from
			echo $this->partial('fieldrow', array(
				'title' => 'PLG_ZLFRAMEWORK_VALID_FROM',
				'description' => 'PLG_ZOOCART_DISCOUNT_VALID_FROM_DESCR',
				'field' => '<input type="text" name="valid_from" class="zx-interval" id="publishup" data-parsley-required="false" data-parsley-group="1" data-uk-datepicker="{format:\'YYYY-MM-DD HH:mm:SS\'}" value="'.($this->resource->valid_from ? $this->resource->valid_from : '').'">'
			));
			// valid to
			echo $this->partial('fieldrow', array(
				'title' => 'PLG_ZLFRAMEWORK_VALID_TO',
				'description' => 'PLG_ZOOCART_DISCOUNT_VALID_FROM_DESCR',
				'field' => '<input type="text" name="valid_to" class="zx-interval" id="publishdn" data-parsley-required="false" data-parsley-group="1" data-uk-datepicker="{format:\'YYYY-MM-DD HH:mm:SS\'}" value="'.($this->resource->valid_to ? $this->resource->valid_to : '').'">'
			));
			// Complex validation message container:
			echo '<div id="interval_valid" class="uk-hidden zx-x-error-msg uk-text-danger">'. JText::_('PLG_ZOOCART_VALIDATOR_INVALID_DATE_INTERVAL') .'</div>';
			// user groups
			echo $this->partial('fieldrow', array(
				'title' => 'PLG_ZLFRAMEWORK_USER_GROUPS',
				'description' => 'PLG_ZOOCART_DISCOUNT_GROUPS_DESCR',
				'field' => $this->app->zoocart->userGroupsList('usergroups[]', $this->resource->usergroups, 'multiple="multiple"')
			));
			// hits per user
			echo $this->partial('fieldrow', array(
				'title' => 'PLG_ZOOCART_DISCOUNT_HITS_PER_USER',
				'description' => 'PLG_ZOOCART_DISCOUNT_HITS_DESCR',
				'field' => $this->app->html->text('hits_per_user', $this->resource->hits_per_user)
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
	<script type="text/javascript">
		jQuery(document).ready(function($){
			$('#zoocart-discount').zx('zoocartForm' ).zx('zoocartDiscountEdit');
		});
	</script>
</div>