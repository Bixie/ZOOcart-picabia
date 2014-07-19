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

<div id="zoocart-addresstypes">

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

			<form id="adminForm" class="menu-has-level3 assign-elements" action="index.php" method="post" name="adminForm" accept-charset="utf-8">

				<div class="col col-left width-50">

					<fieldset>
					<legend><?php echo JText::_('PLG_ZLFRAMEWORK_POSITIONS'); ?></legend>

						<?php
							$elements  = $this->type->getElements();
							if (isset($this->positions['positions']) && ($positions = $this->positions['positions']) && count($positions)) {
								foreach ($positions as $position => $name) {
									echo '<div class="position">'.$name.'</div>';
									echo '<ul class="element-list" data-position="'.$position.'">';

									if ($this->config && isset($this->config[$position])) {
										$i = 0;
										foreach ($this->config[$position] as $data) {
											if (isset($data['element']) && isset($elements[$data['element']])) {
												$element = $elements[$data['element']];

												// render partial
												echo $this->partial('assignelement', array('element' => $element, 'data' => $data, 'position' => $position, 'index' => $i++, 'core' => ($element->getGroup() == 'Core')));
											}
										}
									}

									echo '</ul>';
								}
							} else {
								echo '<i>'.JText::_('PLG_ZOOCART_THERE_ARE_NO_POSITIONS_DEFINED').'</i>';
							}
						?>

					</fieldset>

				</div>

				<div class="col col-right width-50">

					<fieldset>
					<legend><?php echo JText::_('PLG_ZOOCART_CONFIG_ADDRESS_ELEMENTS'); ?></legend>

					<?php
						$elements = $this->type->getElements();
						if ($elements === false || count($elements) == 0) {
							echo '<i>'.JText::_('PLG_ZOOCART_NO_CUSTOM_ELEMENTS_DEFINED').'</i>';
						}

						if ($elements !== false) {
							echo '<ul class="element-list unassigned" data-position="unassigned">';
							foreach ($elements as $element) {

								// render partial
								echo $this->partial('assignelement', array('element' => $element, 'data' => array()));
							}
							echo '</ul>';
						}
					?>

					</fieldset>

				</div>


			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="type" value="<?php echo $this->type->id; ?>" />
			<input type="hidden" name="path" value="<?php echo urlencode($this->relative_path); ?>" />
			<input type="hidden" name="layout" value="<?php echo $this->layout; ?>" />
			<?php echo $this->app->html->_('form.token'); ?>

			</form>
		</div>
</div>
<script type="text/javascript">
	jQuery(function($) {
		$('#adminForm').AssignElements();
	});
</script>
</div>