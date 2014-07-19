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
 * Class Subscription
 * Implements user subscription
 */
class Subscription {

	/**
	 * @var int
	 */
	public $id;

	/**
	 * @var int
	 */
	public $item_id;

	/**
	 * @var int
	 */
	public $order_id;

	/**
	 * @var int
	 */
	public $user_id;

	/**
	 * @var string
	 */
	public $publish_up;

	/**
	 * @var string
	 */
	public $publish_down;

	/**
	 * @var bool
	 */
	public $published;

	/**
	 * Class constructor
	 */
	public function __constructor(){
	}
}
 