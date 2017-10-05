<?php
/**
 * Cache class
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @author    Mark Samman (Talaturen) <marksamman@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.4.3
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

class Cache
{
	static private $instance;

	static public function getInstance($engine = '', $prefix = '')
	{
		if(!self::$instance) {
			switch(strtolower($engine)) {
				case 'apc':
					require('cache_apc.php');
					self::$instance = new Cache_APC($prefix);
					break;

				case 'eaccelerator':
					require('cache_eaccelerator.php');
					self::$instance = new Cache_eAccelerator($prefix);
					break;

				case 'xcache':
					require('cache_xcache.php');
					self::$instance = new Cache_XCache($prefix);
					break;

				case 'file':
					require('cache_file.php');
					self::$instance = new Cache_File($prefix, CACHE);
					break;

				case 'auto':
					self::$instance = self::getInstance(self::detect(), $prefix);
					break;

				default:
					self::$instance = new Cache();
					break;
			}
		}

		return self::$instance;
	}

	static public function detect()
	{
		if(function_exists('apc_fetch'))
			return 'apc';
		else if(function_exists('eaccelerator_get'))
			return 'eaccelerator';
		else if(function_exists('xcache_get') && ini_get('xcache.var_size'))
			return 'xcache';

		return 'file';
	}

	public function enabled() {return false;}
}
?>
