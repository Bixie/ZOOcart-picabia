<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * HTML helper class.
 */
class zoocartHTMLHelper extends AppHelper {

	/**
	 * Wrapper function
	 *
	 * @param string $type The function to call
	 *
	 * @return string The html output
	 * @since 2.0
	 */
	public function _($type) {

		// get arguments
		$args = func_get_args();

		// Check to see if we need to load a helper file
		$parts = explode('.', $type);

		if (count($parts) >= 2) {
			$func = array_pop($parts);
			$file = array_pop($parts);

			if (in_array($file, array('zoo', 'control')) && method_exists($this, $func)) {
				array_shift($args);
				return call_user_func_array(array($this, $func), $args);
			}
		}

		return call_user_func_array(array('JHTML', '_'), $args);
	}

	/**
	 * Returns Tax Classes select list html string.
	 *
	 * @param string $name The html name
	 * @param string $value The value
	 */
	public function taxClassesList($name, $value)
	{
		// init vars
		$options = array();
		$attribs = '';

		// set none Tax option
		// $options[] = $this->app->html->_('select.option', 0, ' - '.JText::_('PLG_ZLFRAMEWORK_NONE').' - ');

		// taxes classes are general, get them all always
		foreach ($this->app->zoocart->table->taxclasses->all() as $taxclass) {
			$options[] = $this->app->html->_('select.option', $taxclass->id, JText::_($taxclass->name));
		}

		return $this->app->html->_('select.genericlist', $options, $name, $attribs, 'value', 'text', $value);
	}

}