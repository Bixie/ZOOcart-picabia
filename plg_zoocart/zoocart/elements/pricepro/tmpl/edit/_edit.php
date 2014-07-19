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

<div class="repeatable-content">

	<div class="row">
		<?php echo $this->app->html->_('control.text', $this->getControlName('value'), $this->get('value'), 'size="30" maxlength="255" class="input-price" style="display: none;"'); ?>
		<input type="text" size="30" maxlength="255" class="input-price-pretty" />
    </div>

	<div class="more-options">

		<div class="trigger">
			<div>
				<div class="tax button"><?php echo JText::_('PLG_ZOOCART_CONFIG_TAXCLASSES'); ?></div>
			</div>
		</div>

		<div class="tax options">

			<div class="row">
				<?php echo $this->app->zoocart->html->_('zoo.taxClassesList', $this->getControlName('tax_class'), $this->get('tax_class', $this->config->find('specific._default_tax_class'))); ?>
			</div>

		</div>

	</div>

</div>