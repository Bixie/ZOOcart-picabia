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
 * Class Email
 * Implements email template
 */
class Email {

	/**
	 * @var int
	 */
	public $id;

	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var string
	 */
	public $groups;

	/**
	 * @var string
	 */
	public $cc;

	/**
	 * @var string
	 */
	public $bcc;

	/**
	 * @var string
	 */
	public $subject;

	/**
	 * @var string
	 */
	public $template;

	/**
	 * @var bool
	 */
	public $published;

	/**
	 * @var string
	 */
	public $language;

	/**
	 * Class constructor
	 */
	public function __constructor(){
	}
}
 