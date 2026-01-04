<?php
/**
 * PHP cache class
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

namespace MyAAC\Cache;

class PHP
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
		$var = var_export($var, true);

		if ($ttl === 0) {
			$ttl = 365 * 24 * 60 * 60; // 365 days
		}

		$expires = time() + $ttl;

		// Write to temp file first to ensure atomicity
		$tmp = $this->dir . "tmp_$key." . uniqid('', true) . '.tmp';
		file_put_contents($tmp, "<?php return ['expires' => $expires, 'var' => $var];", LOCK_EX);

		$file = $this->_name($key);
		rename($tmp, $file);
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
		if (!file_exists($file)) {
			return false;
		}

		$content = include $file;
		if (!isset($content) || $content['expires'] < time()) {
			return false;
		}

		$var = $content['var'];
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
		return sprintf('%s%s%s', $this->dir, $this->prefix, sha1($key) . '.php');
	}
}
