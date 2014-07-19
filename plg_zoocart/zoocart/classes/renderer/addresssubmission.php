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
 * Render submissions for an Address
 */
class AddressSubmissionRenderer extends PositionRenderer {

	/**
	 * The address to render
	 *
	 * @var Item The item
	 */
	protected $_item;

	/**
	 * getLayouts
	 */
	public function getLayouts($dir) {
		$layouts = parent::getLayouts($dir);

		return $layouts;
	}

	/**
	 * setItem
	 */
	public function setItem($item) {
		$this->_item = isset($item) ? $item : null;
	}

	/**
	 * setLayout
	 */
	public function setLayout($layout) {
		$this->_layout = isset($layout) ? $layout : '';
	}

	/**
	 * Render the submission using a layout
	 *
	 * @param string $layout The layout to use
	 * @param array $args The list of arguments to pass on to the layout
	 *
	 * @return string The html code generated
	 */
	public function render($layout, $args = array())
	{
		// init vars
		$this->_item = isset($args['item']) ? $args['item'] : null;

		return parent::render($layout, $args);
	}

	/**
	 * Check if a position generates output
	 *
	 * @param string $position The position to check
	 *
	 * @return boolean If the position generates output
	 */
	public function checkPosition($position) {

		foreach ($this->_getConfigPosition($position) as $index => $data) {
            if ($element = $this->_item->getElement($data['element'])) {

                $data['_layout'] = $this->_layout;
                $data['_position'] = $position;
                $data['_index'] = $index;

                if ($element->canAccess()) {

					// trigger elements beforesubmissiondisplay event
					$render = true;
					$this->app->event->dispatcher->notify($this->app->event->create($this->_item, 'element:beforesubmissiondisplay', array('render' => &$render, 'element' => $element, 'params' => $data)));

					if ($render) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Render the output of the position
	 *
	 * @param string $position The name of the position to render
	 * @param array $args The list of arguments to pass on to the layout
	 *
	 * @return string The html code generated
	 */
	public function renderPosition($position, $args = array()) {

		// init vars
		$elements = array();
		$output   = array();

		// get style
		$style = isset($args['style']) ? $args['style'] : 'submission.address';

		// store layout
		$layout = $this->_layout;

		// render elements
        foreach ($this->_getConfigPosition($position) as $data) {
            if (($element = $this->_item->getElement($data['element']))) {

				if (!$element->canAccess()) {
					continue;
				}

                // set params
                $params = array_merge((array) $data, $args);

                // check value
                $elements[] = compact('element', 'params');
            }
        }

        foreach ($elements as $i => $data) {
        	$element = $data['element'];
            $params = array_merge(array('first' => ($i == 0), 'last' => ($i == count($elements)-1)), array('trusted_mode' => false, 'show_tooltip'=> false), $data['params']);

			// trigger elements beforedisplay event
			$render = true;
			$this->app->event->dispatcher->notify($this->app->event->create($this->_item, 'element:beforeaddressdisplay', array('render' => &$render, 'element' => $element, 'params' => &$params)));

			if ($render) {
				$element->setItem($this->_item);
				$output[$i] = parent::render("element.$style", array('element' => $element, 'params' => $params, 'item' => $this->_item));

				// trigger elements afterdisplay event
				$this->app->event->dispatcher->notify($this->app->event->create($this->_item, 'element:afteraddressdisplay', array('html' => &$output[$i], 'element' => $element, 'params' => $params)));
			}
        }

		// restore layout
		$this->_layout = $layout;

		return implode("\n", $output);
	}

	/**
	 * Get the configuration for this position
	 *
	 * @param string $position The name of the position
	 *
	 * @return JSONData The configuration object
	 */
	protected function _getConfigPosition($position) {
		$config	= $this->getConfig('address')->get('address.'.$this->_layout);
        return $config && isset($config[$position]) ? $config[$position] : array();
    }

	/**
	 * Get the configuration for the given position
	 *
	 * @param string $position The name of the position
	 *
	 * @return JSONData The config for this position
	 */
    public function getConfigPositions($type, $layout) {
		$config	= $this->getConfig('address')->get('address.'.$layout);
		return $this->app->data->create($config);
    }
}