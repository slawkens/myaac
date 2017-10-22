<?php
/**
 * Plugins class
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.6
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

class Plugins {
	private static $warnings = array();
	private static $error = null;
	private static $pluginInfo = array();
	
	public static function install($file) {
		global $db, $cache;
		
		$zip = new ZipArchive();
		if($zip->open($file)) {
			for ($i = 0; $i < $zip->numFiles; $i++) {
				$tmp = $zip->getNameIndex($i);
				if(pathinfo($tmp, PATHINFO_DIRNAME) == 'plugins' && pathinfo($tmp, PATHINFO_EXTENSION) == 'json')
					$json_file = $tmp;
			}
			
			if(!isset($json_file)) {
				self::$error = 'Cannot find plugin info .json file. Installation is discontinued.';
				return false;
			}
			
			if($zip->extractTo(BASE)) { // place in the directory with same name
				$file_name = BASE . $json_file;
				if(!file_exists($file_name)) {
					self::$error = "Cannot load " . $file_name . ". File doesn't exist.";
					return false;
				}
				else {
					$string = file_get_contents($file_name);
					$plugin = json_decode($string, true);
					self::$pluginInfo = $plugin;
					if ($plugin == null) {
						self::$warnings[] = 'Cannot load ' . $file_name . '. File might be not a valid json code.';
					}
					else {
						$continue = true;
						
						if(!isset($plugin['name'])) {
							self::$warnings[] = 'Plugin "name" tag is not set.';
						}
						if(!isset($plugin['description'])) {
							self::$warnings[] = 'Plugin "description" tag is not set.';
						}
						if(!isset($plugin['version'])) {
							self::$warnings[] = 'Plugin "version" tag is not set.';
						}
						if(!isset($plugin['author'])) {
							self::$warnings[] = 'Plugin "author" tag is not set.';
						}
						if(!isset($plugin['contact'])) {
							self::$warnings[] = 'Plugin "contact" tag is not set.';
						}
						
						if(isset($plugin['require'])) {
							$require = $plugin['require'];
							if(isset($require['myaac'])) {
								$require_myaac = $require['myaac'];
								if(version_compare(MYAAC_VERSION, $require_myaac, '<')) {
									self::$warnings[] = "This plugin requires MyAAC version " . $require_myaac . ", you're using version " . MYAAC_VERSION . " - please update.";
									$continue = false;
								}
							}
							
							if(isset($require['php'])) {
								$require_php = $require['php'];
								if(version_compare(phpversion(), $require_php, '<')) {
									self::$warnings[] = "This plugin requires PHP version " . $require_php . ", you're using version " . phpversion() . " - please update.";
									$continue = false;
								}
							}
							
							if(isset($require['database'])) {
								$require_database = $require['database'];
								if($require_database < DATABASE_VERSION) {
									self::$warnings[] = "This plugin requires database version " . $require_database . ", you're using version " . DATABASE_VERSION . " - please update.";
									$continue = false;
								}
							}
						}
						
						if($continue) {
							if (isset($plugin['install'])) {
								if (file_exists(BASE . $plugin['install']))
									require(BASE . $plugin['install']);
								else
									self::$warnings[] = 'Cannot load install script. Your plugin might be not working correctly.';
							}
							
							if (isset($plugin['hooks'])) {
								foreach ($plugin['hooks'] as $_name => $info) {
									if (defined('HOOK_'. $info['type'])) {
										$hook = constant('HOOK_'. $info['type']);
										$query = $db->query('SELECT `id` FROM `' . TABLE_PREFIX . 'hooks` WHERE `name` = ' . $db->quote($_name) . ';');
										if ($query->rowCount() == 1) { // found something
											$query = $query->fetch();
											$db->update(TABLE_PREFIX . 'hooks', array('type' => $hook, 'file' => $info['file']), array('id' => (int)$query['id']));
										} else {
											$db->insert(TABLE_PREFIX . 'hooks', array('id' => 'NULL', 'name' => $_name, 'type' => $hook, 'file' => $info['file']));
										}
									} else
										self::$warnings[] = 'Unknown event type: ' . $info['type'];
								}
							}
							
							if($cache->enabled()) {
								$cache->delete('templates');
							}
							
							$zip->close();
							return true;
						}
					}
				}
			}
			else {
				self::$error = 'There was a problem with extracting zip archive.';
			}
			
			$zip->close();
		}
		else {
			self::$error = 'There was a problem with opening zip archive.';
		}
		
		return false;
	}
	
	public static function getWarnings() {
		return self::$warnings;
	}
	
	public static function getError() {
		return self::$error;
	}
	
	public static function getPluginInfo() {
		return self::$pluginInfo;
	}
}