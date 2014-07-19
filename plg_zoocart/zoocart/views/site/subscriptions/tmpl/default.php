<?php
/**
 * @package		ZOOcart
 * @author		ZOOlanders http://www.zoolanders.com
 * @copyright	Copyright (C) JOOlanders, SL
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// add assets:
$this->app->document->addStylesheet('zoocart:assets/css/site.css');
$this->app->document->addScript('zoocart:assets/js/zoocart.js');

?>

<div id="zoocart-container" class="zx">

	<?php if(count($this->resources)) : ?>
	<form id="zoocart-site-default" class="uk-form zoocart-subscriptions" action="<?php echo $this->component->link(); ?>" method="post" accept-charset="utf-8">

		<?php echo $this->partial('informer'); ?>

		<!-- title -->
		<h2 class="uk-h3"><?php echo JText::_('PLG_ZOOCART_MY_SUBSCRIPTIONS'); ?></h2>

		<table class="uk-table uk-table-hover uk-table-striped uk-table-condensed">
			<thead>
				<tr>
					<th><?php echo JText::_('PLG_ZOOCART_SUBSCRIPTION'); ?></th>
					<th><?php echo JText::_('PLG_ZLFRAMEWORK_ORDER'); ?></th>
					<th><?php echo JText::_('PLG_ZLFRAMEWORK_VALID_FROM'); ?></th>
					<th><?php echo JText::_('PLG_ZLFRAMEWORK_VALID_TO'); ?></th>
					<th><?php echo JText::_('PLG_ZLFRAMEWORK_STATE'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($this->resources as $row) : ?>
				<?php $substatus = $this->app->zoocart->subscription->getUserSubscriptionStatus($row->user_id, $row->id); ?>
				<tr>
					<!-- name -->
					<td class="sub-name">
						<?php echo $row->name; ?>
					</td>
					<!-- related order -->
					<td class="sub-order-id">
						<a class="uk-badge" href="<?php echo $this->app->zl->link(array('controller'=>'orders', 'task'=>'view', 'id'=>$row->order_id)); ?>"><?php echo JText::_('PLG_ZLFRAMEWORK_ORDER').' #'.$row->order_id; ?></a>
					</td>
					<!-- valid from -->
					<td class="subl-subscribed">
						<?php
							$null_date = (strtotime($row->publish_up)<=0);
							echo (!$null_date && $substatus->code!=2) ? $this->app->html->_('date', $row->publish_up, JText::_('DATE_FORMAT_LC3'), $this->app->date->getOffset()) : '-';
						?>
					</td>
					<!-- valid to -->
					<td class="subl-publish_down">
						<?php
							$null_date = (strtotime($row->publish_down)<=0);
							echo (!$null_date && $substatus->code!=2) ? $this->app->html->_('date', $row->publish_down, JText::_('DATE_FORMAT_LC3'), $this->app->date->getOffset()) : '-';
						?>
					</td>
					<!-- state -->
					<td class="subl-controls">
					<?php if ($substatus->code==1) : ?>
						<div class="uk-badge uk-badge-success"><?php echo JText::_('PLG_ZOOCART_SUBSCRIPTION_ACTIVE'); ?></div>
						<?php if($substatus->days_left) : ?>
							<div class="uk-badge"><?php echo $substatus->days_left.' '.JText::_('PLG_ZOOCART_SUBSCRIPTION_DAYS_LEFT'); ?></div>
						<?php endif;?>
					<?php elseif ($substatus->code==3) : ?>
						<div class="uk-badge uk-badge-danger">
							<?php echo JText::_('PLG_ZOOCART_SUBSCRIPTION_ALREADY_EXPIRED'); ?>
						</div>
						<?php if ($this->app->zoocart->subscription->isRenewable($row)) : ?>
							<?php
								$renewal_data = $this->app->zoocart->subscription->getRenewalData($row);
								if(!empty($renewal_data)):
							?>
							<a href="<?php echo $this->app->zl->link(array('controller'=>'cart', 'task'=>'renew', 'subid'=>$row->id, $this->app->session->getFormToken()=>1 ), false); ?>" class="uk-badge uk-badge-success">
								<?php echo JText::_('PLG_ZOOCART_SUBSCRIPTION_RENEW'); ?>
							</a>
							<?php endif; ?>
						<?php endif; ?>
					<?php else : ?>
						<div class="uk-badge uk-badge-warning">
							<?php echo JText::_('PLG_ZOOCART_SUBSCRIPTION_INACTIVE'); ?>
						</div>
					<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<!-- pagination -->
		<?php if ($pagination = $this->pagination->render($this->pagination_link)) : ?>
			<ul class="uk-pagination">
				<?php echo $pagination; ?>
			</ul>
		<?php endif; ?>

		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo $this->app->html->_('form.token'); ?>
	</form>

	<?php else: ?>
	<div class="uk-alert uk-alert-warning"><?php echo JText::_('PLG_ZOOCART_USER_NO_SUBSCRIPTIONS');?></div>
	<?php endif; ?>
</div>