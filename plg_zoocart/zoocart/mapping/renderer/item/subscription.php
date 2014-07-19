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

<tr data-orderitem-id="<?php echo $orderitem->id; ?>">

	<!-- Name -->
	<td class="name">
		<?php if ($this->checkPosition('name')) : ?>
			<?php echo $this->renderPosition('name', array('style' => 'block')); ?>
		<?php else : ?>
			<?php echo $item->name; ?>
		<?php endif; ?>
		<!-- Subscription output: -->
		<?php if(!empty($orderitem->subscription)):?>
			<?php
			$subs = json_decode($orderitem->subscription);
			?>
			<div class="subscription-data">
				<?php echo JText::sprintf('PLG_ZOOCART_SUBSCRIPTION_LINE', $subs->duration); ?>
			</div>
		<?php endif;?>
	</td>

</tr>