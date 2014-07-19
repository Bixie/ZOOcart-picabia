<?php
/**
 * @package		ZOOcart
 * @author		ZOOlanders http://www.zoolanders.com
 * @copyright	Copyright (C) JOOlanders, SL
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
$tip = $this->app->zoocart->informer->popout();

if(!empty($tip))
{
	echo '<div class="uk-alert uk-alert-danger"><a href="" class="uk-alert-close uk-close"></a>'.$tip.'</div>';
}
