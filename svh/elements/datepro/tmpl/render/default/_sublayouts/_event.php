<?php
/**
* @package		com_zoo
* @author    	JOOlanders
* @copyright 	Copyright (C) JOOlanders
* @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

	if($this->get('valueend')){

		$format = $params->find('specific._date_format') == 'custom' ? $params->find('specific._custom_format') : $params->find('specific._date_format');
		$html = $this->app->html->_('date', $this->get('value', ''), $this->app->date->format($format), $this->app->date->getOffset());

		$html .= ' '.JText::_('PLG_ZLELEMENTS_DP_UNTIL').' ';

		$override_ed_format = $params->find('layout._override_ed_format');
		if($override_ed_format == 1 || (($override_ed_format == 2) && $this->daysDiference($this->get('value'), $this->get('valueend')) == 0)){
			$format = $params->find('specific._ed_format') == 'custom' ? $params->find('specific._ed_custom_format') : $params->find('specific._ed_format');
		}
		$html .= $this->app->html->_('date', $this->get('valueend', ''), $this->app->date->format($format), $this->app->date->getOffset());

		echo $html;

	} else {
		echo JText::_('PLG_ZLELEMENTS_DP_FROM').' '.$this->renderValue($params, 'value');
	}