<?php
/**
 * @package		ZOOcart
 * @author		ZOOlanders http://www.zoolanders.com
 * @copyright	Copyright (C) JOOlanders, SL
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Parsley validator setup:
$this->app->document->addScript('zlfw:vendor/jquery/parsley.min.js');

// Keepalive behavior
JHTML::_('behavior.keepalive');
$this->app->document->addScriptDeclaration("
// TinyMCE bug workaround:
function saveDraft(){
		if('undefined'!==tinyMCE){
			if('none'!=document.getElementById('template').style.display){
				tinyMCE.execCommand('mceToggleEditor', false, 'template');
			}
		}
	}
");

$this->resource->groups = explode(',', $this->resource->groups);

?>

<div id="zoocart-email">

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
				<form id="adminForm" action="index.php" class="uk-form" method="post" onsubmit="saveDraft();return true;" name="adminForm" accept-charset="utf-8">

					<?php
					// type
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_TYPE',
						'description' => 'PLG_ZOOCART_EMAIL_TYPE_DESC',
						'field' => '<strong>'.JText::_('PLG_ZOOCART_EMAIL_TYPE_'.strtoupper($this->resource->type)).'</strong>'
					));
					// language
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZOOCART_EMAIL_LANGUAGE',
						'description' => 'PLG_ZOOCART_EMAIL_LANGUAGE_DESC',
						'field' => $this->app->zoocart->getLanguages('language', 'class="inputbox"', $this->resource->language)
					));
					// user groups
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_USER_GROUPS',
						'description' => 'PLG_ZOOCART_EMAIL_GROUPS_DESC',
						'field' => $this->app->zoocart->userGroupsList('groups[]', $this->resource->groups, 'multiple="multiple"')
					));
					// subject
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZOOCART_EMAILS_SUBJECT',
						'description' => 'PLG_ZOOCART_EMAIL_SUBJECT_DESC',
						'field' => $this->app->html->text('subject', $this->resource->subject, 'required')
					));
					// cc
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZOOCART_EMAILS_CC',
						'description' => 'PLG_ZOOCART_EMAIL_CC_DESC',
						'field' => $this->app->html->text('cc', $this->resource->cc)
					));
					// bcc
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZOOCART_EMAILS_BCC',
						'description' => 'PLG_ZOOCART_EMAIL_BCC_DESC',
						'field' => $this->app->html->text('bcc', $this->resource->bcc)
					));
					// template
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZOOCART_EMAILS_TEMPLATE',
						'field' => $this->app->editor->display('template', $this->resource->template, 600, 300, 60, 15, false)
					));
					?>
					<div class="uk-button uk-margin-bottom" data-uk-toggle="{target:'#help-box'}"><span class="uk-icon-life-bouy"></span> <?php echo JText::_('PLG_ZOOCART_HELP_NOTES');?></div>
					<div class="uk-panel uk-panel-box uk-panel-box-primary uk-margin-bottom" id="help-box">
						<h3 class="uk-panel-title"><?php echo JText::_('PLG_ZOOCART_PLACEHOLDERS_DESCRIPTIONS');?></h3>
						<ul class="uk-list">
							<li><strong>{sitename}</strong> - <?php echo JText::_('PLG_ZOOCART_SITENAME_REPLACEMENT');?></li>
							<li><strong>{siteurl}</strong> - <?php echo JText::_('PLG_ZOOCART_SITEURL_REPLACEMENT');?></li>
							<li><strong>{user}</strong> - <?php echo JText::_('PLG_ZOOCART_USER_REPLACEMENT');?></li>
							<li><strong>{username}</strong> - <?php echo JText::_('PLG_ZOOCART_USERNAME_REPLACEMENT');?></li>
							<li><strong>{order_number}</strong> - <?php echo JText::_('PLG_ZOOCART_ORDER_NUMBER_REPLACEMENT');?></li>
							<li><strong>{order_link}</strong> - <?php echo JText::_('PLG_ZOOCART_ORDER_LINK_REPLACEMENT');?></li>
							<li><strong>{order_state}</strong> - <?php echo JText::_('PLG_ZOOCART_ORDER_STATE_REPLACEMENT');?></li>
						</ul>
					</div>
					<?php
					// status
					echo $this->partial('fieldrow', array(
						'title' => 'PLG_ZLFRAMEWORK_STATUS',
						'description' => 'PLG_ZOOCART_EMAIL_STATUS_DESC',
						'field' => $this->app->html->booleanlist('published','',(int)$this->resource->published)
					));
					?>

					<input type="hidden" name="type" value="<?php echo $this->resource->type; ?>" />
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
			$('#zoocart-email').zx('zoocartForm');
		});
	</script>
</div>
