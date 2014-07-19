<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class zoocartQuantityHelper extends AppHelper {

	/**
	 * Remove quantity
	 * 
	 * @param object $item The Item object
	 * @param int $quantity The quantity value
	 * 
	 * @return int The new quantity value
	 */
	public function alterQuantity($item, $quantity, $action) {
		// init vars
		$element = $this->getQuantityElement($item);
		$old_quantity = $element->get('value');
		settype($action, 'bool');

		// alter
		if($action == true) {
			$new_quantity = $old_quantity + $quantity;
		} else {
			$new_quantity = $old_quantity - $quantity;
		}
		$element->set('value', $new_quantity);

		// save updated quantity value
		$this->app->table->item->save($item);

		return $new_quantity;
	}

	/**
	 * Get the Quantity element object
	 * 
	 * @param object $item The Item containing the Element
	 * 
	 * @return object The Quantity Element
	 */
	public function getQuantityElement($item) {
		// get the first quantity element of the type
		$elements = $item->getElementsByType('quantity');
		return array_shift($elements);	
	}

	/**
	 * Check the quantity taking in consideration the Cart as well
	 * 
	 * @param object $item The Item object
	 * 
	 * @return int The quantity value
	 */
	public function checkQuantity($item)
	{
		// get the total quantity
		$quantity = $this->getQuantity($item);

		// get related cartitem
		if (isset($item->cartitem_id)) {
			// trough it's id
			$cartitem = $this->app->zoocart->table->cartitems->get($item->cartitem_id);
		} else {
			// or trough the Item
			$cartitem = $this->app->zoocart->table->cartitems->getByItem($item);
		}

		// rest cartitems amount, if any
		if ($cartitem) $quantity = $quantity - (int)$cartitem->quantity;

		// return result
		return $quantity;
	}

	/**
	 * Get quantity
	 * 
	 * @param object $item The Item object
	 * 
	 * @return int The quantity value
	 */
	public function getQuantity($item) {
		if ($element = $this->getQuantityElement($item)) {
			return $element->get('value', 0);
		}

		return '';
	}
}