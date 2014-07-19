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
 * Class SubscriptionsTable
 * Discount db table
 */
class SubscriptionsTable extends ZoocartTable {

	/**
	 * Class constructor
	 *
	 * @param $app
	 */
	public function __construct($app) {
		parent::__construct($app, 'subscription');
	}

	/**
	 * Get subscription by order and item
	 *
	 * @param   int Order
	 * @param   int Item
	 *
	 * @return mixed
	 */
	public function getRelatedSubscription($order_id, $item_id){
		$db = JFactory::getDBo();
		$query = $db->getQuery(true);
		$query  ->select('*')
				->from($this->name)
			    ->where(array(
				  'order_id='.(int)$order_id,
				  'item_id='.(int)$item_id,
			          ));
		$db->setQuery($query,0,1);
		$subscription = $db->loadObject($this->class,'id');

		return $subscription;
	}

}

/**
 * Class SubscriptionsTableException
 */
class SubscriptionsTableException extends ZoocartTableException{}