<?php
/**
* @package   ZOO Component
* @file      edit.php
* @version   2.1.0 BETA3 September 2010
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2010 YOOtheme GmbH
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

<div class="repeatable-content">

	<div class="row">
		<?php
			if ($this->config->find('specific._editor') == 'joomlaeditor' && $trusted_mode) 
			{
				echo $this->app->system->editor->display($this->getControlName('value'), htmlspecialchars( $this->get('value'), ENT_QUOTES, 'UTF-8' ), null, null, 20, 60, array());
			} 
			else if($this->config->find('specific._editor') == 'customeditor' && $trusted_mode) 
			{
				echo $this->app->html->_('control.textarea', $this->getControlName('value'), $this->get('value'), 'class="tinymce '.$this->config->find('specific._editor_size').'" cols=20 rows=10');
			} 
			else 
			{
				echo $this->app->html->_('control.textarea', $this->getControlName('value'), $this->get('value'), 'cols=20 rows=10');
			}
		?>
	</div>
	
</div>