<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// load assets
$this->app->document->addScript('zlfw:vendor/zlux/js/uikit/addons/datepicker.min.js');

// set renderer
$address_renderer = $this->app->renderer->create('address')->addPath(array($this->app->path->path('component.site:'), $this->app->path->path('zoocart:')));

?>

<div id="zoocart-orders">

	<!-- main menu -->
	<?php echo $this->partial('zlmenu'); ?>

	<!-- informer -->
	<?php echo $this->partial('informer'); ?>

	<!-- main content -->
	<div class="tm-main uk-panel uk-panel-box">
		<form id="adminForm" class="uk-form" action="<?php echo $this->component->link(); ?>" method="post" name="adminForm" accept-charset="utf-8">

			<?php if($this->pagination->total() > 0) : ?>
			<fieldset class="uk-text-right">
				<?php echo $this->lists['select_state'];?>

				<!-- created on from -->
				<input type="text" name="created_on_from" id="created_on_from" data-uk-datepicker="{format:\'YYYY-MM-DD HH:mm:SS\'}" value="<?php echo @$this->lists['created_on_from']; ?>" placeholder="<?php echo JText::_('Created from'); ?>">
				<!-- created on to -->
				<input type="text" name="created_on_to" id="created_on_to" data-uk-datepicker="{format:\'YYYY-MM-DD HH:mm:SS\'}" value="<?php echo @$this->lists['created_on_to']; ?>" placeholder="<?php echo JText::_('Created until'); ?>">

				<button class="uk-button uk-button-small" onclick="this.form.submit();"><?php echo JText::_('PLG_ZOOCART_FILTER'); ?></button>
				<button class="uk-button uk-button-small" onclick="document.getElementById('created_on_from').value='';document.getElementById('filter_state').value='';document.getElementById('created_on_to').value='';this.form.submit();"><?php echo JText::_('PLG_ZLFRAMEWORK_RESET'); ?></button>

			</fieldset>
			<table class="uk-table">
				<thead>
					<tr>
						<th class="tm-table-width-minimum">
							<input type="checkbox" class="tm-check-all" />
						</th>
						<th>
							<?php echo $this->app->html->_('grid.sort', 'PLG_ZOOCART_ORDER_ID', 'id', @$this->lists['order_Dir'], @$this->lists['order']); ?>
						</th>
						<th>
							<?php echo $this->app->html->_('grid.sort', 'PLG_ZLFRAMEWORK_USER', 'user_id', @$this->lists['order_Dir'], @$this->lists['order']); ?>
						</th>
						<th>
							Email
						</th>
						<th>
							<?php echo $this->app->html->_('grid.sort', 'PLG_ZOOCART_ADDRESS_SHIPPING', 'shipping_address', @$this->lists['order_Dir'], @$this->lists['order']); ?>
						</th>
						<th>
							<?php echo $this->app->html->_('grid.sort', 'PLG_ZLFRAMEWORK_NET_TOTAL', 'net', @$this->lists['order_Dir'], @$this->lists['order']); ?>
						</th>
						<th>
							<?php echo $this->app->html->_('grid.sort', 'PLG_ZLFRAMEWORK_TOTAL', 'total', @$this->lists['order_Dir'], @$this->lists['order']); ?>
						</th>
						<th>
							<?php echo $this->app->html->_('grid.sort', 'PLG_ZOOCART_ORDER_STATE', 'state', @$this->lists['order_Dir'], @$this->lists['order']); ?>
						</th>
						<th>
							<?php echo $this->app->html->_('grid.sort', 'PLG_ZOOCART_PAYMENT_METHOD', 'payment_method', @$this->lists['order_Dir'], @$this->lists['order']); ?>
						</th>
						<th>
							<?php echo $this->app->html->_('grid.sort', 'PLG_ZLFRAMEWORK_CREATED_ON', 'created_on', @$this->lists['order_Dir'], @$this->lists['order']); ?>
						</th>
						<th>
							<?php echo $this->app->html->_('grid.sort', 'PLG_ZLFRAMEWORK_MODIFIED_ON', 'modified_on', @$this->lists['order_Dir'], @$this->lists['order']); ?>
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
						<td>
							<a class="zc-badge" href="<?php echo $this->component->link(array('controller' => $this->controller, 'task' => 'edit', 'cid[]' => $row->id));  ?>">#<?php echo sprintf('%05d', $row->id); ?></a>
						</td>
						<td>
							<?php echo JFactory::getUser($row->user_id)->name; ?>
						</td>
						<td>
							<?php echo JFactory::getUser($row->user_id)->email; ?>
						</td>
						<td>
							<?php echo $address_renderer->render('address.shipping', array('item' => $row->getShippingAddress())); ?>
						</td>
						<td>
							<?php echo $this->app->zoocart->currency->format($row->getSubtotal()); ?>
						</td>
						<td>
							<?php echo $this->app->zoocart->currency->format($row->getTotal()); ?>
						</td>
						<td>
							<?php echo JText::_($row->getState()->name); ?>
						</td>
						<td>
							<?php echo ucfirst($row->payment_method); ?>
						</td>
						<td>
							<?php echo $this->app->html->_('date', $row->created_on, JText::_('DATE_FORMAT_LC3'), $this->app->date->getOffset()); ?>
						</td>
						<td>
							<?php echo $this->app->html->_('date', $row->modified_on, JText::_('DATE_FORMAT_LC3'), $this->app->date->getOffset()); ?>
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
        	$title   = JText::_('PLG_ZOOCART_CONFIG_NO_ORDERS_YET');
			$message = JText::_('PLG_ZOOCART_CONFIG_ORDERS_MANAGER_DESC');
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