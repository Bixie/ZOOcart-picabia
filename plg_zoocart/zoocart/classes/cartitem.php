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
 * Object that represents an Cartitem Instance
 */
class Cartitem {

	public $id;

	public $user_id;

	public $session_id;

	public $item_id;

	public $quantity;

	public $subscription;

	public $variations;

	public $params;

	public $modified_on;

	protected $_item;

	/**
 	 * Class Constructor
 	 */
	public function __construct() {

		// init vars
		$this->app = App::getInstance('zoo');
	}

	/**
	 * Get cartitem weight
	 *
	 * @return float
	 */
	public Function getWeight(){
		return $this->app->zoocart->shipping->getItemNetWeight($this->getItem()) * $this->quantity;
	}

	/**
	 * Get the total net price
	 *
	 * @return int The price value
	 */
	public function getNetTotal() {
		return $this->getNetPrice() * $this->quantity;
	}

	/**
	 * Get the total gross price
	 *
	 * @return int The price value
	 */
	public function getGrossTotal($user_id = null) {
		return $this->getGrossPrice($user_id) * $this->quantity;
	}

	/**
	 * Get the net price
	 *
	 * @return int The price value
	 */
	public function getNetPrice() {
		return $this->app->zoocart->price->getItemNetPrice($this->getItem());
	}

	/**
	 * Get item tax
	 *
	 * @param $address  Tax address
	 *
	 * @return float
	 */
	public function getItemTax($address = null){
		return $this->app->zoocart->tax->getTaxes($this->getNetTotal(), null, null, $address);
	}

	/**
	 * Get item total (with tax)
	 *
	 * @param $address  Tax address
	 *
	 * @return float
	 */
	public function getItemTotal($address = null){
		return $this->getItemTax($address) + $this->getNetTotal();
	}

	/**
	 * Get the gross price
	 *
	 * @return int The price value
	 */
	public function getGrossPrice($user_id = null) {
		return $this->app->zoocart->price->getItemGrossPrice($this->getItem(), $user_id);
	}

	/**
	 * Get the associated zoo item
	 *
	 * @return object The item object
	 */
	public function getItem()
	{
		if (!$this->_item)
		{
			$this->_item = $this->app->table->item->get($this->item_id);

			// check if item exist and it's published
			if($this->_item && $this->_item->getState())
			{
				// object must be cloned for variations support
				$this->_item = clone $this->_item;
				$this->_item->elements = clone $this->_item->elements;
				// associate current cartitem
				$this->_item->cartitem_id = $this->id;
				// apply variations
				$this->applyVariations();

			} else {
				return false;
			}
		}

		return $this->_item;
	}

	/**
	 * Apply variations
	 */
	public function applyVariations()
	{
		// apply variations, if any
		if (strlen($this->variations)) {

			// get variation element
			$variation_el = $this->_item->getElementsByType('variations');
			$variation_el = array_shift($variation_el);

			// element check
			if ($variation_el) {
				$variation_el->applyVariations($this->variations);
			}
		}
	}
}