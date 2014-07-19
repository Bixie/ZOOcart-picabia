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
$this->app->document->addScript('zlfw:vendor/zlux/js/addons/manager.min.js');
$this->app->document->addScript('zlfw:vendor/zlux/js/addons/manager-items.min.js');

$this->app->html->_('behavior.modal');

// Keepalive behavior
JHTML::_('behavior.keepalive');

// Parsley validator setup:
$this->app->document->addScript('zlfw:vendor/jquery/parsley.min.js');
$this->app->document->addScriptDeclaration("
	// Custom validation rule for date fields:
	window.ParsleyValidator
	  .addValidator('group', function (value, requirement) {
	    return /^[\d]{4}-[\d]{1,2}-[\d]{1,2}((\s[\d]{2}:[\d]{2}):[\d]{2})*$/.test(value);
	  }, 32);
");

$this->app->document->addScriptDeclaration("
	function jSelectUser_user(uid, name){
		document.getElementById('user_id').value = uid;
		document.getElementById('user_name').innerHTML = name;
		SqueezeBox.close();
    }
    function selectItem(id, name, opt){
		jQuery('#item_id').val(id);
		jQuery('#item_name').html(name);
		SqueezeBox.close();
	}
    ");
$item = null;
if($item_id = $this->resource->item_id){
	$item = $this->app->table->item->get($item_id);
	$app_id = $item->application_id;
}else{
	$app_id = 0;
}

?>

<div id="zoocart-subscriptions">

	<!-- main menu -->
	<?php echo $this->partial('zlmenu'); ?>

	<!-- informer -->
	<?php echo $this->partial('informer'); ?>

	<!-- main content -->
	<div class="tm-main uk-panel uk-panel-box">

		<form id="adminForm" action="index.php" class="uk-form" method="post" name="adminForm" accept-charset="utf-8">

			<?php
			// user
			$text = $this->resource->user_id ? $this->app->user->get($this->resource->user_id)->name : JText::_('PLG_ZOOCART_SELECT_USER');
			echo $this->partial('fieldrow', array(
				'title' => 'PLG_ZLFRAMEWORK_USER',
				'description' => 'PLG_ZOOCART_SUBS_USER_DESCR',
				'field' => '<a class="modal" id="user_name" rel="{handler: \'iframe\', size: {x: 800, y: 500}}" href="'.JRoute::_('index.php?option=com_users&view=users&layout=modal&tmpl=component&field=user',false).'">'.$text.'</a><input type="hidden" id="user_id" name="user_id" value="'.($this->resource->user_id ? $this->resource->user_id : 0).'" />',
			));
			// related order
			echo $this->partial('fieldrow', array(
				'title' => 'PLG_ZOOCART_SUBSCRIPTON_RELATED_ORDER',
				'description' => 'PLG_ZOOCART_SUBS_ORDER_DESCR',
				'field' => '<a class="zc-badge" href="'.$this->app->zl->link(array('controller'=>'orders', 'task'=>'edit', 'cid[]'=> (int)$this->resource->order_id), false).'">'.JText::_('PLG_ZLFRAMEWORK_ORDER').' #' . (int)$this->resource->order_id.'</a>'
			));
			// related item
			echo $this->partial('fieldrow', array(
				'title' => 'PLG_ZOOCART_SUBSCRIPTON_RELATED_ITEM',
				'description' => 'PLG_ZOOCART_SUBS_ITEM_DESCR',
				'field' => '<input type="text" data-zx-itempicker="{init_display:\''.($item ? $item->name : '').'\'}" required name="item_id" value="'.($this->resource->item_id ? $this->resource->item_id : '').'" />'
			));
			// valid from
			echo $this->partial('fieldrow', array(
				'title' => 'PLG_ZLFRAMEWORK_VALID_FROM',
				'description' => 'PLG_ZOOCART_SUBS_VALID_FROM_DESCR',
				'field' => '<input type="text" name="publish_up" class="zx-interval" id="publishup" data-parsley-required="false" data-parsley-group="1" data-uk-datepicker="{format:\'YYYY-MM-DD HH:mm:SS\'}" value="'.($this->resource->publish_up ? $this->resource->publish_up : '').'">'
			));
			// valid to
			echo $this->partial('fieldrow', array(
				'title' => 'PLG_ZLFRAMEWORK_VALID_TO',
				'description' => 'PLG_ZOOCART_SUBS_VALID_TO_DESCR',
				'field' => '<input type="text" name="publish_down" class="zx-interval" id="publishdn" data-parsley-required="false" data-parsley-group="1" data-uk-datepicker="{format:\'YYYY-MM-DD HH:mm:SS\'}" value="'.($this->resource->publish_down ? $this->resource->publish_down : '').'">'
			));
			// Complex validation message container:
			echo '<div id="interval_valid" class="uk-hidden zx-x-error-msg uk-text-danger">'. JText::_('PLG_ZOOCART_VALIDATOR_INVALID_DATE_INTERVAL') .'</div>';
			// status
			echo $this->partial('fieldrow', array(
				'title' => 'PLG_ZLFRAMEWORK_STATUS',
				'description' => 'PLG_ZOOCART_SUBS_STATUS_DESCR',
				'field' => $this->app->html->booleanlist('published','',(int)$this->resource->published)
			));
			?>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" id="task" value="" />
			<input type="hidden" name="order_id" value="<?php echo $this->resource->order_id?$this->resource->order_id:0; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->resource->id; ?>" />
			<input type="hidden" name="cid[]" value="<?php echo $this->resource->id; ?>" />
			<?php echo $this->app->html->_('form.token'); ?>

		</form>

	</div>
	<script type="text/javascript">
		jQuery(document).ready(function($){
			$('#zoocart-subscriptions').zx('zoocartSubsEdit');
		});
	</script>
</div>