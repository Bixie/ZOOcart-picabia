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
<table class="uk-table uk-table-striped uk-table-condensed">
	<thead>
		<tr>
			<th><?php echo JText::_('PLG_ZLFRAMEWORK_DATE'); ?></th>
			<th><?php echo JText::_('PLG_ZOOCART_CHANGES');?></th>
			<th><?php echo JText::_('PLG_ZLFRAMEWORK_USER'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?echo $this->app->html->_('date', $order->created_on, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset()); ?></td>
			<td>
				<ul class="uk-list">
					<li><?php echo JText::_('PLG_ZOOCART_ORDER_CREATED'); ?></li>
				</ul>
			</td>
			<td>
				<?php echo $this->app->user->get($order->user_id)->username; ?>
			</td>
		</tr>
		<?php foreach($histories as $history) :?>
			<tr>
			<?php
				// Retrieve all related records:
				$sids = explode(',', $history->records);
				$stories = array();
				if(!empty($sids)){
					foreach($sids as $sid){
						$stories[] = $this->app->zoocart->table->orderhistories->get($sid);
					}
				}
				$date = date("d.m.Y H:i:s", $history->timestamp);
			?>
			<td><?echo $this->app->html->_('date', $history->timestamp, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset()); ?></td>
			<td>
				<ul class="uk-list">
					<?php foreach($stories as $story):?>
						 <li><?php echo $this->app->zoocart->order->getLogPhrase($story->property, $story->value_old, $story->value_new); ?></li>
					<?php endforeach;?>
				</ul>
			</td>
			<td><?echo $history->username; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>