<?php
/**
* @package		ZOOcart
* @author		ZOOlanders http://www.zoolanders.com
* @copyright	Copyright (C) JOOlanders, SL
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class ZoocartHelper extends AppHelper {

	/* prefix */
	protected $_prefix;

	/* models */
	protected $_helpers = array();

	/* app zoocart config */
	static $config = array();

	/* zoocart global cfg */
	static $global_cfg = array();
	
	/* inflection rules */
	protected static $_rules = array
	(
		'pluralization'   => array(
			'/move$/i'                      => 'moves',
			'/sex$/i'                       => 'sexes',
			'/child$/i'                     => 'children',
			'/children$/i'                  => 'children',
			'/man$/i'                       => 'men',
			'/men$/i'                       => 'men',
			'/foot$/i'                      => 'feet',
			'/feet$/i'                      => 'feet',
			'/person$/i'                    => 'people',
			'/people$/i'                    => 'people',
			'/taxon$/i'                     => 'taxa',
			'/taxa$/i'                      => 'taxa',
			'/(quiz)$/i'                    => '$1zes',
			'/^(ox)$/i'                     => '$1en',
			'/oxen$/i'                      => 'oxen',
			'/(m|l)ouse$/i'                 => '$1ice',
			'/(m|l)ice$/i'                  => '$1ice',
			'/(matr|vert|ind|suff)ix|ex$/i' => '$1ices',
			'/(x|ch|ss|sh)$/i'              => '$1es',
			'/([^aeiouy]|qu)y$/i'           => '$1ies',
			'/(?:([^f])fe|([lr])f)$/i'      => '$1$2ves',
			'/sis$/i'                       => 'ses',
			'/([ti]|addend)um$/i'           => '$1a',
			'/([ti]|addend)a$/i'            => '$1a',
			'/(alumn|formul)a$/i'           => '$1ae',
			'/(alumn|formul)ae$/i'          => '$1ae',
			'/(buffal|tomat|her)o$/i'       => '$1oes',
			'/(bu)s$/i'                     => '$1ses',
			'/(alias|status)$/i'            => '$1es',
			'/(octop|vir)us$/i'             => '$1i',
			'/(octop|vir)i$/i'              => '$1i',
			'/(gen)us$/i'                   => '$1era',
			'/(gen)era$/i'                  => '$1era',
			'/(ax|test)is$/i'               => '$1es',
			'/s$/i'                         => 's',
			'/$/'                           => 's',
		)
	);

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct($app) {
		parent::__construct($app);

		// Retrieve global zoocart cfg:
		self::$global_cfg = $this->app->zl->getConfig('zoocart');

		// set helper prefix
		$this->_prefix = 'zoocart';
	}

	/*
		Function: get
			Retrieve a helper

		Parameters:
			$name - Helper name
			$prefix - Helper prefix

		Returns:
			Mixed
	*/
	public function get($name, $prefix = null) {
		
		// set prefix
		if ($prefix == null) {
			$prefix = $this->_prefix;
		}

		// load class
		$class = $prefix . $name . 'Helper';
		
		$this->app->loader->register($class, 'helpers:zoocart/'.strtolower($name).'.php');

		// add helper, if not exists
		if (!isset($this->_helpers[$name])) {
			$this->_helpers[$name] = class_exists($class) ? new $class($this->app) : new AppHelper($this->app, $class);
		}

		return $this->_helpers[$name];
	}
	
	/*
		Function: __get
			Retrieve a helper

		Parameters:
			$name - Helper name

		Returns:
			Mixed
	*/
	public function __get($name) {
		return $this->get($name);
	}

	/**
	 * Get applications select box
	 *
	 * @param   string
	 * @param   int
	 *
	 * @return html
	 */
	public function getAppSelectBox($attribs='', $selected=0){
		$options = array();
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query  ->select('`id` as value, `name` as text')
				->from($this->app->table->application->name)
				->order('`name` ASC');

		$db->setQuery($query);
		$list = $db->loadAssocList();

		$html = $this->app->html->genericList($list, 'app', $attribs, 'value', 'text', $selected);

		return $html;
	}

	/**
	 * Transforms the singular word to plural
	 * Method will become deprecated, when Joomla 2.5 will be unsupported.
	 * Instead of this is better to use JStringInflector class method from Joomla 3.x
	 *
	 * @param string
	 *
	 * @return string
	 */
	public function toPlural($word){

		foreach (self::$_rules['pluralization'] as $regexp => $replacement)
		{
			$matches = null;
			$plural  = preg_replace($regexp, $replacement, $word, -1, $matches);

			if ($matches > 0)
			{
			  return $plural;
			}
		}

		return $word;
	}

	/**
	 * Get items select box
	 *
	 * @param   string
	 * @param   int
	 *
	 * @return html
	 */
	public function getItemSelectBox($app_id, $attribs='', $selected=0){
		$options = array();
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query  ->select('`id` as value, `name` as text')
		        ->from($this->app->table->item->name)
				->where(array(
					'application_id='.(int)$app_id
				        ))
		        ->order('`name` ASC');

		$db->setQuery($query);
		$list = $db->loadAssocList();

		$html = $this->app->html->genericList($list, 'item_id', $attribs, 'value', 'text', $selected);

		return $html;
	}

	/**
	 * Retrieve ZOOcart config
	 * 
	 * @param string $app_id The App id, optional
	 * 
	 * @return object The config data wrapped with DATA class
	 */
	public function getConfig($app_id = null) {

		$config = self::$global_cfg;

		if(!$app_id)
			return $config;

		// Get application
		if($app_id)
		{
			$application = $app_id ? $this->app->table->application->get($app_id) : null;

			// save retrieved config in cache
			if(!isset(self::$config[$app_id])) {
				self::$config[$app_id] = $this->app->data->create($application->getParams()->get('global.zoocart.'));
			}

			$app_own_config = self::$config[$app_id];

			if(!empty($app_own_config))
			{
				// Merging settings:
				$params = $app_own_config->getArrayCopy();
				foreach($params as $key=>$value)
				{
					$config->set($key,$value);
				}
			}
		}

		// return config
		return $config;
	}

	/**
	 * Set ZOOcart app specific params
	 * 
	 * @param string $app_id The App id, optional
	 * @param array $config The config data to be set
	 * 
	 * @return boolean True on success
	 */
	public function setConfig($app_id = null, $config = array()) {

		// set Application object or use current one
		$application = $app_id ? $this->app->table->application->get($app_id) : $this->app->zoo->getApplication();

		// basic check
		if (!$application) return false;

		// wrapp the config with DATA class
		if (is_array($config)) {
			$config = $this->app->data->create($config);
		}

		// save config
		self::$config[$app_id] = $config;
		$application->params->set('global.zoocart.', $config);
		$this->app->table->application->save($application);

		return true;
	}

	public function getElementsList($app_groups = array(), $type_ids = array(), $element_type = null, $name = 'zoocart', $selected = '', $add_none = false ) {

		$list = $this->app->zlfield->elementsList($app_groups, $element_type, $type_ids);

		$options = array();
		if ($add_none) {
			$options[] = $this->app->html->_('select.option', '', JText::_('PLG_ZLFRAMEWORK_NONE'));
		}

		foreach($list as $label => $id) {
			$options[] = $this->app->html->_('select.option', $id, $label);
 		}

		return $this->app->html->_('select.genericlist', $options, $name, '', 'value', 'text', $selected);
	}

	public function orderstatesList($name, $selected, $attribs = '', $add_none = false) {

		$options = array();		

		if ($add_none) {
			$options[] = $this->app->html->_('select.option', '', '- ' . JText::_('PLG_ZOOCART_SELECT_STATE') . ' -');
		}

		foreach ($this->app->zoocart->table->orderstates->all() as $orderstate) {
			$options[] = $this->app->html->_('select.option', $orderstate->id, JText::_($orderstate->name));
		}

		return $this->app->html->_('select.genericlist', $options, $name, $attribs, 'value', 'text', $selected);
	}

	public function userGroupsList($name, $selected, $attribs = '')
	{
		// Initialise variables.
		$db		= $this->app->database;
		$query	= $db->getQuery(true)
			->select('a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level, a.parent_id')
			->from('#__usergroups AS a')
			->leftJoin('`#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt')
			->group('a.id')
			->order('a.lft ASC');

		$db->setQuery($query);
		$options = $db->loadObjectList();

		return $this->app->html->_('select.genericlist', $options, $name, $attribs, 'value', 'text', $selected);
	}

	/**
	 * Check if a product has been purchased
	 * 
	 * @param array $items_id The list of Items ID to be checked
	 * @param int $user_id The user ID to check the order from
	 * @param int $mode The query where mode. 0=OR / 1=AND
	 * 
	 * @return boolean True on success
	 */
	public function hasPurchased($items_id, $user_id = null, $mode = 0)
	{
		// validate data
		settype($items_id, 'array');
		settype($user_id, 'int');

		if (is_null($user_id)) {
			$user_id = $this->app->user->get()->id;
		}

		// init vars
		$db	= $this->app->database;
		$states = array();
		$mode = $mode ? 'AND' : 'OR';

		// add the completed order state
		$states[] = $this->app->zoocart->getConfig()->get('finished_orderstate', 5);

		// add the payment recieved order state
		$states[] = $this->app->zoocart->getConfig()->get('payment_received_orderstate', 2);

		// set wheres
		$wheres = array();
		foreach ($items_id as $id) {
			$wheres[] = 'oi.item_id = ' . $id;
		}

		// set query
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from('`#__zoo_zl_zoocart_orders` AS o')
			->leftJoin('`#__zoo_zl_zoocart_orderitems` AS oi ON oi.order_id = o.id')
			->where('o.state IN('.implode(',', $states).') AND o.user_id = '.$user_id.' AND (' . implode(" $mode ", $wheres) . ')')
			->group('order_id');
		$db->setQuery($query);

		// query
		$result = $db->loadObjectList();

		return !empty($result);
	}

	/**
	 * Get the ZOOcart Item Type
	 *
	 * @param   object $item
	 *
	 * @return string Item, Product, Digital or Subscription
	 */
	public function getItemType($item)
	{
		// retrieve zoocart element
		$elements = $item->getElementsByType('addtocart');
		$element = array_shift($elements);

		if ($element) {
			return $element->config->find('specific.item_type', 'product');
		}

		// if no zoocart element set, is zoo standart item
		return 'item';
	}

	/**
	 * Get languages list
	 *
	 * @param string $name
	 * @param string $attribs
	 * @param $selected
	 *
	 * @return string
	 */
	public function getLanguages($name = '', $attribs = '', $selected = null){

		$languages = JFactory::getLanguage()->getKnownLanguages(JPATH_SITE);

		return $this->app->html->_('select.genericlist', $languages, $name, $attribs, 'tag', 'name', $selected);
	}

	/**
	 * Check for plugins that can cause fatal errors in this version of ZOOcart
	 *
	 * @return  array    Array of active plugin names
	 */
	public function getBreakingPlugins(){
		$plugins = array();
		$zoocart_breaking_plugins = array('zooaccess', 'zooaksubs', 'zoolingual');

		foreach($zoocart_breaking_plugins as $plg)
		{
			if(JPluginHelper::getPlugin('system', $plg)){
				$plugins[] = $plg;
			}
		}

		return $plugins;
	}
}