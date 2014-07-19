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

<div id="zoocart-addresstypes">

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
				<form id="adminForm" class="uk-form" action="index.php" method="post" name="adminForm" accept-charset="utf-8">

					<table class="uk-table">
						<thead>
							<tr>
								<th>
									<?php echo JText::_('PLG_ZLFRAMEWORK_NAME'); ?>
								</th>
								<th>
									<?php echo JText::_('PLG_ZLFRAMEWORK_LAYOUTS'); ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$type = $this->address;
								$edit = $this->app->component->active->link(array('controller' => $this->controller, 'task' => 'editelements', 'cid[]' => $type->id));
							?>
							<tr>
								<td>
									<span class="editlink hasTip" title="<?php echo JText::_('PLG_ZLFRAMEWORK_EDIT_ELEMENTS');?>::<?php echo $type->name; ?>">
										<a href="<?php echo $edit; ?>"><?php echo $type->name; ?></a>
									</span>
								</td>
								<td>
									<?php foreach ($this->extensions as $extension) {

										$renderer = $this->app->renderer->create()->addPath($extension['path']);

										if (count($renderer->getLayouts('address'))) {
											echo '<div>'.ucfirst($extension['name']).': ';

											$links = array();
											foreach ($renderer->getLayouts('address') as $layout) {

												// get layout metadata
												$metadata = $renderer->getLayoutMetaData("address.$layout");

												$layout_type = $metadata->get('type');

												$task = 'assignsubmission';
												if($layout_type == 'display') {
													$task = 'assignelements';
												}

												// create link
												$path = $this->app->path->relative($extension['path']);
												$link = '<a href="'.$this->app->component->active->link(array('controller' => $this->controller, 'task' => $task, 'type' => $type->id, 'path' => urlencode($path), 'layout' => $layout)).'">'.$metadata->get('name', $layout).'</a>';

												// create tooltip
												if ($description = $metadata->get('description')) {
													$link = '<span class="editlinktip hasTip" title="'.$metadata->get('name', $layout).'::'.$description.'">'.$link.'</span>';
												}

												$links[] = $link;
											}

											echo implode(' | ', $links);
											echo '</div>';
										}
									} ?>
								</td>
							</tr>
						</tbody>
					</table>

					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
					<input type="hidden" name="task" value="" />
					<input type="hidden" name="boxchecked" value="0" />
					<?php echo $this->app->html->_('form.token'); ?>

				</form>
			</div>
		</div>
	</div>
</div>