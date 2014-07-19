<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// get all availablle plugins
$plugins = $this->app->zoocart->payment->getPaymentPlugins();

?>

<div class="uk-form">

	<!-- init uikit button radio -->
	<div data-uk-button-radio>

	<!-- if 1 plugin -->
	<?php if (count($plugins) == 1) : ?>

		<?php $plugin = array_shift($plugins); $params = $this->app->data->create($plugin->params); ?>
		<input type="hidden" name="payment_method" value="<?php echo $plugin->name; ?>" /> 
		<?php echo JText::_( $params->get('title', ucfirst($plugin->name)) ); ?>

	<!-- if multiple -->
	<?php else : foreach($plugins as $plugin) : ?>

		<?php
			// get params
			$params = $this->app->data->create($plugin->params);

			// set active state
			$active = isset($selected) && $selected == $plugin->name ? true : false;
			$active = $active ? ' uk-active' : '';
		?>
		<button type="button" class="uk-button uk-button-mini<?php echo $active; ?>" name="payment_method" value="<?php echo $plugin->name; ?>" required /> 
		<?php 
			$title = strlen($params->get('title')) ? $params->get('title') : ucfirst($plugin->name);
			echo JText::_($title);
		?>
		</button>

	<?php endforeach; endif; ?>
	</div>
</div>