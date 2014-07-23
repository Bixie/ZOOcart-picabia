<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// /*bixie  playground
require_once(JPATH_ADMINISTRATOR.'/components/com_zoo/config.php');
$zoo = App::getInstance('zoo');
$newPrice = false; // "285.00";
if ($newPrice) {
	$db = JFactory::getDbo();
	$db->setQuery($db->getQuery(true)
		->select("*")
		->from("#__zoo_item")
		->where("type = 'cursusdatum' AND state = 1")
		->order("name ASC")
	);
	$existingItems = $db->loadObjectList('alias');
// echo '<pre>';
// print_r($existingItems);
// echo '</pre>';
	// foreach ($existingItems as $datum) {
		// $datum->elements = str_replace('"value": "285"','"value": "'.$newPrice.'"',$datum->elements);
		// $db->updateObject('#__zoo_item',$datum,'id');
	// }
}



//export leuk maar niet nodig
$json = false;//file_get_contents(JPATH_ROOT.'/tmp/cursussen.json');
if (!isset($json)) {
	// export maken
	try {
		// set_time_limit doesn't work in safe mode
		if (!ini_get('safe_mode')) {
			@set_time_limit(0);
		}
		$json = $zoo->export->create('zoo2')->export();
	} catch (AppExporterException $e) {
		echo $e;
	}
}
//csv inleze
$file = JPATH_ROOT.'/tmp/cursus2.csv';
$i=0;
echo '<pre>';
$dataRows = array();
$monthRef = array(
	'jan'=>1,
	'feb'=>2,
	'mar'=>3,
	'apr'=>4,
	'mei'=>5,
	'jun'=>6,
	'jul'=>7,
	'aug'=>8,
	'sep'=>9,
	'okt'=>10,
	'nov'=>11,
	'dec'=>12
);
if ($file && ($handle = fopen($file, "r")) !== FALSE) {
	while ( ($data = fgetcsv($handle, 10000, ';') ) !== FALSE ) { 
		if ($i==0) {
			$i++;
			continue;
		}
		if ($data[0] == '') continue; //lege regel
		$cursusdatum = new Cursusdatum($data[0],$data[1]);
		foreach (array_splice($data,2) as $datum) {
			if (!empty($datum)) {
				$split = explode('-',$datum);
				$date = JFactory::getDate($split[0].'-'.$split[1].'-20'.$split[2]);
// echo $datum.'-'.$date->toSql().'<br/>';
				$cursusdatum->datums[] = $date->toSql(); 
			}
		}
		$dataRows[] = $cursusdatum;
		$i++;
	}
	fclose($handle);
}
// print_r($dataRows);
echo '</pre>';
//data verwerken
$newData = new stdClass;
$newData->items = new stdClass;
$cursusConfig = json_decode(file_get_contents(JPATH_ROOT.'/media/zoo/applications/blog/types/cursus.config'));
$cursusdatumConfig = json_decode(file_get_contents(JPATH_ROOT.'/media/zoo/applications/blog/types/cursusdatum.config'));

