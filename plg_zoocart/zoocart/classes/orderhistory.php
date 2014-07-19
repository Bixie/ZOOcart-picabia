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
 * Class Orderhistory
 */
class Orderhistory {

	/**
	 * @var int Keyfield
	 */
	public $id;

	/**
	 * @var int Order id
	 */
	public $order_id;

	/**
	 * @var int Timestamp
	 */
	public $timestamp;

	/**
	 * @var string  Property name, that have been changed
	 */
	public $property;

	/**
	 * @var text Old value
	 */
	public $value_old;

	/**
	 * @var text New value
	 */
	public $value_new;

	/**
	 * @var int Modified by User
	 */
	public $modified_by;

}