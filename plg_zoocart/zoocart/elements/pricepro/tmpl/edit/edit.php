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

<?php if ($this->config->get('repeatable')) : ?>

	<div id="<?php echo $this->identifier; ?>" class="repeat-elements">
		<ul class="repeatable-list">

			<?php $this->rewind(); ?>
			<?php foreach($this as $self) : ?>
				<li class="repeatable-element">
					<?php echo $this->$function($params); ?>
				</li>
			<?php endforeach; ?>

			<?php $this->rewind(); ?>

			<li class="repeatable-element hidden">
				<?php echo preg_replace('/(elements\[\S+])\[(\d+)\]/', '$1[-1]', $this->$function($params)); ?>
			</li>

		</ul>
		<p class="add">
			<a href="javascript:void(0);"><?php echo JText::_('Add another'); ?></a>
		</p>

	</div>
	
	<script type="text/javascript">
		jQuery('#<?php echo $this->identifier; ?>').ElementRepeatable({ msgDeleteElement : '<?php echo JText::_('PLG_ZLFRAMEWORK_DELETE_ELEMENT'); ?>', msgSortElement : '<?php echo JText::_('PLG_ZLFRAMEWORK_SORT_ELEMENT'); ?>' });
		jQuery('#<?php echo $this->identifier; ?>').zx('EditElementPrice', {currency: <?php echo json_encode($this->getCurrency()); ?>});
	</script>
	
<?php else : ?>

	<div id="<?php echo $this->identifier; ?>" class="repeatable-element">
		<?php echo $this->$function($params); ?>
	</div>

	<script type="text/javascript">
		jQuery('#<?php echo $this->identifier; ?>').zx('EditElementPrice', {currency: <?php echo json_encode($this->getCurrency()); ?>});
	</script>

<?php endif; ?>