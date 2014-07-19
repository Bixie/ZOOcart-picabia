<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class Tax {

	public $id;
	
	public $country;
	
	public $city;
	
	public $state;

	public $zip;

	public $vies;

	public $taxrate;

	public $published;

	public $ordering;

	public $tax_class_id;

	public function getTaxClass()
	{
		$zoo = App::getInstance('zoo');
		return $zoo->zoocart->table->taxclasses->get((int)$this->tax_class_id);
	}

}