<?php
/**
 * @package		ZOOcart
 * @author		ZOOlanders http://www.zoolanders.com
 * @copyright	Copyright (C) JOOlanders, SL
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

?>

<div class="uk-form-row">
	<label class="uk-form-label">
		<?php echo JText::_($title); ?>
	</label>
	<div class="uk-form-controls">
		<?php echo $field; ?>
	</div>
	<?php if(isset($description)) : ?>
	<p class="uk-form-help-block"><?php echo JText::_($description); ?></p>
	<?php endif; ?>
</div>