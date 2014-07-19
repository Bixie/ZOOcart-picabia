<?php
/**
* @package		ZL Elements
* @author    	JOOlanders, SL http://www.zoolanders.com
* @copyright 	Copyright (C) JOOlanders, SL
* @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// init var
$default = $this->config->get('default');

// set default, if item is new
if ($default != '' && $this->_item != null && $this->_item->id == 0) {
	$this->set('value', $default);
}

?>

<div class="row">
	<?php echo $this->app->html->_('control.text', $this->getControlName('value'), $this->get('value'), 'size="30" maxlength="255" class="zx-x-raw" style="display: none;"'); ?>
	<input type="text" size="30" maxlength="255" class="zx-x-pretty" />
</div>