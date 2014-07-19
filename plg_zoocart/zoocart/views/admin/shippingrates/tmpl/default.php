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

<div id="zoocart-rates">

	<!-- main menu -->
	<?php echo $this->partial('zlmenu'); ?>

	<!-- informer -->
	<?php echo $this->partial('informer'); ?>

	<!-- main content -->
	<div class="tm-main uk-panel uk-panel-box">
		<div class="uk-grid">
			<div class="uk-width-medium-1-10">
				<?php echo $this->partial('settings_tab'); ?>
			</div>
			<div class="uk-width-medium-9-10">
				<form id="adminForm" class="uk-form" action="<?php echo $this->component->link(); ?>" method="post" name="adminForm" accept-charset="utf-8">
				<?php
				if($this->pagination->total() > 0) : ?>

					<table class="uk-table">
						<thead>
							<tr>
								<th>
									<input type="checkbox" class="tm-check-all" />
								</th>
								<th class="uk-width-2-10">
									<?php echo $this->app->html->_('grid.sort', 'PLG_ZLFRAMEWORK_NAME', 'name', @$this->lists['order_Dir'], @$this->lists['order']); ?>
								</th>
								<th class="uk-width-2-10">
									<?php echo JText::_('PLG_ZLFRAMEWORK_TYPE'); ?>
								</th>
								<th class="uk-width-2-10">
									<?php echo JText::_('PLG_ZLFRAMEWORK_PRICE'); ?>
								</th>
								<th class="uk-width-2-10">
									<?php echo JText::_('PLG_ZOOCART_PRICE_CONSTR'); ?>
								</th>
								<th class="uk-width-2-10">
									<?php echo JText::_('PLG_ZOOCART_QTY_CONSTR'); ?>
								</th>
								<th class="uk-width-2-10">
									<?php echo JText::_('PLG_ZOOCART_WEIGHT_CONSTR'); ?>
								</th>
								<th class="uk-width-2-10">
									<?php echo JText::_('PLG_ZOOCART_REG_CONSTR'); ?>
								</th>
								<th class="uk-width-2-10 uk-text-center">
									<?php echo JText::_('PLG_ZLFRAMEWORK_STATUS'); ?>
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
								<td class="uk-width-2-10">
									<a href="<?php echo $this->component->link(array('controller' => $this->controller, 'task' => 'edit', 'cid[]' => $row->id));  ?>"><?php echo $row->name; ?></a>
								</td>
								<td class="uk-width-1-10">
									<?php echo $row->type; ?>
								</td>
								<td class="uk-width-1-10">
									<?php echo $row->price; ?>
								</td>
								<td class="uk-width-1-10">
									<?php echo $this->app->zoocart->shipping->fromTo( (int)$row->price_from , (int)$row->price_to); ?>
								</td>
								<td class="uk-width-1-10">
									<?php echo $this->app->zoocart->shipping->fromTo( (int)$row->quantity_from , (int)$row->quantity_to); ?>
								</td>
								<td class="uk-width-1-10">
									<?php echo $this->app->zoocart->shipping->fromTo( (int)$row->weight_from , (int)$row->weight_to); ?>
								</td>
								<td class="uk-width-1-10">
									<?php echo $row->countries; ?>
								</td>
								<td class="uk-width-1-10 uk-text-center">
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

						$title   = JText::_('PLG_ZOOCART_CONFIG_NO_SHIPPINGRATES_YET');
						$message = JText::_('PLG_ZOOCART_CONFIG_SHIPPINGRATES_MANAGER_DESC');
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
		</div>
	</div>
	<script type="text/javascript">
		jQuery(document).ready(function($){
			// set lang strings
			$.zx.lang.push({
				'ZL_TIP_STATUS_ENABLED': '<?php echo $this->app->zlfw->html->tooltipText('PLG_ZLFRAMEWORK_ENABLED', 'PLG_ZLFRAMEWORK_TOGGLE_STATE'); ?>',
				'ZL_TIP_STATUS_DISABLED': '<?php echo $this->app->zlfw->html->tooltipText('PLG_ZLFRAMEWORK_DISABLED', 'PLG_ZLFRAMEWORK_TOGGLE_STATE'); ?>'
			});

			// init script
			$('#zoocart-rates' )
				.zx('zoocartTogglable');
		});
	</script>
</div>
