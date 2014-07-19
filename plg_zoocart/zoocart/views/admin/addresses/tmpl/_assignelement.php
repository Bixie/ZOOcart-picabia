<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// get elements meta data
$name = isset($position) && isset($index) ? 'positions['.$position.']['.$index.']' : 'elements['.$element->identifier.']';
$form = $element->getConfigForm();
$form->layout_path = $this->path;
$form->selectable_types = $element->config->get('selectable_types', array());

?>
<li class="element hideconfig" data-element="<?php echo $element->identifier; ?>">
	<div class="element-icon edit-element edit-event" title="<?php echo JText::_('PLG_ZLFRAMEWORK_EDIT_ELEMENT'); ?>"></div>
	<div class="element-icon delete-element delete-event" title="<?php echo JText::_('PLG_ZLFRAMEWORK_DELETE_ELEMENT'); ?>"></div>
	<div class="name sort-event" title="<?php echo JText::_('PLG_ZLFRAMEWORK_DRAG_TO_SORT'); ?>"><?php echo $element->config->get('name'); ?>
	<?php if ($element->getGroup() != 'Core') :?>
		<span>(<?php echo $element->getMetaData('name'); ?>)</span>
	<?php endif;?>
	</div>
	<div class="config">
		<?php echo $form->setValues($data)->render($name, 'render'); ?>
		<input type="hidden" name="<?php echo $name;?>[element]" value="<?php echo $element->identifier; ?>" />
	</div>
</li>
