<?php
/**
 * Cache class
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @author    Mark Samman (Talaturen) <marksamman@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

namespace MyAAC\Cache;

/**
 * Class Cache
 *
 * @method set($key, $var, $ttl = 0)
 * @method get($key)
 * @method fetch($key, &$var)
 * @method delete($key)
 */
class Cache
{
	static private $instance;

	/**
	 * @return Cache
	 */
	public static function getInstance()
	{
		if (!self::$instance) {
			return self::generateInstance(config('cache_engine'), config('cache_prefix'));
		}

		return self::$instance;
	}

	/**
	 * @param string $engine
	 * @param string $prefix
	 * @return Cache
	 */
	public static function generateInstance($engine = '', $prefix = '')
	{
		if (config('env') === 'dev') {
			self::$instance = new self();
			return self::$instance;
		}

		switch (strtolower($engine)) {
			case 'apc':
				self::$instance = new APC($prefix);
				break;

			case 'apcu':
				self::$instance = new APCu($prefix);
				break;

			case 'xcache':
				self::$instance = new XCache($prefix);
				break;

			case 'file':
				self::$instance = new File($prefix, CACHE);
				break;

			case 'php':
				self::$instance = new PHP($prefix, CACHE);
				break;

			case 'auto':
				self::$instance = self::generateInstance(self::detect(), $prefix);
				break;

			default:
				self::$instance = new self();
				break;
		}

		return self::$instance;
	}

	/**
	 * @return string
	 */
	public static function detect()
	{
		if (function_exists('apc_fetch'))
			return 'apc';
		else if (function_exists('apcu_fetch'))
			return 'apcu';
		else if (function_exists('xcache_get') && ini_get('xcache.var_size'))
			return 'xcache';

		return 'file';
	}

	/**
	 * @return bool
	 */
	public function enabled()
	{
		return false;
	}

	public static function remember($key, $ttl, $callback)
	{
		$cache = self::getInstance();
		if (!$cache->enabled()) {
			return $callback();
		}

		$value = null;
		if ($cache->fetch($key, $value)) {
			return unserialize($value);
		}

		$value = $callback();
		$cache->set($key, serialize($value), $ttl);
		return $value;
	}
}
