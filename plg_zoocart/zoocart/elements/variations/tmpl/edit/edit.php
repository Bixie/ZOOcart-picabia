<?php
/**
* @package		ZL Elements
* @author    	JOOlanders, SL http://www.zoolanders.com
* @copyright 	Copyright (C) JOOlanders, SL
* @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// get fieldset
$fieldset = $this->getFieldset();

?>

<div id="<?php echo $this->identifier; ?>" class="repeat-elements">
	<ul class="repeatable-list">

		<!-- render instances -->
		<?php $this->rewind(); ?>
		<?php foreach($this as $self) : ?>
		<li class="repeatable-element" data-initial-index="<?php echo $this->index(); ?>">
			<?php echo $fieldset->edit(); ?>
		</li>
		<?php endforeach; ?>

		<!-- render empty instance, 999 index will return no data -->
		<?php $this->seek(999); ?>
		<li class="repeatable-element hidden">
			<?php echo preg_replace(
				array('/(elements\[\w{8}-\w{4}-\w{4}-\w{4}-\w{12}\])(\[-?\d+\])/', '/([\d\w-]+_variation_)[\d]/'),
				array('$1[-1]', '$1[zluxvar-1]'),
				$fieldset->edit());
			?>
		</li>
		<?php $this->rewind(); ?>

	</ul>

	<p class="add">
		<a class="zl-btn" href="javascript:void(0);"><?php echo JText::_('Add variation'); ?></a>
	</p>
</div>

<script type="text/javascript">

	// init Element
	jQuery('#<?php echo $this->identifier; ?>').zx('zoocartVariationsElementEdit', {
		elm_id: '<?php echo $this->identifier; ?>',
		type: '<?php echo $this->getItem()->getType()->id; ?>',
		item_id: <?php echo $this->getItem()->id; ?>,
		variants: <?php echo json_encode($this->getAttrVariants()); ?>
	});

	// init Repeatable Pro feature
	jQuery('#<?php echo $this->identifier; ?>').ElementRepeatablePro({ 
		msgDeleteElement : '<?php echo JText::_('PLG_ZLFRAMEWORK_DELETE_ELEMENT'); ?>', 
		msgSortElement : '<?php echo JText::_('PLG_ZLFRAMEWORK_SORT_ELEMENT'); ?>', 
		instanceLimit: '<?php echo $this->config->get('instancelimit') ?>'
	});
</script>