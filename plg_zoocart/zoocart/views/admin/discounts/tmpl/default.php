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

<div id="zoocart-discounts">

	<!-- main menu -->
	<?php echo $this->partial('zlmenu'); ?>

	<!-- informer -->
	<?php echo $this->partial('informer'); ?>

	<!-- main content -->
	<div class="tm-main uk-panel uk-panel-box">
			<form id="adminForm" class="uk-form" action="<?php echo $this->component->link(); ?>" method="post" name="adminForm" accept-charset="utf-8">

				<?php if($this->pagination->total() > 0) : ?>
					<table class="uk-table">
					<thead class="zx-hidden-mini">
						<tr>
							<th class="tm-table-width-minimum">
								<input type="checkbox" class="tm-check-all">
							</th>
							<!-- name -->
							<th>
								<?php echo $this->app->html->_('grid.sort', 'PLG_ZLFRAMEWORK_NAME', 'name', @$this->lists['order_Dir'], @$this->lists['order']); ?>
							</th>
							<!-- code -->
							<th class="uk-width-2-10">
								<?php echo JText::_('PLG_ZOOCART_DISCOUNT_CODE'); ?>
							</th>
							<!-- discount -->
							<th class="uk-width-1-10 uk-text-right">
								<?php echo JText::_('PLG_ZOOCART_DISCOUNT'); ?>
							</th>
							<!-- restrictions -->
							<th class="uk-width-2-10 uk-visible-large">
								<?php echo JText::_('PLG_ZLFRAMEWORK_RESTRICTIONS'); ?>
							</th>
							<!-- used -->
							<th class="uk-width-1-10 uk-text-center uk-visible-large">
								<?php echo $this->app->html->_('grid.sort', 'PLG_ZOOCART_DISCOUNT_USED', 'used', @$this->lists['order_Dir'], @$this->lists['order']); ?>
							</th>
							<!-- status -->
							<th class="uk-width-1-10 uk-text-center">
								<?php echo $this->app->html->_('grid.sort', 'PLG_ZLFRAMEWORK_STATUS', 'published', @$this->lists['order_Dir'], @$this->lists['order']); ?>
							</th>
						</tr>
						</thead>
						<tbody>
						<?php for ($i=0, $n=count($this->resources); $i < $n; $i++) :
							$row	= $this->resources[$i];
							$v_from = (int)strtotime($row->valid_from);
							$v_to   = (int)strtotime($row->valid_to);
							$data = array('id'=>$row->id);
						?>
						<tr data-row="<?php echo htmlentities(json_encode($data)); ?>">
								<td class="tm-table-width-minimum">
									<input type="checkbox" name="cid[]" value="<?php echo $row->id; ?>" />
								</td>
								<!-- name -->
								<td>
									<a href="<?php echo $this->component->link(array('controller' => $this->controller, 'task' => 'edit', 'cid[]' => $row->id)); ?>"><?php echo $row->name; ?></a>
								</td>
								<!-- code -->
								<td class="uk-width-2-10 zx-hidden-mini">
									<?php echo $row->code; ?>
								</td>
								<!-- discount -->
								<td class="uk-width-1-10 uk-text-right <?php echo (1==$row->type)?' perc':''; ?>">
									<?php echo $row->discount; ?><?php echo (1==$row->type)?'%':''; ?>
								</td>
								<!-- retrictions -->
								<td class="uk-width-2-10 uk-visible-large">
									<?php if($v_from>0 || $v_to>0):?><span class="r-icon time hasTip" title="<?php echo JText::_('PLG_ZOOCART_DISCOUNT_RESTR_BY_TIME'); ?>"></span><?php endif; ?>
									<?php if(!empty($row->usergroups)):?><span class="r-icon group hasTip" title="<?php echo JText::_('PLG_ZOOCART_DISCOUNT_RESTR_BY_GROUP'); ?>"></span><?php endif; ?>
									<?php if(!empty($row->hits_per_user)):?><span class="r-icon counter hasTip" title="<?php echo JText::_('PLG_ZOOCART_DISCOUNT_RESTR_BY_HITS'); ?>"></span><?php endif; ?>
								</td>
								<!-- used -->
								<td class="uk-width-1-10 uk-text-center uk-visible-large">
									<?php echo $row->used; ?>
								</td>
								<!-- status -->
								<?php
								$title = JText::_($row->published == 1 ? 'PLG_ZLFRAMEWORK_ENABLED' : 'PLG_ZLFRAMEWORK_DISABLED');
								$title = $this->app->zlfw->html->tooltipText($title, 'PLG_ZLFRAMEWORK_TOGGLE_STATE');
								$class = $row->published == 1 ? 'check uk-text-success' : 'times uk-text-danger';
								?>
								<td class="uk-width-1-10 uk-text-center zx-hidden-mini">
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
					$title   = JText::_('PLG_ZOOCART_CONFIG_NO_DISCOUNTS_YET');
					$message = JText::_('PLG_ZOOCART_CONFIG_DISCOUNTS_MANAGER_DESC');
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
			$('#zoocart-discounts').zx('zoocartTogglable' );
		});
	</script>
</div>