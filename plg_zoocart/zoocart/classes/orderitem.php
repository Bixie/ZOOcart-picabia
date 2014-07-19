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
 * Class representing an orderitem
 */
class Orderitem {

	/**
	 * The id of the orderitem
	 *
	 * @var int
	 */
	public $id;
	
	/**
	 * The id of the related order
	 *
	 * @var int
	 */
	public $order_id;
	
	/**
	 * The id of the related item
	 *
	 * @var int
	 */
	public $item_id;
	
	/**
	 * The name of the orderitem
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The quantity of the orderitem
	 *
	 * @var int
	 */
	public $quantity;

	/**
	 * Product variations
	 *
	 * @var string (JSON)
	 */
	public $variations;

	/**
	 * The elements of the item encoded in json format
	 *
	 * @var string
	 */
	public $elements;

	/**
	 * Subscription data
	 *
	 * @var string
	 */
	public $subscription;

	/**
	 * The unit_price of the orderitem
	 *
	 * @var float
	 */
	public $price;

	/**
	 * Item total net weight
	 *
	 * @var
	 */
	public $weight;

	/**
	 * The tax value of the orderitem
	 *
	 * @var float
	 */
	public $tax;

	/**
	 * The total value of the orderitem
	 *
	 * @var float
	 */
	public $total;

	/**
	 * The orderitem parameters
	 *
	 * @var ParameterData
	 */
	public $params;

	/**
	 * A reference to the global App object
	 *
	 * @var App
	 */
	public $app;

	/**
	 * The object of the related Item
	 *
	 * @var App
	 */
	protected $_item = null;

	/**
	 * Class Constructor
	 */
	public function __construct() {

		// get app instance
		$app = App::getInstance('zoo');
	}

	/**
	 * Bind order item with data from object
	 *
	 * @param   mixed   Data
	 *
	 * @return  object
	 */
	public function bind($data){

		if(!empty($data)){
			$own_properties = get_object_vars($this);

			if(is_object($data)){
				$properties = get_object_vars($data);
				if(!empty($properties)){
					foreach($properties as $key=>$value){
						if(array_key_exists($key, $own_properties)){
							$this->$key = $value;
						}
					}
				}
			}
		}

		return $this;
	}

	/**
	 * Get the tax
	 *
	 * @return float The tax value
	 */
	public function getTax($address) {

		if (!$this->tax) {
			$this->tax = $this->app->zoocart->tax->getTaxes($this->getPrice(), null, null, $address) * $this->quantity;
		}

		return $this->tax;
	}

	/**
	 * Get the total
	 *
	 * @return float The total value
	 */
	public function getTotal($address) {

		if (!$this->total) {
 			$this->total = $this->getTax($address) + ($this->quantity * $this->getPrice());
		}

		return $this->total;
	}

	/**
	 * Get the price
	 *
	 * @return float The unit price value
	 */
	public function getPrice() {

		if (!$this->price) {
			$this->price = $this->app->zoocart->price->getItemNetPrice($this->getItem());
		}

		return $this->price;
	}

	/**
	 * Get the associated zoo item
	 *
	 * @return object The item object
	 */
	public function getItem()
	{
		if (!$this->_item) {
			// set Item, it object must be cloned for variations support
			if ($item = $this->app->table->item->get($this->item_id)) {
				$this->_item = clone $item;
				$this->_item->elements = clone $item->elements;

				// apply variations
				$this->applyVariations();
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