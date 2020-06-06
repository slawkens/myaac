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
	private $container = array();

	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			$this->container[] = $value;
		} else {
			$this->container[$offset] = $value;
		}
	}

	public function offsetExists($offset) {
		return isset($this->container[$offset]);
	}

	public function offsetUnset($offset) {
		unset($this->container[$offset]);
	}

	public function offsetGet($offset)
	{
		if (!isset($this->container[$offset])) {
			$file = PLUGINS . $offset . '/settings.php';
			if(!file_exists($file)) {
				throw new \RuntimeException('Failed to load settings file for plugin: ' . $offset);
			}

			$this->container[$offset] = require $file;
		}

		return  $this->container[$offset];
	}
}