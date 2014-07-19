<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$render_type = $this->app->request->getCmd('renderer', 'submission');

?>
<li class="element hideconfig" data-element="<?php echo $element->identifier; ?>">
	<div class="element-icon edit-element edit-event" title="<?php echo JText::_('PLG_ZLFRAMEWORK_EDIT_ELEMENT'); ?>"></div>
	<div class="element-icon delete-element delete-event" title="<?php echo JText::_('PLG_ZLFRAMEWORK_DELETE_ELEMENT'); ?>"></div>
	<div class="name sort-event" title="<?php echo JText::_('PLG_ZLFRAMEWORK_DRAG_TO_SORT'); ?>"><?php echo $element->config->get('name'); ?>
		<span>(<?php echo $element->getMetaData('name'); ?>)</span>
	</div>
	<div class="config">
		<?php echo $element->getConfigForm()->setValues($data)->render($element->identifier, $render_type); ?>
		<input type="hidden" name="<?php echo $element->identifier; ?>[element]" value="<?php echo $element->identifier; ?>" />
	</div>
</li>
