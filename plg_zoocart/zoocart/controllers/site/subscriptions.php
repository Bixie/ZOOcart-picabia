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
class SubscriptionsController extends SiteResourceController {

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
	 * Get resources available
	 *
	 * @return Array
	 */
	public function getResources()
	{

		// Filters
		$per_page		    = $this->joomla->getCfg('list_limit');
		$page				= $this->app->request->getInt('page', 1);
		$user               = $this->app->user->get();

		$count = $this->table->count();

		// in case limit has been changed, adjust limitstart accordingly
		$limitstart = ($page - 1) * $per_page;

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query  ->select('s.`id`, s.`publish_up`, s.`publish_down`, s.`user_id`, s.`order_id`, s.`item_id`, s.`published`, i.`name`')
				->from($this->table->name.' s')
				->join('LEFT',$this->app->table->item->name.' i ON i.`id`=s.`item_id`')
				->where(array(
					's.`user_id`='.(int)$user->id,
				));

		$db->setQuery($query, $limitstart, $per_page);

		$list = $db->loadObjectList('id',$this->resource_class);

		return $list;
	}

}

/**
 * Class SubscriptionsControllerException
 */
class SubscriptionsControllerException extends AppException {}