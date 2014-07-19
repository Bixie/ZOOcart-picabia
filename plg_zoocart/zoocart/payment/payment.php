<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class JPayment extends JObject {

	public $id = null;

	public $transaction_id = null;

	public $ip = null;

	public $status = null;

	public $created_on = null;

	public $params = null;
}


?>