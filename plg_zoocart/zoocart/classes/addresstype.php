<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// register Element class
App::getInstance('zoo')->loader->register('Type', 'classes:type.php');

/**
 * Class that represents a ZOOcart Type
 *
 * @package Component.Classes
 */
class AddressType extends Type {

	/**
	 * Get the configuration file path
	 *
	 * @param  string $id The type id (default: the current type id)
	 *
	 * @return string     The path to the file
	 */
	public function getConfigFile($id = null) {

		$id = ($id !== null) ? $id : $this->id;

		if ($id && ($path = $this->app->path->path('zoocart:'))) {
			return $path.'/types/'.$id.'.config';
		}

		return null;
	}
}

/**
 * Exception for the Type class
 *
 * @see ZOOcartType
 */
class AddressTypeException extends AppException {}