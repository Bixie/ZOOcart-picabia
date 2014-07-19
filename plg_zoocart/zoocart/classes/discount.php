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
 * Class Discount
 */
class Discount {

	/**
	 * @var int id
	 */
	public $id;

	/**
	 * @var string Name(Title) of the discount
	 */
	public $name;

	/**
	 * @var string Coupon code
	 */
	public $code;

	/**
	 * @var float Discount value (% or fixed rate)
	 */
	public $discount;

	/**
	 * @var int Discount type: 0-fixed, 1-percentage
	 */
	public $type;

	/**
	 * @var string Valid from date-time
	 */
	public $valid_from;

	/**
	 * @var string Valid to date-time
	 */
	public $valid_to;

	/**
	 * @var string target usergroups
	 */
	public $usergroups;

	/**
	 * @var int Hits per user
	 */
	public $hits_per_user;

	/**
	 * @var bool Publication option
	 */
	public $published;

	/**
	 * @var int Statistic (how many times used)
	 */
	public $used;

	/**
	 * @var object App The ZOO app instance
	 */
	public $app;

	/**
 	 * Class Constructor
 	 */
	public function __construct() {

		// init vars
		$this->app = App::getInstance('zoo');
	}

	/**
	 * Reduce price according to this discount
	 *
	 * @param float price to recount
	 */
	public function discount($value = 0)
	{
		$price = $value;
		switch($this->type){
			case 1: $price -= $price * ($this->discount/100); break;
			case 0:
			default: $price -= $this->discount;
		}

		//Check received price value:
		if($price<0)
			$price = 0;

		return $price;
	}

	/**
	 * Get the discount amount
	 */
	public function getAmount()
	{
		switch($this->type){
			case 1: return floatval($this->discount).'%';
			case 0:
			default: return $this->app->zoocart->currency->format($this->discount);
		}
	}
}