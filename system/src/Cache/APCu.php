<?php
/**
 * Cache APC class
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @author    Mark Samman (Talaturen) <marksamman@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

namespace MyAAC\Cache;

class APCu
{
	private string $prefix;
	private bool $enabled;

	public function __construct($prefix = '')
	{
		$this->prefix = $prefix;
		$this->enabled = function_exists('apcu_fetch');
	}

	public function set($key, $var, $ttl = 0): void
	{
		$key = $this->prefix . $key;
		apcu_delete($key);
		apcu_store($key, $var, $ttl);
	}

	public function get($key): string
	{
		$tmp = '';
		if ($this->fetch($this->prefix . $key, $tmp)) {
			return $tmp;
		}

		return '';
	}

	public function fetch($key, &$var): bool {
		return ($var = apcu_fetch($this->prefix . $key)) !== false;
	}

	public function delete($key): void {
		apcu_delete($this->prefix . $key);
	}

	public function enabled(): bool {
		return $this->enabled;
	}
}
