<?php
/**
 * @package		ZOOcart
 * @author		ZOOlanders http://www.zoolanders.com
 * @copyright	Copyright (C) JOOlanders, SL
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// get tip content
$tip = $this->app->zoocart->informer->popout();

if(!empty($tip))
{
	echo '<div class="uk-alert uk-alert-warning"><strong>'.JText::_('PLG_ZOOCART_INFORMER_TIP').'</strong>: '.$tip.'</div>';
}