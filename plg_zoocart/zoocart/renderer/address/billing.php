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

<?php if($this->checkPosition('name')): ?>
<div class="zx-zoocart-address-name">
	<strong>
		<?php echo $this->renderPosition('name', array('style' => 'default')); ?>
	</strong>
</div>
<?php endif; ?>

<?php if($this->checkPosition('address')): ?>
<div class="zx-zoocart-address-resume">
	<?php echo $this->renderPosition('address', array('style' => 'comma')); ?>
</div>
<?php endif; ?>

<?php if($this->checkPosition('details')): ?>
<div class="zx-zoocart-address-details">
	<?php echo $this->renderPosition('details', array('style' => 'comma')); ?>
</div>
<?php endif; ?>