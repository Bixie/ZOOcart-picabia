<?php
/**
* @package		ZL Elements
* @author    	JOOlanders, SL http://www.zoolanders.com
* @copyright 	Copyright (C) JOOlanders, SL
* @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// init vars
$index = $this->getElement()->index();
$attributes = $this->config->find('specific.attributes', array());
$checked = $this->get('default') ? ' checked="checked"' : null;
$id = $this->_element->identifier.$this->_element->index();
$quantity_els = $this->getItem()->getElementsByType('quantity');

?>

<div class="zlux-fieldset zl-bootstrap">

	<!-- Set as default field -->
	<div class="row zlux-x-checkbox">
		<input id="<?php echo $id; ?>" type="radio" name="<?php echo $this->getControlName('default'); ?>" value="1"<?php echo $checked; ?> />
		<label for="<?php echo $id; ?>"><?php echo JText::_('PLG_ZOOCART_VARIATIONS_APPLY_BY_DEFAULT'); ?></label>
	</div>

	<!-- Tab toggles -->
	<ul class="nav nav-tabs">
		<li class="active"><a href="#variations-<?php echo $index; ?>-attributes" data-toggle="tab">
			<?php echo JText::_('PLG_ZLFRAMEWORK_ATTRIBUTES'); ?>
		</a></li>
		<li><a href="#variations-<?php echo $index; ?>-price" data-toggle="tab"><?php echo JText::_('PLG_ZLFRAMEWORK_PRICE'); ?></a></li>
		<?php if (count($quantity_els)) : ?>
		<li><a href="#variations-<?php echo $index; ?>-quantity" data-toggle="tab"><?php echo JText::_('PLG_ZLFRAMEWORK_QUANTITY'); ?></a></li>
		<?php endif; ?>
		<li><a href="#variations-<?php echo $index; ?>-variations" data-toggle="tab"><?php echo JText::_('PLG_ZLFRAMEWORK_ELEMENTS'); ?></a></li>
	</ul>

	<!-- Tab content -->
	<div class="tab-content">

		<!-- Attributes -->
		<div class="tab-pane active" id="variations-<?php echo $index; ?>-attributes">
			<?php if (count($attributes)) foreach ($attributes as $attr) {
				$options = array();
				foreach ($attr['options'] as $option) {
					$options[] = $this->app->html->_('select.option', $option['value'], $option['name']);
				}

				echo '<div class="row" data-attr="'.$attr['value'].'">'.$this->app->html->_('select.genericlist', $options, $this->getControlName('attributes').'['.$attr['value'].']', '', 'value', 'text', $this->get('attributes', array())).'</div>';
			} ?>
		</div>

		<!-- Price -->
		<div class="tab-pane" id="variations-<?php echo $index; ?>-price">
			<?php foreach($this->getItem()->getElementsByType('pricepro') as $element) {
				$el = json_decode($this->getElement()->getElementEdit($element->identifier), true);
				echo $el['html'];
				echo !empty($el['script']) ? '<script type="text/javascript">'.$el['script'].'</script>' : '';
			} ?>
		</div>

		<!-- Quantity -->
		<?php if (count($quantity_els)) : ?>
		<div class="tab-pane" id="variations-<?php echo $index; ?>-quantity">
			<?php foreach($quantity_els as $element) {
				$el = json_decode($this->getElement()->getElementEdit($element->identifier), true);
				echo $el['html'];
				echo !empty($el['script']) ? '<script type="text/javascript">'.$el['script'].'</script>' : '';
			} ?>
		</div>
		<?php endif; ?>

		<!-- Variations -->
		<div class="tab-pane" id="variations-<?php echo $index; ?>-variations">
			<!-- foreach element -->
			<?php foreach ($this->getElement()->config->find('specific.elements', array()) as $identifier) : ?>
			<?php $element = $this->getItem()->getElement($identifier); ?>
			<div data-variations='<?php echo json_encode(array('identifier' => $identifier, 'name' => $element->config->name)); ?>'>
				
				<!-- set current values -->
				<div style="display: none;">
					<?php echo implode('', $this->getElement()->valuesToInputs($this->get($identifier), $this->getControlName($identifier))); ?>
				</div>

				<!-- set modal buttons -->
				<p><button class="btn btn-mini" type="button">
					<i class="icon-edit"></i><?php echo $element->config->name; ?>
				</button></p>

			</div>
			<?php endforeach; ?>
		</div>
	</div>

</div>