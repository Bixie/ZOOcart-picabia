<?php
/**
 * @package		ZOOcart
 * @author		ZOOlanders http://www.zoolanders.com
 * @copyright	Copyright (C) JOOlanders, SL
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<!-- render item -->
<?php if($item = $orderitem->getItem()) : ?>
	<?php echo $this->renderer->render('item.order', array('view' => $this, 'item' => $item, 'orderitem' => $orderitem)); ?>

<!-- if no item, render raw data -->
<?php else : ?>
<tr data-orderitem-id="<?php echo $orderitem->id; ?>">

	<!-- Name -->
	<td class="name">
		<?php echo $orderitem->name; ?>

		<!-- Variations -->
		<?php if (isset($orderitem->variations) && strlen($orderitem->variations)) {
			$variations = json_decode($orderitem->variations, true);

			$var_resume = array();
			foreach ($variations as $name => $val) {
				$var_resume[] = ucfirst($name) . ' > ' . ucfirst($val);
			}

			echo '<div><small>'.implode(', ', $var_resume).'</small></div>';
		} ?>
	</td>

	<!-- Quantity -->
	<td class="quantity">
		<?php echo $orderitem->quantity; ?>
	</td>

	<!-- Unit price -->
	<td class="price uk-text-right">
		<?php 
		if ($this->app->zoocart->getConfig()->get('show_price_with_tax', 1)) {
			// total includes taxes and dividing we can get the unit_price with taxes
			$price = $orderitem->total / $orderitem->quantity;
		} else {
			$price = $orderitem->price;
		}
		
		echo $this->app->zoocart->currency->format($price);
		?>
	</td>

	<!-- Total -->
	<td class="price-total uk-text-right">
		<?php
		if ($this->app->zoocart->getConfig()->get('show_price_with_tax', 1)) {
			$price = $orderitem->total;
		} else {
			$price = $orderitem->total - $orderitem->tax;
		}

		echo $this->app->zoocart->currency->format($price);
		?>
	</td>
</tr>
<?php endif; ?>