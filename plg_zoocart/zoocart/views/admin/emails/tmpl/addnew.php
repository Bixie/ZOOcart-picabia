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

?>

<div id="zoocart-emails">

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
				<form id="adminForm" action="index.php" class="menu-has-level3 uk-form" method="post" name="adminForm" accept-charset="utf-8">

					<h2><?php echo JText::_('PLG_ZOOCART_EMAIL_NEW_PREFLIGHT'); ?></h2>

					<?php
						// email type
						echo $this->partial('fieldrow', array(
							'title' => 'PLG_ZOOCART_EMAIL_TYPE',
							'field' => $this->app->zoocart->email->emailTypesList('type', '', '')
						));
						// language
						echo $this->partial('fieldrow', array(
							'title' => 'PLG_ZOOCART_EMAIL_LANGUAGE',
							'field' => $this->app->zoocart->getLanguages('language', '', JLanguageHelper::detectLanguage())
						));
					?>

					<!-- button -->
					<div class="uk-form-row">
						<button class="uk-button uk-button-primary" onclick="this.form.task.value='edit';return true;">
							<?php echo JText::_('PLG_ZOOCART_EMAIL_NEW_PROCEED'); ?>
						</button>
					</div>

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
</div>