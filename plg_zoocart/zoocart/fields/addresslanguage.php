<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$languages = JFactory::getLanguage()->getKnownLanguages(JPATH_SITE);
?>
<?php foreach ($languages as $tag => $lang) : ?>
	<?php 
	$lang_name = explode(' ', $lang['name']);
	?>
		<p><?php echo $lang_name[0]; ?> <input type="text" name="<?php echo $control_name.'['.$name.']['.$lang['tag'].']'; ?>" value="<?php echo @$value[$lang['tag']]; ?>" /></p>
<?php endforeach; ?>