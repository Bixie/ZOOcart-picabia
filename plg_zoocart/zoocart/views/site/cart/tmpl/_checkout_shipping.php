<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Init vars
$multiple = count($shipping_rates) > 1; // multiple shipping plugins

// Check shipping autochoice option:
$auto = $this->app->zoocart->getConfig()->get('shipping_autochoice', false);

?>

<div class="zx-zoocart-checkout-shipping uk-form uk-text-center"<?php if($auto)echo ' data-autochoose '?>>
	<?php if(!empty($shipping_rates)):?>
		<?php foreach($shipping_rates as $plugin => $rates) : ?>

			<?php // if multiple plugin let's display a name to differentiate the rates
			if($multiple) {
				$plg = $this->app->zoocart->shipping->getPluginByName($plugin);
				$params = $this->app->data->create($plg->params);
				$plugin_name = strlen($params->get('title')) ? $params->get('title') : ucfirst($plg->name);
			}
			?>

			<?php if(!$multiple && (count($rates) == 1) && $auto) : ?>
				<?php $rate = array_shift($rates); ?>
				<input type="hidden" name="shipping_method" value="<?php echo $rate['id']; ?>" data-shipping-plugin="<?php echo $plugin; ?>" />
				<?php echo JText::_(ucfirst($rate['name'])) .' - '. $this->app->zoocart->currency->format($rate['price']); ?>
			<?php else : ?>

				<span data-uk-button-radio>
					<?php foreach($rates as $rate) : ?>
					<button type="button" class="uk-button uk-button-mini" name="shipping_method" value="<?php echo $rate['id']; ?>" data-shipping-plugin="<?php echo $plugin; ?>" required>
						<?php echo JText::_(ucfirst($rate['name'])) . ($multiple ?' ('.$plugin_name.')' : '') .' - '. $this->app->zoocart->currency->format($rate['price']); ?>
					</button>
					<?php endforeach; ?>
				</span>
			<?php endif; ?>

		<?php endforeach; ?>
	<?php endif; ?>
</div>