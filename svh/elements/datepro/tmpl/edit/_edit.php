<?php
/**
* @package		ZL Elements
* @author    	JOOlanders SL http://www.zoolanders.com
* @copyright 	Copyright (C) JOOlanders SL
* @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$time = $this->config->find('specific._time', false); // by default no time option

?>

<?php if ($this->config->find('specific._mode') == 'event') : ?>

	<span class="first">
		<?php $value = $this->get('value', ''); $value = !empty($value) ? $this->app->html->_('date', $value, $this->app->date->format($edit_date_format), $this->app->date->getOffset()) : ''; ?>
		<?php echo JText::_('PLG_ZLELEMENTS_DP_FROM').' '.$this->app->html->_('zoo.calendar', $value, $this->getControlName('value'), $this->getControlName('value'), array('class' => 'calendar-element'), $time); ?>
	</span>
	
	<span>
		<?php $value = $this->get('valueend', ''); $value = !empty($value) ? $this->app->html->_('date', $value, $this->app->date->format($edit_date_format), $this->app->date->getOffset()) : ''; ?>
		<?php echo '&nbsp;&nbsp;'.JText::_('PLG_ZLELEMENTS_DP_UNTIL').' '.$this->app->html->_('zoo.calendar', $value, $this->getControlName('valueend'), $this->getControlName('valueend'), array('class' => 'calendar-element'), $time); ?>
	</span>

<?php else : ?>

	<?php $value = $this->get('value', ''); $value = !empty($value) ? $this->app->html->_('date', $value, $this->app->date->format($edit_date_format), $this->app->date->getOffset()) : ''; ?>
	<?php echo $this->app->html->_('zoo.calendar', $value, $this->getControlName('value'), $this->getControlName('value'), array('class' => 'calendar-element'), $time); ?>
	
<?php endif; ?>