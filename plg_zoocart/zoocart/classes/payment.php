<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class Payment {

	public $id;

	public $payment_method;
	
	public $order_id;

	public $transaction_id;

	public $data;

	public $total;

	public $status;

	public $created_in;

	protected $_data = null;

	public function getData() {

		if (!$this->_data) {
			$zoo = App::getInstance('zoo');
			$this->_data = $zoo->data->create($this->data);
		}

		return $this->_data;
	}

}