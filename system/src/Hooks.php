<?php

namespace MyAAC;

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
			foreach(self::$_hooks[$type] as $name => $hook) {
				/** @var $hook Hook */
				if (!$hook->execute($params)) {
					$ret = false;
				}
			}
		}

		return $ret;
	}

	public function exist($type) {
		return isset(self::$_hooks[$type]);
	}

	public function load()
	{
		foreach(Plugins::getHooks() as $hook) {
			$this->register($hook['name'], $hook['type'], $hook['file']);
		}

		Plugins::clearWarnings();
	}
}
