<?php
/**
* @package   com_zoo
* @author    ZOOlanders http://www.zoolanders.com
* @copyright Copyright (C) ZOOlanders.com
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<div id="<?php echo $this->identifier; ?>">
	<div class="row">
		<?php echo $this->app->html->_('control.text', $this->getControlName('value'), $value, 'size="30" maxlength="255" class="input-quantity"'); ?>
    </div>
</div>