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
$form = $element->getConfigForm();
$name = $element->config->get('name', 'New');
$var = 'elements['.$element->identifier.']';

?>

<div class="element-icon edit-element edit-event" title="<?php echo JText::_('PLG_ZLFRAMEWORK_EDIT_ELEMENT'); ?>"></div>
<div class="element-icon delete-element delete-event" title="<?php echo JText::_('PLG_ZLFRAMEWORK_DELETE_ELEMENT'); ?>"></div>
<div class="name sort-event" title="<?php echo JText::_('PLG_ZLFRAMEWORK_DRAG_TO_SORT'); ?>"><?php echo $name; ?> <span>(<?php echo $element->getMetaData('name'); ?>)</span></div>
<div class="config">
	<?php echo $form->render($var); ?>
	<input type="hidden" name="<?php echo $var; ?>[type]" value="<?php echo $element->getElementType(); ?>" />
</div>