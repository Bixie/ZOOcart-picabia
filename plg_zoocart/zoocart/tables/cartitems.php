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
	Class: CartitemsTable
*/
class CartitemsTable extends ZoocartTable {

	public function __construct($app) {
		parent::__construct($app, 'cartitem');
	}

	protected function _initObject($object) {

		parent::_initObject($object);

		// trigger init event
		$this->app->event->dispatcher->notify($this->app->event->create($object, 'cartitem:init'));

		return $object;
	}

	/*
		Function: save
			Override. Save object to database table.

		Returns:
			Boolean.
	*/
	public function save($object) {

		$object->modified_on = $this->app->date->create('now', $this->app->date->getOffset())->toSql();
		
		$new = !(bool) $object->id;
		
		$result = parent::save($object);

		// trigger save event
		$this->app->event->dispatcher->notify($this->app->event->create($object, 'cartitem:saved', compact('new')));

		return $result;
	}

	/*
		Function: delete
			Override. Delete object from database table.

		Returns:
			Boolean.
	*/
	public function delete($object) {

		$result = parent::delete($object);

		// trigger deleted event
		$this->app->event->dispatcher->notify($this->app->event->create($object, 'cartitem:deleted'));

		return $result;
	}

	/*
		Function: getByUser
			Method to retrieve Users cartitems.

		Parameters:
			$user_id - User ID

		Returns:
			Array - Array of categories
	*/
	public function getByUser($user_id = null)
	{
		$session_id = $this->app->session->getId();
		$user_id = ($user_id  === null) ? $this->app->user->get()->id : $user_id;

		$options = "`user_id` = " . (int) $user_id;
		if (!$user_id) {
			$options .= " AND `session_id` = ".$this->app->database->q($session_id);
		}

		return $this->mergeDuplicates($this->all(array('conditions' => $options)));
	}

	/*
		Function: flushByUserId
			Method to delete users related cartitems.

		Parameters:
			$user_id - User id

		Returns:
			Array - Related categories
	*/
	public function flushByUserId($user_id)
	{
		if(!$user_id)
			return;

		$items_to_flush = $this->getByUser($user_id);
		if(!empty($items_to_flush))
			foreach($items_to_flush as $item)
				$this->delete($item);
	}

	/*
		Function: getByItem
			Method to retrieve Item's related cartitem

		Parameters:
			$item - The Item object, array('item_id', 'variations') or ID
			$user - The User object

		Returns:
			Object - Related cartitem
	*/
	public function getByItem($item, $user = null)
	{
		// validate Item
		if (is_object($item)) {
			$item_id = $item->id;
			$variations = isset($item->variations) ? $item->variations : null;
		} else if (is_array($item)) {
			$item_id = $item['item_id'];
			$variations = isset($item['variations']) ? $item['variations'] : null;
		} else {
			$item_id = $item;
			$variations = null;
		}

		// validate user
		$user = $user == null ? $this->app->user->get() : $user;
		$user_id = $user ? $user->id : 0;

		// set session
		$session_id = $this->app->session->getId();

		// set query
		$options = "user_id = " . (int) $user_id 
				  ." AND item_id = " . (int) $item_id 
				  .($variations !== null && strlen($variations) ? " AND variations = " . $this->app->database->q($variations) : '');

		// if guest user use session instead
		if (!$user_id) {
			$options .= " AND session_id = ".$this->app->database->q($session_id);
		}

		// query
		$result = $this->mergeDuplicates($this->all(array('conditions' => $options)));

		// return first result
		return array_shift($result);
	}

	/*
		Function: mergeDuplicates
			Method to merge duplicate cartitems

		Parameters:
			$cartitems - Cart items

		Returns:
			Array - The merged cartitems objects
	*/
	public function mergeDuplicates($cartitems)
	{
		foreach($cartitems as $key => &$item)
		{
			// get copy of cartitems removing the current one to avoid comparing it with it self
			$new = $cartitems;
			unset($new[$key]);

			foreach($new as $subkey => $subitem) {
				
				// compare
				if (($item->item_id === $subitem->item_id) && ($item->variations === $subitem->variations)) {

					// merge quantities
					$item->quantity += $subitem->quantity;

					// save updated object
					$this->save($item);

					// remove duplicates
					$this->delete($cartitems[$subkey]);
					unset($cartitems[$subkey]);
				}
			}
		}

		return $cartitems;
	}
}

/*
	Class: CartitemTableException
*/
class CartitemTableException extends ZoocartTableException {}