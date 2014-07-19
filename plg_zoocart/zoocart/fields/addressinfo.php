<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$options = array(
	'name' 			=> JText::_('PLG_ZLFRAMEWORK_NAME'),
	'address' 		=> JText::_('PLG_ZLFRAMEWORK_ADDRESS'),
	'company' 		=> JText::_('PLG_ZOOCART_ADDRESS_COMPANY'),
	'country' 		=> JText::_('PLG_ZLFRAMEWORK_ADDRESS_COUNTRY'),
	'state' 		=> JText::_('PLG_ZLFRAMEWORK_ADDRESS_STATE'),
	'city' 			=> JText::_('PLG_ZLFRAMEWORK_ADDRESS_CITY'),
	'zip' 			=> JText::_('PLG_ZLFRAMEWORK_ADDRESS_ZIP'),
	'vat' 			=> JText::_('PLG_ZOOCART_VIES_VAT'),
	'personal_id'	=> JText::_('PLG_ZOOCART_PERSONAL_ID'),
	'phone'			=> JText::_('PLG_ZLFRAMEWORK_PHONE'),
	'other' 		=> JText::_('PLG_ZLFRAMEWORK_OTHER')
);
?>
<select name="<?php echo $control_name.'['.$name.']'; ?>">
	<?php foreach ($options as $v => $label) : ?>
	<option value="<?php echo $v; ?>" <?php if ($value == $v || (!$value && $v == 'other')) echo 'selected="selected"'; ?>><?php echo $label; ?></option>
	<?php endforeach; ?>
</select>
