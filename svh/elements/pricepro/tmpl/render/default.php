<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
	
	if ($this->app->zoocart->getConfig()->get('show_price_with_tax', 1)) {
		$price = $this->getGrossPrice();
	} else {
		$price = $this->getNetPrice();
	}
	
	echo '<div class="price-pro">'.$this->formatNumber($price). '</div>';
?>