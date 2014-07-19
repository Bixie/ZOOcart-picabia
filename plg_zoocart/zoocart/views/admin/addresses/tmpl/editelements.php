<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$this->app->html->_('behavior.tooltip');

// load zoo assets until new UI is ready
$this->app->document->addScript('libraries:jquery/jquery-ui.custom.min.js');
$this->app->document->addStylesheet('libraries:jquery/jquery-ui.custom.css');
$this->app->document->addScript('libraries:jquery/plugins/timepicker/timepicker.js');
$this->app->document->addStylesheet('libraries:jquery/plugins/timepicker/timepicker.css');
$this->app->document->addScript('assets:js/accordionmenu.js');
$this->app->document->addScript('assets:js/placeholder.js');
$this->app->document->addScript('assets:js/default.js');
$this->app->document->addStylesheet('assets:css/ui.css');
$this->app->document->addStyleSheet('assets:css/admin.css');
$this->app->document->addScript('assets:js/type.js');

?>

<!-- main menu -->
<?php echo $this->partial('zlmenu'); ?>

<!-- informer -->
<?php echo $this->partial('informer'); ?>

<div class="tm-main uk-panel uk-panel-box">
<div class="uk-grid">
	<div class="uk-width-medium-1-10">
		<?php echo $this->partial('settings_tab', array('current' => $this->controller)); ?>
	</div>
	<div class="uk-width-medium-9-10">

		<form id="adminForm" class="menu-has-level3" action="index.php" method="post" name="adminForm" accept-charset="utf-8">

			<div class="col col-left width-50">
				<fieldset>
					<legend><?php echo $this->type->name; ?></legend>
					<ul id="element-list" class="element-list">
					<?php
						$elements = $this->type->getElements();
						if (empty($elements)) {
							echo '<li></li>';
						} else {
							foreach ($elements as $element) {
								echo '<li class="element hideconfig">'.$this->partial('editelement', array('element' => $element)).'</li>';
							}
						}
					?>
					</ul>
				</fieldset>
			</div>

			<div id="add-element" class="col col-right width-50">
				<fieldset>
					<legend><?php echo JText::_('PLG_ZOOCART_ELEMENT_LIBRARY'); ?></legend>
					<?php
						if (count($this->elements)) {
							$i = 0;
							$html = array();
							$html[] = '<div class="groups">';
							foreach ($this->elements as $group => $elements) {
								if ($i == round(count($this->elements)/2)) {
									$html[] = '</div><div class="groups">';
								}
								$html[] = '<div class="elements-group-name">'.JText::_($group).'</div>';
								$html[] = '<ul class="elements">';
								foreach ($elements as $element) {
									$element->loadConfigAssets();
									$html[] = '<li class="'.$element->getElementType().'" title="'.JText::_('PLG_ZLFRAMEWORK_ADD_ELEMENT').'">'.JText::_($element->getMetaData('name')).'</li>';
								}

								$html[] = '</ul>';
								$i++;
							}
							$html[] = '</div>';
							echo implode("\n", $html);
						}
					?>
				</fieldset>
			</div>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="cid[]" value="<?php echo $this->type->id; ?>" />
		<?php echo $this->app->html->_('form.token'); ?>

		</form>
</div>
</div>
<script type="text/javascript">
	jQuery(function($) {
		$('#element-list').EditElements({ url: '<?php echo $this->app->component->active->link(array('controller' => $this->controller), false); ?>', msgNoElements: '<?php echo JText::_('PLG_ZOOCART_THERE_ARE_NO_ELEMENTS_DEFINED'); ?>', msgDeletelog: '<?php echo JText::_('PLG_ZLFRAMEWORK_DELETE_ELEMENT'); ?>' });
	});
</script>
</div>