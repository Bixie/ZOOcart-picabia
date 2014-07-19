<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// set params object
$params = $this->app->data->create($params);

// set country data
if($element->config->get('billing') == 'country') {
	$default = (array)$this->app->zoocart->getConfig()->get('default_country', array());
	$country = $element->get('country', array());
	$country = !empty($country) ? $country : $default;

	// set default selection
	$element->set('country', $country);

	// prepare mapping
	$mapping_data = array(
		'isEU' => $this->app->country->isEU(array_shift($country)) // set if current or default country is EU
	);
}

// set vat data
if($element->config->get('billing') == 'vat') {
	$address = $element->getItem();
	$mapping_data = array(
		'vies' => $this->app->zoocart->tax->isValidVat($address->country, $element->get('value'))
	);
}

// set mapping
$mapping = isset($mapping_data) ? " data-mapping-data='".json_encode($mapping_data)."'" : '';
$mapping .= ' data-mapping="'.$element->config->get('billing').'"';

?>

<div class="uk-form-row" <?php echo $mapping; ?>>
	<?php echo $this->app->zoocart->address->getSubmissionField($element, $params); ?>
</div>