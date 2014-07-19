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
 * Class zoocartUserHelper
 * User data operations support helper
 */
class zoocartDiscountHelper extends AppHelper {

	/**
	 * validateCoupon
	 */
	public function validateCoupon($code)
	{
		// init vars:
		$success = true;
		$response = array('errors' => array(), 'notices' => array());

		// general check
		if(empty($code) || !$this->app->zoocart->getConfig()->get('discounts_allowed', 0)){
			$response['success'] = false;
			return $response;
		}

		$user = $this->app->user->get();
		$discount = $this->app->table->discounts->getDiscountByCode($code);
		if(!empty($discount) && $discount->published){
			
			// Check datetime restrictions:
			$vfrom = strtotime($discount->valid_from);
			$vto = strtotime($discount->valid_to);
			$now = strtotime($this->app->date->create());

			if($vfrom > 0 || $vto > 0){
				if($now < $vfrom) {
					$response['errors'][] = JText::_('PLG_ZOOCART_COUPON_REPORT_NOTYET');
					$success = false;
				} else if ($now > $vto) {
					$response['errors'][] = JText::_('PLG_ZOOCART_COUPON_REPORT_EXPIRED');
					$success = false;
				}
			}

			// Check usergroup restrictions:
			if(!empty($discount->usergroups)){
				$allowed_groups = explode(',',$discount->usergroups);
				$usergroups = $user->getAuthorisedGroups();
				$intersect = array_intersect($allowed_groups, $usergroups);
				if(empty($intersect)){
					$response['errors'][] = JText::_('PLG_ZOOCART_COUPON_REPORT_RESTRICTED');
					$success = false;
				}
			}

			// Check times of use per user
			if($discount->hits_per_user){
				$hits = $this->app->table->user_discount->getRecord($user->id,$discount->id);
				if(!empty($hits) && $hits->hits>=$discount->hits_per_user){
					$response['errors'][] = JText::_('PLG_ZOOCART_COUPON_REPORT_MAX_HITS');
					$success = false;
				}
			}

		} else {
			$response['errors'][] = JText::_('PLG_ZOOCART_COUPON_REPORT_INVALID');
			$success = false;
		}

		// set discount object
		if($success) {
			$response['discount'] = $discount;
		}

		// return
		$response['success'] = $success;
		return $response;
	}
}