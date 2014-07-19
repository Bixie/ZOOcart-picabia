<?php
/*
* @package		ZL Elements
* @author    	ZOOlanders http://www.zoolanders.com
* @copyright 	Copyright (C) JOOlanders, SL
* @license   	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// init vars
$is_cart = $this->app->zlfw->enviroment->is('site.com_zoolanders.cart');
$is_order = $this->app->zlfw->enviroment->is('site.com_zoolanders.order');
$attributes = $this->getFilteredAttributes();
$sel_attributes = $var_resume = array();

// if in cart or order view
if($is_cart || $is_order) {
	// get item
	$item = $this->getItem();

	// retrieve it's variations
	if (isset($item->variations) && strlen($item->variations)) {
		$sel_attributes = strlen($item->variations) ? json_decode($item->variations, true) : $sel_attributes;
	}

} else {
	// load assets
	$this->app->zlfw->zlux->loadMainAssets(true);
	$this->app->document->addScript('elements:variations/tmpl/render/default/script.js');

	// prepare init data
	$data = array(
		'elm_id' => $this->identifier,
		'type' => $this->getItem()->getType()->id,
		'item_id' => $this->getItem()->id,
		'layout' => $params->get('_layout')
	);

	$sel_attributes = $this->getDefaultVariation();
	$sel_attributes = $sel_attributes ? $sel_attributes : array();
}

?>

<div class="zx-zoocart-variations zx"<?php if(!$is_cart && !$is_order) : ?> data-zx-zoocart-variations='<?php echo json_encode($data); ?>'<?php endif; ?>>
	<?php foreach ($attributes as $attr) : ?>
	<?php 
		// get selected from value or use default
		$selected = array_key_exists($attr['value'], $sel_attributes) ? $sel_attributes[$attr['value']] : $attr['options'][0]['value'];

		// get selected value/name by filtering out all other values
		$selected = array_filter($attr['options'], create_function('$value', 'return $value["value"] == "'.$selected.'";'));
		$selected = array_shift($selected);
	?>

	<!-- dropdown -->
	<?php if (!$is_order) : ?>
	<div class="uk-button-dropdown<?php echo (!$is_cart ? ' uk-display-block uk-margin-small-bottom' : ''); ?>" data-uk-dropdown="{mode:'click'}" data-attr="<?php echo $attr['value']; ?>" data-selected="<?php echo $selected['value']; ?>">

		<!-- dropdown toggler -->
		<button type="button" class="uk-button <?php echo ($is_cart ? 'uk-button-mini' : 'uk-button-small'); ?>">
			<?php echo JText::_($attr['name']); ?> > <span class="zx-x-attr"><?php echo JText::_($selected['name']); ?></span>
			<i class="uk-icon-caret-down"></i>
		</button>

		<!-- dropdown options -->
		<?php if (count($attr['options']) > 1) : ?>
		<div class="uk-dropdown uk-dropdown-small">
			<ul class="uk-nav uk-nav-dropdown">
				<?php foreach ($attr['options'] as $option) : ?>
				<li<?php echo $selected['value'] == $option['value'] ? ' class="uk-hidden"' : ''; ?>>
					<a href="#" data-value="<?php echo $option['value']; ?>">
						<?php echo JText::_($option['name']); ?>
					</a>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>

		<!-- form value -->
		<?php if($is_cart) : $cartitem = $this->app->zoocart->table->cartitems->get($item->cartitem_id); ?>
		<input type="hidden" name="items[<?php echo $cartitem->id; ?>][variations][<?php echo $attr['value']; ?>]" value="<?php echo $selected['value']; ?>">
		<?php endif; ?>

	</div>
	<?php endif; ?>

	<!-- if order -->
	<?php if ($is_order) $var_resume[] = JText::_($attr['name']) . ' > ' . JText::_($selected['name']); ?>

	<?php endforeach; ?>

	<!-- if order -->
	<?php if ($is_order) : ?>
		<small><?php echo implode(', ', $var_resume); ?></small>
	<?php endif; ?>
</div>