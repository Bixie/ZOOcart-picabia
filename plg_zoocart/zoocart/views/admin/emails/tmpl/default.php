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

<div id="zoocart-emails">

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
				<form id="adminForm" class="menu-has-level3" action="<?php echo $this->component->link(); ?>" method="post" name="adminForm" accept-charset="utf-8">

				<?php if($this->pagination->total() > 0) : ?>

					<table class="uk-table">
						<thead>
						<tr>
							<th class="tm-table-width-minimum">
								<input type="checkbox" class="tm-check-all" />
							</th>
							<th>
								<?php echo $this->app->html->_('grid.sort', 'PLG_ZLFRAMEWORK_NAME', 'type', @$this->lists['order_Dir'], @$this->lists['order']); ?></th>
							</th>
							<th class="uk-width-1-10 uk-text-center">
								<?php echo $this->app->html->_('grid.sort', 'PLG_ZLFRAMEWORK_PUBLISHED', 'published', @$this->lists['order_Dir'], @$this->lists['order']); ?>
							</th>
						</tr>
						</thead>
						<tbody>
							<?php
							for ($i=0, $n=count($this->resources); $i < $n; $i++) :
								$row = $this->resources[$i];
								$data = array('id' => $row->id);
							?>
							<tr data-row="<?php echo htmlentities(json_encode($data)); ?>">
								<td class="tm-table-width-minimum">
									<input type="checkbox" name="cid[]" value="<?php echo $row->id; ?>" />
								</td>
								<td class="name">
									<a href="<?php echo $this->component->link(array('controller' => $this->controller, 'task' => 'edit', 'cid[]' => $row->id)); ?>"><strong><?php echo JText::_('PLG_ZOOCART_EMAIL_TYPE_'.strtoupper($row->type)); ?></strong></a>
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
					$title   = JText::_('PLG_ZOOCART_CONFIG_NO_EMAILS_YET');
					$message = JText::_('PLG_ZOOCART_CONFIG_EMAILS_MANAGER_DESC');
					echo $this->partial('message', compact('title', 'message'));

				endif;
				?>
			</div>

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
			$('#zoocart-emails').zx('zoocartTogglable' );
		});
	</script>
</div>