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

<?php if ($this->app->request->getString('format') == 'raw') : ?>
	<?php echo $this->message; ?>
<?php else : ?>
<div id="zoocart-container" class="zx">
	<?php echo $this->message; ?>
</div>
<?php endif; ?>