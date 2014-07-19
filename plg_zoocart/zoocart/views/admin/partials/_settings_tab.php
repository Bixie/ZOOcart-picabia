<?php
/**
 * @package		ZOOcart
 * @author		ZOOlanders http://www.zoolanders.com
 * @copyright	Copyright (C) JOOlanders, SL
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if(empty($current)){
	$current = $this->controller;
}

// tabs config
$tabs = array(
	'zoocart' => array(
		'icon' => 'cog',
	    'text' => 'PLG_ZOOCART_TAB_GENERAL'
	),
	'currencies' => array(
		'icon' => 'dollar',
		'text' => 'PLG_ZOOCART_TAB_CURRENCIES'
	),
	'shippingrates' => array(
		'icon' => 'truck',
		'text' => 'PLG_ZOOCART_TAB_SHIPPING'
	),
	'discounts' => array(
		'icon' => 'ticket',
		'text' => 'PLG_ZOOCART_TAB_DISCOUNTS'
	),
	'orders' => array(
		'icon' => 'gift',
		'text' => 'PLG_ZOOCART_TAB_ORDERS'
	),
	'quantities' => array(
		'icon' => 'cubes',
		'text' => 'PLG_ZOOCART_TAB_QUANTITIES'
	),
	'addresses' => array(
		'icon' => 'map-marker',
		'text' => 'PLG_ZOOCART_TAB_ADDRESSES'
	),
	'taxes' => array(
		'icon' => 'legal',
		'text' => 'PLG_ZOOCART_TAB_TAXES'
	),
	'emails' => array(
		'icon' => 'envelope',
		'text' => 'PLG_ZOOCART_TAB_EMAILS'
	),
);

if(!array_key_exists($current, $tabs)){
	$current = 'zoocart';
}

$target = ($this->controller == 'zoocart')?' data-uk-tab="{connect:\'#settings-tab-content\'}"':'';

?>
<ul class="uk-tab uk-tab-left"<?php echo $target; ?>>
	<?php foreach($tabs as $key=>$tab):?>
	<li<?php if($key==$current)echo ' class="uk-active"'?>>
		<a href="<?php echo $this->app->zl->link(array('controller' => 'zoocart', 'open' => $key), false); ?>">
			<span class="uk-icon-<?php echo $tab['icon']; ?>"></span> <span class="uk-hidden-medium"><?php echo JText::_($tab['text']); ?></span>
		</a>
	</li>
	<?php endforeach; ?>
</ul>

 