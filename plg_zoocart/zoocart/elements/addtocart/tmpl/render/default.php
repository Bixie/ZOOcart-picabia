<?php
/**
* @package   ZOOcart
* @author    ZOOlanders http://www.zoolanders.com
* @copyright Copyright (C) ZOOlanders.com
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// load assets
$this->app->zlfw->zlux->loadMainAssets(true);
$this->app->document->addStylesheet('zoocart:assets/css/site.css');
$this->app->document->addScript('zlfw:vendor/zlux/js/addons/notify.js');
$this->app->document->addScript('zoocart:elements/addtocart/tmpl/render/default/script.js');

// init vars
$check_quantities = $this->app->zoocart->getConfig()->get('check_quantities', true);
$avoid_readd = $params->find('layout._avoid_readd', true);
$variations = @empty($this->getItem()->variations) ? null : $this->getItem()->variations;

// set cart related vars
$cartitem = $this->app->zoocart->table->cartitems->getByItem($this->getItem());
$in_cart = (bool)$cartitem;
$in_cart_quant = $in_cart ? $cartitem->quantity : 0;

// get stock state
if ($check_quantities) {
	$in_stock = $this->app->zoocart->quantity->checkQuantity($this->getItem()) > 0;
} else $in_stock = true;

// action after the item has been added to cart
switch($params->find('layout._action', 'cart')) {
	case 'reload':
		$action = '_reload_';
		break;
	case 'none':
		$action = '';
		break;
	case 'cart':
	default:
		$action = $this->app->zl->route->zoocart->cart();
		break;
}

// add to cart label
$addtocart_label = $params->find('layout._label');
$addtocart_label = $addtocart_label ? $addtocart_label : 'PLG_ZOOCART_ADDTOCART_ADD_TO_CART';
$addtocart_label = JText::_($addtocart_label);

// complete label
$complete_label = $params->find('layout._complete_label');
$complete_label = $complete_label ? $complete_label : 'PLG_ZOOCART_ADDTOCART_COMPLETE_LABEL';
$complete_label = JText::_($complete_label);

// stock limit reached label
$stock_exhausted_label = JText::_('PLG_ZOOCART_STOCK_LIMIT_REACHED');

// if product in cart and stock exhausted
if ($in_cart && !$in_stock) {
	$complete_label = $stock_exhausted_label;
}

// set the button state
$state = ($avoid_readd && $in_cart) || !$in_stock ? false : true;

// get the elements hash
$hash = md5(serialize(array(
	$params->get('element').$params->get('_position').$params->get('_index')
)));

// call once the script
if (!defined('ZOOCART_ELEMENT_SCRIPT_DECLARATION_'.$hash)) {
	define('ZOOCART_ELEMENT_SCRIPT_DECLARATION_'.$hash, true);

	// init the js functions
	$javascript = "jQuery(function($) { 
		$('body').zx('zoocartAddToCart', {
			hash: '{$hash}',
			redirectUrl: '{$action}',
			avoid_readd: {$avoid_readd},
			def_variations: ".($variations != null ? $variations : 'null').",
			token: '".$this->app->session->getFormToken()."'
		});

		// add js language strings
		$.zx.lang.push({
			\"ZC_COMPLETE_LABEL\":\"{$complete_label}\",
			\"ZC_STOCK_EXHAUSTED_LABEL\":\"{$stock_exhausted_label}\"
		});
	});";
	
	$this->app->document->addScriptDeclaration($javascript);
}

?>

<div class="zoocart-addtocart zx" data-hash="<?php echo $hash; ?>" data-item-id="<?php echo $this->getItem()->id; ?>">
	<?php if($in_cart || $in_stock) : ?>

	<button type="button" class="uk-button uk-button-small uk-button-primary<?php echo !$state ? ' zx-x-disabled' : ''; ?>">
		<span class="zx-x-incart-quant"><?php echo $in_cart_quant ? $in_cart_quant.'x' : ''; ?></span>
		<i class="uk-icon-shopping-cart"></i>&nbsp;&nbsp;
		<span class="zx-x-text"><?php echo !$state ? $complete_label : $addtocart_label; ?></span>
	</button>

	<?php else : ?>
	<?php echo JText::_('PLG_ZOOCART_STOCK_PRODUCT_OUT_OF_STOCK'); ?>
	<?php endif; ?>
</div>