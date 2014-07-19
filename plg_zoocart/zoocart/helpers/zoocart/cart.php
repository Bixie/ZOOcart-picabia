<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/*
	Class: zoocartCartHelper
*/
class zoocartCartHelper extends AppHelper {

	protected $_cartitems = array();
	protected $_fee = 0;

	/**
	 * Sum the fee
	 *
	 * @param int $fee The fee to be sumed
	 */
	public function sumFee($fee){
		$this->_fee += $fee;
	}

	/**
	 * Get the cart total net price
	 *
	 * @param int $user_id The user id to get the price from
	 *
	 * @return float The total net price
	 */
	public function getNetTotal($user_id = null)
	{
		$subtotal = 0;
		foreach ($this->getCartitems($user_id) as $cartitem) {
			$subtotal += $cartitem->getNetTotal();
		}

		return $subtotal;
	}

	/**
	 * Get the cart total net price
	 *
	 * @param int $user_id The user id to get items
	 *
	 * @return float The total net weight
	 */
	public function getTotalWeight($user_id = null)
	{
		$totalweight = 0;
		foreach ($this->getCartitems($user_id) as $cartitem) {
			$totalweight += $cartitem->getWeight();
		}

		return $totalweight;
	}

	/**
	 * Get the cart sub total price
	 *
	 * @param int $user_id The user id
	 *
	 * @return float The subtotal net price
	 */
	public function getSubtotal($user_id = null)
	{
		$net_total = 0;
		foreach ($this->getCartitems($user_id) as $cartitem) {
			$net_total += $cartitem->getNetTotal();
		}

		// plus fees
		$net_total += $this->_fee;

		return $net_total;
	}

	/**
	 * Get the cart taxes price
	 *
	 * @param int $user_id The user id
	 * @param object $address The address info
	 *
	 * @return float The taxes price
	 */
	public function getTaxes( $user_id = null, $address = null )
	{
		$taxes = 0;
		foreach ($this->getCartitems($user_id) as $cartitem) {
			$taxes += $this->app->zoocart->tax->getItemTaxes($cartitem->getItem(), $user_id, $address) * $cartitem->quantity;
		}

		// plus fees
		$taxes += $this->app->zoocart->tax->getTaxes($this->_fee, null, null, $address);

		return $taxes;
	}

	/**
	 * Get the cart total price
	 *
	 * @param int $user_id The user id
	 * @param object $address The address info
	 *
	 * @return float The total price
	 */
	public function getTotal($user_id = null, $address = null )
	{
		$total = $this->getSubtotal($user_id);
		$total += $this->getTaxes($user_id, $address);

		return $total;
	}

	/**
	 * Get the cartitems
	 *
	 * @param int $user_id The user id
	 *
	 * @return array The cartitems objects
	 */
	public function getCartitems($user_id = null)
	{
		if (!$this->_cartitems) {
			$this->_cartitems = $this->app->zoocart->table->cartitems->getByUser($user_id);
		}

		return $this->_cartitems;
	}
}