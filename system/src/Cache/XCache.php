<?php
/**
 * XCache class
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @author    Mark Samman (Talaturen) <marksamman@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

namespace MyAAC\Cache;

class XCache
{
	private string $prefix;
	private bool $enabled;

	public function __construct($prefix = '')
	{
		$this->prefix = $prefix;
		$this->enabled = function_exists('xcache_get') && ini_get('xcache.var_size');
	}

	public function set($key, $var, $ttl = 0): void
	{
		$key = $this->prefix . $key;
		xcache_unset($key);
		xcache_set($key, $var, $ttl);
	}

	public function get($key): string
	{
		$tmp = '';
		if ($this->fetch($this->prefix . $key, $tmp)) {
			return $tmp;
		}

		return '';
	}

	public function fetch($key, &$var): bool
	{
		$key = $this->prefix . $key;
		if (!xcache_isset($key)) {
			return false;
		}

		$var = xcache_get($key);
		return true;
	}

	public function delete($key): void {
		xcache_unset($this->prefix . $key);
	}

	public function enabled(): bool {
		return $this->enabled;
	}
}
