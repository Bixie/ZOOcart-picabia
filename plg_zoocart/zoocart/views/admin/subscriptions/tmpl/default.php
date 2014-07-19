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

<div id="zoocart-subscriptions">

	<!-- main menu -->
	<?php echo $this->partial('zlmenu'); ?>

	<!-- informer -->
	<?php echo $this->partial('informer'); ?>

	<!-- main content -->
	<div class="tm-main uk-panel uk-panel-box">

		<form id="adminForm" class="uk-form" action="<?php echo $this->component->link(); ?>" method="post" name="adminForm" accept-charset="utf-8">
			<?php if($this->pagination->total() > 0) : ?>
					<table class="uk-table">
					<thead>
						<tr>
							<th class="tm-table-width-minimum">
								<input type="checkbox" class="tm-check-all" />
							</th>
							<th>
								<?php echo $this->app->html->_('grid.sort', 'PLG_ZLFRAMEWORK_ID', 's.`id`', @$this->lists['order_Dir'], @$this->lists['order']); ?>
							</th>
							<th>
								<?php echo $this->app->html->_('grid.sort', 'PLG_ZLFRAMEWORK_USER', 'u.`name`', @$this->lists['order_Dir'], @$this->lists['order']); ?>
							</th>
							<th>
								<?php echo $this->app->html->_('grid.sort', 'PLG_ZLFRAMEWORK_ITEM', 'i.`id`', @$this->lists['order_Dir'], @$this->lists['order']); ?>
							</th>
							<th>
								<?php echo JText::_('PLG_ZLFRAMEWORK_ORDER'); ?>
							</th>
							<th>
								<?php echo JText::_('PLG_ZLFRAMEWORK_VALID_FROM'); ?>
							</th>
							<th>
								<?php echo JText::_('PLG_ZLFRAMEWORK_VALID_TO'); ?>
							</th>
							<!-- status -->
							<th class="uk-text-center">
								<?php echo $this->app->html->_('grid.sort', 'PLG_ZLFRAMEWORK_STATUS', 's.`published`', @$this->lists['order_Dir'], @$this->lists['order']); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php for ($i=0, $n=count($this->resources); $i < $n; $i++) :
							$row = $this->resources[$i];
							$data = array('id'=>$row->id);
						?>
						<tr data-row="<?php echo htmlentities(json_encode($data)); ?>">
							<td>
								<input type="checkbox" name="cid[]" value="<?php echo $row->id; ?>" />
							</td>
							<!-- ID -->
							<td>
								<a class="zc-badge" href="<?php echo $this->component->link(array('controller' => $this->controller, 'task' => 'edit', 'cid[]' => $row->id)); ?>">#<?php echo sprintf('%05d', $row->id); ?></a>
							</td>
							<!-- name -->
							<td>
								<?php echo $row->user_name; ?>
							</td>
							<!-- item -->
							<td>
								<?php echo $row->level_name; ?>
							</td>
							<!-- order -->
							<td>
								<a href="<?php echo $this->app->zl->link(array('controller'=>'orders', 'task'=>'edit', 'cid[]'=> (int)$row->order_id), false); ?>"><?php echo '#'.$row->order_id; ?></a>
							</td>
							<!-- publish up -->
							<td>
								<?php $crt = strtotime($row->publish_up);
								if($crt>0){
									echo $this->app->html->_('date', $row->publish_up, 'Y-m-d H:i', $this->app->date->getOffset());
								}else{
									echo '-';
								} ?>
							</td>
							<!-- publish down -->
							<td>
								<?php $exp = strtotime($row->publish_down);
								if($exp>0){
									echo $this->app->html->_('date', $row->publish_down, 'Y-m-d H:i', $this->app->date->getOffset());
								}else{
									echo '-';
								} ?>
							</td>
							<!-- status -->
							<?php
								$title = JText::_($row->published == 1 ? 'PLG_ZLFRAMEWORK_PUBLISHED' : 'PLG_ZLFRAMEWORK_UNPUBLISHED');
								$class = $row->published == 1 ? 'check uk-text-success' : 'times uk-text-danger';
							?>
							<td class="uk-text-center">
								<!-- status -->
								<?php
								$title = JText::_($row->published == 1 ? 'PLG_ZLFRAMEWORK_ENABLED' : 'PLG_ZLFRAMEWORK_DISABLED');
								$title = $this->app->zlfw->html->tooltipText($title, 'PLG_ZLFRAMEWORK_TOGGLE_STATE');
								$class = $row->published == 1 ? 'check uk-text-success' : 'times uk-text-danger';
								?>
								<div class="zl-x-status uk-text-center">
									<a href="#" data-uk-tooltip title="<?php echo $title; ?>">
										<i class="uk-icon-<?php echo $class; ?>"></i>
									</a>
								</div>
							</td>
						</tr>
					<?php endfor; ?>
					</tbody>
				</table>
        		<!-- pagination -->
				<?php if ($pagination = $this->pagination->render($this->pagination_link)) : ?>
				<ul class="uk-pagination">
					<?php echo $pagination; ?>
				</ul>
				<?php endif; ?>

				<?php
				else :
					// Empty list message:
					$title   = JText::_('PLG_ZOOCART_CONFIG_NO_SUBSCRIPTIONS_YET');
					$message = JText::_('PLG_ZOOCART_CONFIG_SUBSCRIPTION_MANAGER_DESC');
					echo $this->partial('message', compact('title', 'message'));
        		endif;
				?>

			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
			<?php echo $this->app->html->_('form.token'); ?>
		</form>
	</div>
	<script type="text/javascript">
		jQuery(document).ready(function($){
			// set lang strings
			$.zx.lang.push({
				'ZL_TIP_STATUS_ENABLED': '<?php echo $this->app->zlfw->html->tooltipText('PLG_ZLFRAMEWORK_ENABLED', 'PLG_ZLFRAMEWORK_TOGGLE_STATE'); ?>',
				'ZL_TIP_STATUS_DISABLED': '<?php echo $this->app->zlfw->html->tooltipText('PLG_ZLFRAMEWORK_DISABLED', 'PLG_ZLFRAMEWORK_TOGGLE_STATE'); ?>'
			});

			// init script
			$('#zoocart-subscriptions' )
				.zx('zoocartTogglable');
		});
	</script>
</div>