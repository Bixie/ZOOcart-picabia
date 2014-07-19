<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
<div class="uk-grid">
	<h3 class="uk-width-2-10 date">%d</h3>
	<div class="uk-width-3-10">
	<strong class="month">%B</strong><br/>
	<span class="time"><i class="uk-icon-clock-o"></i>&nbsp;&nbsp;%H:%M</span>
	</div>
	<div class="uk-width-5-10 full">%a %d %B '%y<br/>%H:%M</div>
</div>
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<li class="uk-width-1-1">
	<div class="uk-grid">
		<div class="uk-width-medium-3-5">
		<?php echo $this->renderPosition('title', array('style' => 'uikit_blank')); ?>
		</div>

		<div class="uk-width-medium-2-5">
		<?php echo $this->renderPosition('links', array('style' => 'uikit_blank')); ?>
		</div>
	</div>
</li>