$import = false;
// $import = 'datums';
$import = 'locaties';
if ($import) {
	echo '<pre>';
	$db = JFactory::getDbo();
	$db->setQuery($db->getQuery(true)
		->select("*")
		->from("#__zoo_item")
		->where("type = 'cursus' AND state = 1")
		->order("name ASC")
	);
	$existingItems = $db->loadObjectList('alias');
	// print_r($existingItems);

	foreach ($dataRows as $cursusdatum) {
		if ($import == 'datums') {
			
			$locatieZooId = isset($existingItems[$cursusdatum->alias])?$existingItems[$cursusdatum->alias]->id:0;
	
			//data
			$locatieData = array();
			$locData = new stdClass;
			$locData->option = (object) array($cursusdatum->alias);
			$locData->select = (string) 1;
			$locatieData['Locatie'] = $locData;
			$relatedData = new stdClass;
			if ($locatieZooId) $relatedData->item = (object) array($locatieZooId);
			$locatieData['Cursuslocatie'] = $relatedData;
			$priceData = new stdClass;
			$priceData->value = (string) "0.00";
			$priceData->tax_class = (string) 1;
			$locatieData['Prijs'] = (object) array($priceData);
			$cartData = new stdClass;
			$cartData->value = (string) 1;
			$locatieData['Reserveren'] = (object) array($cartData);
			$formatDates = array();
			$dateValues = array();
			$firstDate = false;
			foreach ($cursusdatum->datums as $datum) {
				$date = JFactory::getDate($datum);
				if (!$firstDate) $firstDate = $date->toSql();
				$formatDates[] = $date->format('d M');
				$datumData = new stdClass;
				$datumData->value =  $date->format('Y-m-d 00:00:00');
				$dateValues[] = $datumData;
			}
			$locatieData['Datum'] = (object) $dateValues;
			$zooItem = new zooItem($cursusdatum->plaats . ' - ' .implode(', ', $formatDates) ,'Cursusdatum',$firstDate);
			//elementen bouwen
			foreach ($cursusdatumConfig->elements as $identifier=>$elementData) {
				if ($elementData->name == 'Access') break;
				$element = new stdClass;
				$element->type = $elementData->type;
				$element->name = $elementData->name;
				if (isset($locatieData[$elementData->name]))
					$element->data = $locatieData[$elementData->name];
				else 
					$element->data = new stdClass;
				$zooItem->setElement($identifier, $element);
				// $zooItem->elements->$identifier = $element;
			}
			$zooItem->toObject();
			$alias = $zoo->string->sluggify($zooItem->name);

			$newData->items->{$alias} = $zooItem;
	// print_r($cursusdatumConfig->elements);

		} else {
			$zooItem = new zooItem($cursusdatum->plaats,'Cursuslocatie');
			//data
			$locatieData = array();
			$locatieData['Locatie'] = (object) array();
			$provincieData = new stdClass;
			$provincieData->option = (object) array($cursusdatum->provincie);
			$provincieData->select = (string) 1;
			$locatieData['Provincie'] = $provincieData;
			$mapsData = new stdClass;
			$mapsData->location = (string) $cursusdatum->plaats.', The Netherlands';
			$locatieData['Maps-locatie'] = $mapsData;
			$moduleData = new stdClass;
			$moduleData->value = (string) 114;
			$locatieData['Cursusbeschrijving'] = $moduleData;
			//elementen bouwen
			foreach ($cursusConfig->elements as $identifier=>$elementData) {
				if ($elementData->name == 'Access') break;
				$element = new stdClass;
				$element->type = $elementData->type;
				$element->name = $elementData->name;
				if (isset($locatieData[$elementData->name]))
					$element->data = $locatieData[$elementData->name];
				else 
					$element->data = new stdClass;
				$zooItem->setElement($identifier, $element);
				// $zooItem->elements->$identifier = $element;
			}
			$zooItem->toObject();
			$newData->items->{$cursusdatum->alias} = $zooItem;
		}
			
	}
	print_r($newData);
	file_put_contents(JPATH_ROOT.'/tmp/out'.$import.'.json',json_encode($newData));
	echo '</pre>';
}

class Cursusdatum {
	public $alias;
	public $plaats;
	public $provincie;
	public $datums = array();
	public function __construct ($plaats,$provincie) {
		$this->plaats = $plaats;
		$this->provincie = strtolower($provincie);
		$this->alias = str_replace(' ','-',strtolower($plaats));
	}
}

class zooItem {
	public $searchable;
	public $state;
	public $created;
	public $modified;
	public $hits;
	public $access;
	public $priority;
	public $publish_up;
	public $publish_down;
	public $author;
	public $tags;
	public $config;
	public $categories = array();
	public $elements;
	public $name;
	public $group;
	public function __construct ($name,$group,$publish_down='0000-00-00 00:00:00') {
		$this->name = $name;
		$this->group = $group;
		$this->searchable = (string)1;
		$this->state = (string)1;
		$this->access = (string)1;
		$this->hits = (string)0;
		$this->priority = (string)0;
		$this->author = 'import';
		$this->created = JFactory::getDate()->toSql();
		$this->modified = '0000-00-00 00:00:00';
		$this->publish_down = $publish_down;
		$this->publish_up = JFactory::getDate()->toSql();
		$this->tags = new stdClass;
		if ($group == 'Cursuslocatie') {
			$this->categories[] = 'cursuslocaties';
			$this->config->primary_category = 'cursuslocaties';
		}
		if ($group == 'Cursusdatum') {
			$this->categories[] = 'cursusdata';
			$this->config->primary_category = 'cursusdata';
		}
	}
	public function setElement($identifier,$data) {
		$this->elements->$identifier = $data;
	}
	public function toObject() {
		$this->categories = (object)$this->categories;
		return $this;
	}
}



