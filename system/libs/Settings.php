<?php
/**
 * CreateCharacter
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2020 MyAAC
 * @link      https://my-aac.org
 */

class Settings implements ArrayAccess
{
	static private $instance;
	private $plugins = [];
	private $settings = [];
	private $cache = [];

	/**
	 * @return Settings
	 */
	public static function getInstance()
	{
		if (!self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function load()
	{
		$cache = Cache::getInstance();
		if ($cache->enabled()) {
			$tmp = '';
			if ($cache->fetch('settings', $tmp)) {
				$this->settings = unserialize($tmp);
				return;
			}
		}

		global $db;
		$settings = $db->query('SELECT * FROM `' . TABLE_PREFIX . 'settings`');

		if($settings->rowCount() > 0) {
			foreach ($settings->fetchAll(PDO::FETCH_ASSOC) as $setting) {
				$this->settings[$setting['plugin_name']][$setting['key']] = $setting['value'];
			}
		}

		if ($cache->enabled()) {
			$cache->set('settings', serialize($this->settings), 600);
		}
	}

	#[\ReturnTypeWillChange]
	public function offsetSet($offset, $value)
	{
		if (is_null($offset)) {
			throw new \RuntimeException("Settings: You cannot set empty offset with value: $value!");
		}

		$pluginName = $offset;
		if (strpos($offset, '.')) {
			$explode = explode('.', $offset, 2);

			$pluginName = $explode[0];
			$key = $explode[1];
		}

		$this->loadPlugin($pluginName);

		// remove whole plugin settings
		if (!isset($key)) {
			$this->plugins[$pluginName] = [];

			// remove from settings
			if (isset($this->settings[$pluginName])) {
				unset($this->settings[$pluginName]);
			}

			// remove from cache
			if (isset($this->cache[$pluginName])) {
				unset($this->cache[$pluginName]);
			}
			/*foreach ($this->cache as $_key => $value) {
				if (strpos($_key, $pluginName) !== false) {
					unset($this->cache[$_key]);
				}
			}*/
		}

		$this->settings[$pluginName][$key] = $value['value'];
	}

	#[\ReturnTypeWillChange]
	public function offsetExists($offset) {
		return isset($this->settings[$offset]);
	}

	#[\ReturnTypeWillChange]
	public function offsetUnset($offset) {
		unset($this->settings[$offset]);
	}

	/**
	 * Get settings
	 * Usage: $setting['plugin_name.key']
	 * Example: $settings['shop_system.paypal_email']
	 *
	 * @param mixed $offset
	 * @return array|mixed
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet($offset)
	{
		// try cache hit
		if(isset($this->cache[$offset])) {
			return $this->cache[$offset];
		}

		$pluginName = $offset;
		if (strpos($offset, '.')) {
			$explode = explode('.', $offset, 2);

			$pluginName = $explode[0];
			$key = $explode[1];
		}

		$this->loadPlugin($pluginName);

		// return specified plugin settings (all)
		if(!isset($key)) {
			return $this->plugins[$pluginName];
		}

		$ret = [];
		if(isset($this->plugins[$pluginName][$key])) {
			$ret = $this->plugins[$pluginName][$key];
		}

		if(isset($this->settings[$pluginName][$key])) {
			$value = $this->settings[$pluginName][$key];

			$ret['value'] = $value;
		}
		else {
			$ret['value'] = $this->plugins[$pluginName][$key]['default'];
		}

		if(isset($ret['type'])) {
			switch($ret['type']) {
				case 'boolean':
					$ret['value'] = $ret['value'] === 'true';
					break;

				case 'number':
					$ret['value'] = (int)$ret['value'];
					break;

				default:
					break;
			}
		}

		$this->cache[$offset] = $ret;
		return $ret;
	}

	private function loadPlugin($pluginName)
	{
		if (!isset($this->plugins[$pluginName])) {
			if ($pluginName === 'core') {
				$file = SYSTEM . 'settings.php';
			} else {
				$file = PLUGINS . $pluginName . '/settings.php';
			}

			if (!file_exists($file)) {
				throw new \RuntimeException('Failed to load settings file for plugin: ' . $pluginName);
			}

			$this->plugins[$pluginName] = require $file;
		}
	}
}
