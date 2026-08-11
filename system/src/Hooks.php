<?php

namespace MyAAC;

class Hooks
{
	private static array $_hooks = [];

	public function register($hook, $type = '', $file = null): void
	{
		if(!($hook instanceof Hook))
			$hook = new Hook($hook, $type, $file);

		self::$_hooks[$hook->type()][] = $hook;
	}

	public function unregister($name, $type, $file): void
	{
		if (is_string($type)) {
			$type = constant($type);
		}

		if(!isset(self::$_hooks[$type])) {
			return;
		}

		foreach(self::$_hooks[$type] as $id => $hook) {
			if($name == $hook->name()
				&& $type == $hook->type()
				&& $file == $hook->file()
			) {
				unset(self::$_hooks[$type][$id]);
			}
		}
	}

	public function trigger($type, $params = []): bool
	{
		$ret = true;

		if(isset(self::$_hooks[$type])) {
			foreach(self::$_hooks[$type] as $name => $hook) {
				/** @var Hook $hook */
				if (!$hook->execute($params)) {
					$ret = false;
				}
			}
		}

		return $ret;
	}

	public function triggerFilter($type, &$args): void
	{
		if(isset(self::$_hooks[$type])) {
			foreach(self::$_hooks[$type] as $hook) {
				/** @var Hook $hook */
				$hook->executeFilter($args);
			}
		}
	}

	public function exist($type): bool {
		return isset(self::$_hooks[$type]);
	}

	public function load(): void
	{
		foreach(Plugins::getHooks() as $hook) {
			$this->register($hook['name'], $hook['type'], $hook['file']);
		}

		Plugins::clearWarnings();
	}
}
