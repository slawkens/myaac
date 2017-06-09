<?php
/**
 * Events system
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.2.4
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

define('HOOK_STARTUP', 1);
define('HOOK_BEFORE_PAGE', 2);
define('HOOK_AFTER_PAGE', 3);
define('HOOK_FINISH', 4);
define('HOOK_TIBIACOM_ARTICLE', 5);
define('HOOK_TIBIACOM_BORDER_3', 6);
define('HOOK_FIRST', HOOK_STARTUP);
define('HOOK_LAST', HOOK_TIBIACOM_BORDER_3);

$hook_types = array(
	'STARTUP' => HOOK_STARTUP,
	'BEFORE_PAGE' => HOOK_BEFORE_PAGE,
	'AFTER_PAGE' => HOOK_AFTER_PAGE,
	'FINISH' => HOOK_FINISH,
	'TIBIACOM_ARTICLE' => HOOK_TIBIACOM_ARTICLE,
	'TIBIACOM_BORDER_3' => HOOK_TIBIACOM_BORDER_3
);

class Hook
{
	private $_name, $_type, $_file;

	public function __construct($name, $type, $file) {
		$this->_name = $name;
		$this->_type = $type;
		$this->_file = $file;
	}

	public function execute($params)
	{
		/*if(is_callable($this->_callback))
		{
			$tmp = $this->_callback;
			$ret = $tmp($params);
		}*/
		
		global $db, $config, $template_path, $ots;
		if(file_exists(BASE . $this->_file)) {
			require(BASE . $this->_file);
		}

		return true;
	}

	public function name() {return $this->_name;}
	public function type() {return $this->_type;}
}

class Hooks
{
	private static $_hooks = array();

	public function register($hook, $type = '', $file = null) {
		if(!($hook instanceof Hook))
			$hook = new Hook($hook, $type, $file);

		self::$_hooks[$hook->type()][] = $hook;
	}

	public function trigger($type, $params = array())
	{
		$ret = true;
		if(isset(self::$_hooks[$type]))
		{
			foreach(self::$_hooks[$type] as $name => $hook)
				$ret = $hook->execute($params);
		}

		return $ret;
	}
	
	public function load()
	{
		global $db;
		$hooks = $db->query('SELECT `name`, `type`, `file` FROM `' . TABLE_PREFIX . 'hooks`;');
		foreach($hooks as $hook)
			$this->register($hook['name'], $hook['type'], $hook['file']);
	}
}
?>
