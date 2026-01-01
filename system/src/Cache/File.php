<?php
/**
 * File cache class
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

namespace MyAAC\Cache;

class File
{
	private string $prefix;
	private string $dir;
	private bool $enabled;

	public function __construct($prefix = '', $dir = '')
	{
		$this->prefix = $prefix;
		$this->dir = $dir;

		ensureFolderExists($this->dir);
		ensureIndexExists($this->dir);

		$this->enabled = (file_exists($this->dir) && is_dir($this->dir) && is_writable($this->dir));
	}

	public function set($key, $var, $ttl = 0): void
	{
		$file = $this->_name($key);
		file_put_contents($file, $var);

		if ($ttl === 0) {
			$ttl = 365 * 24 * 60 * 60; // 365 days
		}

		touch($file, time() + $ttl);
	}

	public function get($key): string
	{
		$tmp = '';
		if ($this->fetch($key, $tmp)) {
			return $tmp;
		}

		return '';
	}

	public function fetch($key, &$var): bool
	{
		$file = $this->_name($key);
		if (!file_exists($file) || filemtime($file) < time()) {
			return false;
		}

		$var = file_get_contents($file);
		return true;
	}

	public function delete($key): void
	{
		$file = $this->_name($key);
		if (file_exists($file)) {
			unlink($file);
		}
	}

	public function enabled(): bool {
		return $this->enabled;
	}

	private function _name($key): string {
		return sprintf('%s%s%s', $this->dir, $this->prefix, sha1($key));
	}
}
