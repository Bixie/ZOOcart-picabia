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
 * Class SubscriptionsController
 * Operating with subscriptions
 */
class SubscriptionsController extends AdminResourceController {

	/**
	 * Class constructor
	 *
	 * @param array $default
	 */
	public function __construct($default = array()) {

		$this->resource_name = 'subscriptions';

		$this->resource_class = 'Subscription';

		parent::__construct($default);
	}

	/**
	 * Before display initializations
	 */
	protected function beforeListDisplay() {
		// Populate order state
		$order = $this->_getOrdering(true);
		$this->lists['order'] = $order->order;
		$this->lists['order_Dir'] = $order->direction;
	}

	/**
	 * Get records list
	 *
	 * @return mixed
	 */
	protected function getResources() {
		// Filters
		$state_prefix       = $this->option.'.'.$this->resource_name;
		$limit		        = $this->joomla->getUserStateFromRequest('global.list.limit', 'limit', $this->joomla->getCfg('list_limit'), 'int');
		$limitstart			= $this->joomla->getUserStateFromRequest($state_prefix.'limitstart', 'limitstart', 0,	'int');
		$db                 = JFactory::getDBO();

		$count = $this->table->count();

		// in case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$limitstart = $limitstart > $count ? floor($count / $limit) * $limit : $limitstart;

		$query = $db->getQuery(true);
		$query  ->select('s.*, i.`name` AS `level_name`, u.`name` AS `user_name`')
				->from($this->table->name.' s')
				->join('LEFT',$this->app->table->item->name.' AS i ON i.`id`=s.`item_id`')
				->join('LEFT','#__users AS u ON u.`id`=s.`user_id`')
				->order($this->_getOrdering());

		$db->setQuery($query,$limitstart,$limit);

		$list = $db->loadObjectList('id',$this->resource_class);

		return $list;
	}
}

/**
 * Class Subs_levelsControllerException
 */
class SubscriptionsControllerException extends AppException {}