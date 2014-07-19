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
$this->app->document->addScript('zlfw:vendor/zlux/js/addons/nestable.js');

// get settings
$settings = $this->app->zoocart->getConfig();

?>

<div id="zoocart-taxes">

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

					<?php if($this->pagination->total() > 0) : ?>

						<!-- fake table header -->
						<div class="tm-table-fake tm-table-fake-header tm-table-fake-header-indent">
							<div class="zl-hidden-mini tm-table-width-minimum">
								<input type="checkbox" class="tm-check-all">
							</div>
							<div class="">
								<?php echo JText::_('PLG_ZOOCART_TAX'); ?>
							</div>
							<div class="uk-width-1-4 uk-width-large-1-10 uk-text-center">
								<?php echo JText::_('PLG_ZLFRAMEWORK_STATUS'); ?>
							</div>
							<?php if ($settings->get('vies_validation')) :?>
							<div class="uk-width-1-4 uk-width-large-1-10 uk-text-center">
								<?php echo JText::_('PLG_ZOOCART_VIES'); ?>
							</div>
							<?php endif; ?>
							<div class="uk-width-1-4 uk-width-large-1-10 uk-text-center">
								<?php echo JText::_('PLG_ZOOCART_TAX_RATE'); ?>
							</div>
						</div>
						<!-- fake table rows -->
						<ul class="uk-nestable" data-uk-nestable="{maxDepth:1}">

							<?php for ($i=0, $n=count($this->resources); $i < $n; $i++) :
								$row = $this->resources[$i];
								$data = array('id' => $row->id);
							?>
							<li data-row='<?php echo json_encode($data); ?>' class="uk-nestable-list-item">
								<div class="uk-nestable-item tm-table-fake">
									<div class="zl-hidden-mini tm-table-width-minimum">
										<div class="uk-nestable-handle"></div>
									</div>
									<div class="zl-hidden-mini tm-table-width-minimum">
										<input type="checkbox" name="cid[]" value="<?php echo $row->id; ?>" />
									</div>
									<!-- link -->
									<div class="">
										<?php
											// set the name by row attributes
											$attrs = array();
											if($tax_class = $row->getTaxClass()) $attrs[] = $tax_class->name;
											foreach (array('country', 'city', 'zip') as $value) {
												if (isset($row->$value) && !empty($row->$value)) $attrs[] = $row->$value;
											}
										?>
										<a href="<?php echo $this->component->link(array('controller' => $this->controller, 'task' => 'edit', 'cid[]' => $row->id));  ?>"><?php echo implode(', ', $attrs); ?></a>
									</div>
									<!-- status -->
									<?php
										$title = JText::_($row->published == 1 ? 'PLG_ZLFRAMEWORK_ENABLED' : 'PLG_ZLFRAMEWORK_DISABLED');
										$title = $this->app->zlfw->html->tooltipText($title, 'PLG_ZLFRAMEWORK_TOGGLE_STATE');
										$class = $row->published == 1 ? 'check uk-text-success' : 'times uk-text-danger';
									?>
									<div class="zl-x-status uk-width-1-4 uk-width-large-1-10 uk-text-center">
										<a href="#" data-uk-tooltip title="<?php echo $title; ?>">
											<i class="uk-icon-<?php echo $class; ?>"></i>
										</a>
									</div>
									<!-- VIES -->
									<?php if ($settings->get('vies_validation')) :
										$title = JText::_($row->vies == 1 ? 'PLG_ZOOCART_VIES_REGISTERED' : 'PLG_ZOOCART_VIES_REGISTERED_NOT');
										$class = $row->vies == 1 ? 'check uk-text-success' : 'times uk-text-danger';
									?>
									<div class="uk-width-1-4 uk-width-large-1-10 uk-text-center">
										<i class="uk-icon-<?php echo $class; ?>" data-uk-tooltip title="<?php echo $title; ?>"></i>
									</div>
									<?php endif; ?>
									<!-- tax rate -->
									<div class="uk-width-1-4 uk-width-large-1-10 uk-text-center">
										<?php echo $row->taxrate; ?>%
									</div>
								</div>
							</li>
							<?php endfor; ?>

						</ul>

						<!-- pagination -->
						<?php if ($pagination = $this->pagination->render($this->pagination_link)) : ?>
						<ul class="uk-pagination">
							<?php echo $pagination; ?>
						</ul>
						<?php endif; ?>

					<!-- if no resources -->
					<?php else :

						$title   = JText::_('PLG_ZOOCART_CONFIG_NO_TAXRULES_YET');
						$message = JText::_('PLG_ZOOCART_CONFIG_TAXRULES_MANAGER_DESC');
						echo $this->partial('message', compact('title', 'message'));

					endif; ?>

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
		$('#zoocart-taxes' )
			.zx('zoocartTogglable')
			.zx('zoocartTaxes');
	});
	</script>
</div>