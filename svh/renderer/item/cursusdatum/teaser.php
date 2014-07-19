<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// init vars
$params = $item->getParams('site');

/* set media alignment */
$align = ($this->checkPosition('media')) ? $params->get('template.teaseritem_media_alignment') : '';

?>


<div class="uk-grid">
	<div class="uk-width-medium-1-2">
	<?php echo $this->renderPosition('content', array('style' => 'uikit_blank')); ?>
	</div>

	<div class="uk-width-medium-1-2">
	<?php echo $this->renderPosition('links', array('style' => 'uikit_blank')); ?>
	</div>
</div>
