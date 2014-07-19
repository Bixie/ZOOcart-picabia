<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class Update31BETA18 extends zlUpdate {

	/* List of obsolete files and folders */
	protected $_obsolete = array(
		'files'	=> array(
			'plugins/system/zoocart/zoocart/elements/quantity/tmpl/edit.php',
			'plugins/system/zoocart/zoocart/elements/quantity/tmpl/default.php'
		),
		'folders' => array()
	);

	/**
	 * Performs the update
	 */
	public function run()
	{
		// remove obsolete files
		$this->removeObsolete();
	}
}