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

		self::$instance = match (strtolower($engine)) {
			'apc' => new APC($prefix),
			'apcu' => new APCu($prefix),
			'xcache' => new XCache($prefix),
			'file' => new File($prefix, CACHE),
			'php' => new PHP($prefix, CACHE),
			'auto' => self::generateInstance(self::detect(), $prefix),
			default => new self(),
		};

		return self::$instance;
	}

	/**
	 * @return string
	 */
	public static function detect(): string
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
	public function enabled(): bool {
		return false;
	}

	public static function remember($key, $ttl, $callback)
	{
		$cache = self::getInstance();
		if (!$cache->enabled() || $ttl == 0) {
			return $callback();
		}

		$value = null;
		if ($cache->fetch($key, $value)) {
			return unserialize($value);
		}

		// -1 for infinite cache
		if ($ttl == -1) {
			$ttl = 10 * 365 * 24 * 60 * 60; // 10 years should be enough
		}

		$value = $callback();
		$cache->set($key, serialize($value), $ttl);
		return $value;
	}
}
