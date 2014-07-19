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

<?php if ($align == "above") : ?>
	<?php echo $this->renderPosition('media', array('style' => 'uikit_block')); ?>
<?php endif; ?>

<?php if ($this->checkPosition('title')) : ?>
<h1 class="uk-article-title">
	<?php echo $this->renderPosition('title'); ?>
</h1>
<?php endif; ?>

<?php if ($this->checkPosition('subtitle')) : ?>
<p class="uk-article-lead">
	<?php echo $this->renderPosition('subtitle'); ?>
</p>
<?php endif; ?>

<?php if ($align == "top") : ?>
	<?php echo $this->renderPosition('media', array('style' => 'uikit_block')); ?>
<?php endif; ?>

<?php if ($align == "left" || $align == "right") : ?>
<div class="uk-align-medium-<?php echo $align; ?>">
	<?php echo $this->renderPosition('media'); ?>
</div>
<?php endif; ?>
<?php if ($this->checkPosition('content') || $this->checkPosition('links')) : ?>
<div id="datalist<?php echo $this->item->id; ?>" class="uk-grid">

	<div class="uk-width-1-1">
		<!-- This is the container enabling the JavaScript -->
		<div class="uk-button-dropdown" data-uk-dropdown="{justify:'#datalist<?php echo $this->item->id; ?>'}">

			<!-- This is the element toggling the dropdown -->
			<button class="uk-button uk-button-primary">Data voor deze locatie</button>

			<!-- This is the dropdown -->
			<div class="uk-dropdown">
		<?php echo $this->renderPosition('content'); ?>
				
			</div>
		</div>
	</div>
	<div class="uk-width-1-1">
		
		<?php if ($this->checkPosition('links')) : ?>
		<div class="">
			<?php echo $this->renderPosition('links', array('style' => 'uikit_blank')); ?>
		</div>
		<?php endif;?>
	</div>
</div>
<?php endif; ?>

<?php if ($this->checkPosition('meta')) : ?>
<p class="uk-article-meta">
    <?php echo $this->renderPosition('meta'); ?>
</p>
<?php endif; ?>

<?php if ($align == "bottom") : ?>
	<?php echo $this->renderPosition('media', array('style' => 'uikit_block')); ?>
<?php endif; ?>

