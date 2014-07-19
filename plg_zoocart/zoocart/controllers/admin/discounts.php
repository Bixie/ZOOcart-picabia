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
 * Class DiscountsController
 * Operating with order-based discounting coupons
 */
class DiscountsController extends AdminResourceController {

	/**
	 * Class constructor
	 *
	 * @param array $default
	 */
	public function __construct($default = array()) {

		$this->resource_name = 'discounts';

		$this->resource_class = 'Discount';

		parent::__construct($default);
	}

}

/**
 * Class DiscountsControllerException
 */
class DiscountsControllerException extends AppException {}