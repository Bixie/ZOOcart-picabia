<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div id="zoocart-orderstates">

	<!-- main menu -->
	<?php echo $this->partial('zlmenu'); ?>

	<!-- informer -->
	<?php echo $this->partial('informer'); ?>

	<!-- main content -->
	<div class="tm-main uk-panel uk-panel-box">
		<div class="uk-grid">
			<div class="uk-width-medium-1-10">
				<?php echo $this->partial('settings_tab', array('current' => 'orders')); ?>
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
								<th class="uk-width-2-6">
									<?php echo $this->app->html->_('grid.sort', 'PLG_ZLFRAMEWORK_NAME', 'name', @$this->lists['order_Dir'], @$this->lists['order']); ?>
								</th>
								<th class="uk-width-4-6">
									<?php echo JText::_('PLG_ZLFRAMEWORK_DESCRIPTION'); ?>
								</th>
							</tr>
						</thead>
						<tbody>
						<?php for ($i=0, $n=count($this->resources); $i < $n; $i++) :
							$row = $this->resources[$i];
						?>
							<tr>
								<td>
									<input type="checkbox" name="cid[]" value="<?php echo $row->id; ?>" />
								</td>
								<td class="uk-width-2-6">
									<a href="<?php echo $this->component->link(array('controller' => $this->controller, 'task' => 'edit', 'cid[]' => $row->id));  ?>"><?php echo JText::_($row->name); ?></a>
								</td>
								<td class="uk-width-4-6">
									<?php echo JText::_($row->description); ?>
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

						$title   = JText::_('PLG_ZOOCART_CONFIG_NO_ORDERSTATES_YET');
						$message = JText::_('PLG_ZOOCART_CONFIG_ORDERSTATES_MANAGER_DESC');
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
</div>