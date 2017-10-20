<?php
/**
 * Events system
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.4
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

define('HOOK_STARTUP', 1);
define('HOOK_BEFORE_PAGE', 2);
define('HOOK_AFTER_PAGE', 3);
define('HOOK_FINISH', 4);
define('HOOK_TIBIACOM_ARTICLE', 5);
define('HOOK_TIBIACOM_BORDER_3', 6);
define('HOOK_CHARACTERS_BEFORE_INFORMATIONS', 7);
define('HOOK_CHARACTERS_AFTER_INFORMATIONS', 8);
define('HOOK_CHARACTERS_BEFORE_SIGNATURE', 9);
define('HOOK_CHARACTERS_AFTER_SIGNATURE', 10);
define('HOOK_CHARACTERS_AFTER_ACCOUNT', 11);
define('HOOK_CHARACTERS_AFTER_CHARACTERS', 12);
define('HOOK_FIRST', HOOK_STARTUP);
define('HOOK_LAST', HOOK_CHARACTERS_AFTER_CHARACTERS);

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
		
		global $db, $config, $template_path, $ots, $content;
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
		$hooks = $db->query('SELECT `name`, `type`, `file` FROM `' . TABLE_PREFIX . 'hooks` WHERE `enabled` = 1 ORDER BY `ordering`;');
		foreach($hooks as $hook)
			$this->register($hook['name'], $hook['type'], $hook['file']);
	}
}
?>
