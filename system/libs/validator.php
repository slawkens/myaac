<?php
/**
 * Validator class
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.0.3
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

class Validator
{
	/**
	 * Advanced mail validator
	 *
	 * @param string $email
	 */
	public static function email($email) {
		return preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[A-z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/', $email);
	}

	/**
	 * Simple string validator, checks if string contains valid characters
	 *
	 * @param string $str String to validate
	 * @param boolean $numbers Numbers should be allowed?
	 */
	public static function str($str, $numbers = false) {
		return preg_match('/^[a-z0-9\ ]*$/i', $str);
	}
}
?>
