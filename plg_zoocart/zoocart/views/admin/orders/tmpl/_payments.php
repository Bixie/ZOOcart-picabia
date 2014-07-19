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

<?php if (count($payments)) : ?>
<table class="uk-table uk-table-striped uk-table-condensed">
	<thead>
		<tr>
			<th><?php echo JText::_('PLG_ZOOCART_TRANSACTION_ID'); ?></th>
			<th><?php echo JText::_('PLG_ZLFRAMEWORK_STATUS'); ?></th>
			<th><?php echo JText::_('PLG_ZOOCART_PAYMENT_METHOD'); ?></th>
			<th><?php echo JText::_('PLG_ZLFRAMEWORK_TOTAL'); ?></th>
			<th><?php echo JText::_('PLG_ZLFRAMEWORK_DATE'); ?></th>
		</tr>
	</thead>
	<tbody>		
		<?php foreach($payments as $payment) :?>
		<tr>
			<td><?php echo $payment->transaction_id; ?></td>
			<td><span class="uk-icon <?php echo $payment->status?'uk-icon-check uk-text-success':'uk-icon-warning uk-text-warning'; ?>"></span></td>
			<td><?php echo $payment->payment_method; ?></td>
			<td><?php echo $this->app->zoocart->currency->format($payment->total); ?></td>
			<td><?php echo $this->app->html->_('date', $payment->created_on, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset()); ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else : ?>

<div class="uk-alert uk-alert-warnin">
	<?php echo JText::_('PLG_ZOOCART_NO_PAYMENTS_YET'); ?>
</div>

<?php endif; ?>