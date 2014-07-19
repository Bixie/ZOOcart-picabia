<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// register ZLFW installer class
JLoader::register('zlInstallerScript', JPATH_ROOT . '/plugins/system/zlframework/zlframework/classes/installer.php');

/*
	The Installer class
*/
class plgSystemZoocartInstallerScript extends zlInstallerScript
{
	public $lng_prefix = 'PLG_ZOOCART_SYS';
	protected $_ext = 'zoocart';

	/**
	 * Called before any type of action
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install)
	 * @param   object  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($type, $parent)
	{
		// init vars
		$this->initVars($type, $parent);

		// load ZLFW sys language file
		JFactory::getLanguage()->load('plg_system_zlframework.sys', JPATH_ADMINISTRATOR, 'en-GB', true);

		// check dependencies if not uninstalling
		if($this->type != 'uninstall' && !$this->checkDependencies($parent)){
			Jerror::raiseWarning(null, $this->_error);
			return false;
		}

		// on uninstall
		if($this->type == 'uninstall'){
			// save the sql files now to be able to iterate over later
			$this->sqls = JFolder::files($this->target . '/zoocart/sql');
		}
	}

	/**
	 * Called on installation
	 *
	 * @param   object  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	function install($parent)
	{
		// enable plugin
		$this->db->setQuery("UPDATE `#__extensions` SET `enabled` = 1 WHERE `type` = 'plugin' AND `element` = '{$this->_ext}' AND `folder` = 'system'")
			->execute();
	}

	/**
	 * Called on update
	 *
	 * @return void
	 */
	public function update($parent){}

	/**
	 * Called on uninstallation
	 *
	 * @param   object  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	function uninstall($parent)
	{
		// disable all zoocart modules
		$this->db->setQuery("UPDATE `#__extensions` SET `enabled` = 0 WHERE `element` LIKE '%zoocart%'")->execute();

		// drop tables
		if(is_array($this->sqls)) foreach($this->sqls as $sql)
		{
			$sql = basename($sql, '.sql');
			$this->db->setQuery('DROP TABLE IF EXISTS `#__zoo_zl_zoocart_' . $sql . '`')->execute();
		}
	}

	/**
	 * Called after install
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install)
	 * @param   object  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($type, $parent)
	{
		// after not uninstall
		if($this->type != 'uninstall')
		{
			// create/update tables
			$sqls = JFolder::files($this->source . '/zoocart/sql', '.sql$');
			if(is_array($sqls)) foreach($sqls as $sql)
			{
				$sql = JFile::read($this->source . '/zoocart/sql/' . $sql);
				$queries = explode("-- QUERY SEPARATOR --", $sql);
				foreach($queries as $sql) {
					if (!$this->db->setQuery($sql)->execute()) {
						$this->_error = 'ZL Error Query: ' . $sql . ' - ' . $this->db->getErrorMsg();
						return false;
					}
				}
			}
		}

		// after update
		if($this->type == 'update')
		{
			// temporal solution to cleanup the schema table wrongly populated in previous versions
			$this->db->setQuery("SELECT * FROM `#__schemas` WHERE `extension_id` = '{$this->getExtID()}'");
			if($res = $this->db->loadObject()) {
				if($res->version_id == '2013-01-01') {
					$this->cleanVersion();
				}
			}

			// run update scripts
			if(!$this->runUpdates($this->getVersion(), $this->source.'/updates')) {
				return false;
			}	
		}

		// execute common functions
		parent::postflight($type, $parent);
	}
}