//*/
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_banners.category');
$archived	= $this->state->get('filter.state') == 2 ? true : false;
$trashed	= $this->state->get('filter.state') == -2 ? true : false;
$params		= (isset($this->state->params)) ? $this->state->params : new JObject;
$saveOrder	= $listOrder == 'a.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_banners&task=banners.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_banners&view=banners'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php
		// Search tools bar
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped" id="articleList">
				<thead>
					<tr>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
						<th width="1%" class="hidden-phone">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
						<th width="1%" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('searchtools.sort', 'COM_BANNERS_HEADING_NAME', 'a.name', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_BANNERS_HEADING_STICKY', 'a.sticky', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_BANNERS_HEADING_CLIENT', 'client_name', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_BANNERS_HEADING_IMPRESSIONS', 'impmade', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'COM_BANNERS_HEADING_CLICKS', 'clicks', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="13">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php foreach ($this->items as $i => $item) :
						$ordering  = ($listOrder == 'ordering');
						$item->cat_link = JRoute::_('index.php?option=com_categories&extension=com_banners&task=edit&type=other&cid[]=' . $item->catid);
						$canCreate  = $user->authorise('core.create',     'com_banners.category.' . $item->catid);
						$canEdit    = $user->authorise('core.edit',       'com_banners.category.' . $item->catid);
						$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
						$canChange  = $user->authorise('core.edit.state', 'com_banners.category.' . $item->catid) && $canCheckin;
						?>
						<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">
							<td class="order nowrap center hidden-phone">
								<?php
								$iconClass = '';
								if (!$canChange)
								{
									$iconClass = ' inactive';
								}
								elseif (!$saveOrder)
								{
									$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
								}
								?>
								<span class="sortable-handler <?php echo $iconClass ?>">
									<i class="icon-menu"></i>
								</span>
								<?php if ($canChange && $saveOrder) : ?>
									<input type="text" style="display:none" name="order[]" size="5"
										value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
								<?php endif; ?>
							</td>
							<td class="center hidden-phone">
								<?php echo JHtml::_('grid.id', $i, $item->id); ?>
							</td>
							<td class="center">
								<div class="btn-group">
									<?php echo JHtml::_('jgrid.published', $item->state, $i, 'banners.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
									<?php
									// Create dropdown items
									$action = $archived ? 'unarchive' : 'archive';
									JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'banners');

									$action = $trashed ? 'untrash' : 'trash';
									JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'banners');

									// Render dropdown list
									echo JHtml::_('actionsdropdown.render', $this->escape($item->name));
									?>
								</div>
							</td>
							<td class="nowrap has-context">
								<div class="pull-left">
									<?php if ($item->checked_out) : ?>
										<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'banners.', $canCheckin); ?>
									<?php endif; ?>
									<?php if ($canEdit) : ?>
										<a href="<?php echo JRoute::_('index.php?option=com_banners&task=banner.edit&id=' . (int) $item->id); ?>">
											<?php echo $this->escape($item->name); ?></a>
									<?php else : ?>
										<?php echo $this->escape($item->name); ?>
									<?php endif; ?>
									<span class="small">
										<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
									</span>
									<div class="small">
										<?php echo $this->escape($item->category_title); ?>
									</div>
								</div>
							</td>
							<td class="center hidden-phone">
								<?php echo JHtml::_('banner.pinned', $item->sticky, $i, $canChange); ?>
							</td>
							<td class="small hidden-phone">
								<?php echo $item->client_name; ?>
							</td>
							<td class="small hidden-phone">
								<?php echo JText::sprintf('COM_BANNERS_IMPRESSIONS', $item->impmade, $item->imptotal ? $item->imptotal : JText::_('COM_BANNERS_UNLIMITED')); ?>
							</td>
							<td class="center small hidden-phone">
								<?php echo $item->clicks; ?> -
								<?php echo sprintf('%.2f%%', $item->impmade ? 100 * $item->clicks / $item->impmade : 0); ?>
							</td>

							<td class="small nowrap hidden-phone">
								<?php if ($item->language == '*'): ?>
									<?php echo JText::alt('JALL', 'language'); ?>
								<?php else: ?>
									<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
								<?php endif; ?>
							</td>
							<td class="center hidden-phone">
								<?php echo $item->id; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
		<?php //Load the batch processing form. ?>
		<?php echo $this->loadTemplate('batch'); ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
