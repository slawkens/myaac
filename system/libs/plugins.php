<?php
/**
 * Plugins class
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

spl_autoload_register(function ($class) {
	// project-specific namespace prefix
	$prefix = 'Composer\\Semver\\';

	// base directory for the namespace prefix
	$base_dir = LIBS . '/semver/';

	// does the class use the namespace prefix?
	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) {
		// no, move to the next registered autoloader
		return;
	}

	// get the relative class name
	$relative_class = substr($class, $len);

	// replace the namespace prefix with the base directory, replace namespace
	// separators with directory separators in the relative class name, append
	// with .php
	$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

	// if the file exists, require it
	if (file_exists($file)) {
		require $file;
	}
});

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
								if(!self::satisfies(MYAAC_VERSION, $require_myaac)) {
									self::$error = "Your AAC version doesn't meet the requirement of this plugin. Required version is: " . $require_myaac . ", and you're using version " . MYAAC_VERSION . ".";
									$continue = false;
								}
							}
							
							if(isset($require['php'])) {
								$require_php = $require['php'];
								if(!self::satisfies(phpversion(), $require_php)) {
									self::$error = "Your PHP version doesn't meet the requirement of this plugin. Required version is: " . $require_php . ", and you're using version " . phpversion() . ".";
									$continue = false;
								}
							}
							
							if(isset($require['database'])) {
								$require_database = $require['database'];
								if(!self::satisfies(DATABASE_VERSION, $require_database)) {
									self::$error = "Your database version doesn't meet the requirement of this plugin. Required version is: " . $require_database . ", and you're using version " . DATABASE_VERSION . ".";
									$continue = false;
								}
							}
							
							foreach($require as $req => $version) {
								if(in_array($req, array('myaac', 'php', 'database'))) {
									continue;
								}
								
								if(!self::is_installed($req, $version)) {
									self::$error = "This plugin requires another plugin to run correctly. The another plugin is: " . $req . ", with version " . $version . ".";
									$continue = false;
									break;
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
											$db->insert(TABLE_PREFIX . 'hooks', array('id' => null, 'name' => $_name, 'type' => $hook, 'file' => $info['file']));
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
	
	public static function uninstall($plugin_name) {
		global $cache, $db;
		
		$filename = BASE . 'plugins/' . $plugin_name . '.json';
		if(!file_exists($filename)) {
			self::$error = 'Plugin ' . $plugin_name . ' does not exist.';
			return false;
		}
		else {
			$string = file_get_contents($filename);
			$plugin_info = json_decode($string, true);
			if($plugin_info == false) {
				self::$error = 'Cannot load plugin info ' . $plugin_name . '.json';
				return false;
			}
			else {
				if(!isset($plugin_info['uninstall'])) {
					self::$error = "Plugin doesn't have uninstall options defined. Skipping...";
					return false;
				}
				else {
					$success = true;
					foreach($plugin_info['uninstall'] as $file) {
						$file = BASE . $file;
						if(!deleteDirectory($file)) {
							$success = false;
						}
					}
					
					if (isset($plugin_info['hooks'])) {
						foreach ($plugin_info['hooks'] as $_name => $info) {
							if (defined('HOOK_'. $info['type'])) {
								$hook = constant('HOOK_'. $info['type']);
								$query = $db->query('SELECT `id` FROM `' . TABLE_PREFIX . 'hooks` WHERE `name` = ' . $db->quote($_name) . ';');
								if ($query->rowCount() == 1) { // found something
									$query = $query->fetch();
									$db->delete(TABLE_PREFIX . 'hooks', array('id' => (int)$query['id']));
								}
							} else
								self::$warnings[] = 'Unknown event type: ' . $info['type'];
						}
					}
					
					if($success) {
						if($cache->enabled()) {
							$cache->delete('templates');
						}
						
						return true;
					}
					else {
						self::$error = error_get_last();
					}
				}
			}
		}
		
		return false;
	}
	
	public static function is_installed($plugin_name, $version) {
		$filename = BASE . 'plugins/' . $plugin_name . '.json';
		if(!file_exists($filename)) {
			return false;
		}

		$string = file_get_contents($filename);
		$plugin_info = json_decode($string, true);
		if($plugin_info == false) {
			return false;
		}

		if(!isset($plugin_info['version'])) {
			return false;
		}

		return self::satisfies($plugin_info['version'], $version);
	}

	public static function satisfies($version, $constraints) {
		$is_semver = false;
		$array = array(',', '>', '<', '=', '*', '|', '~');
		foreach($array as $x) {
			if(strpos($constraints, $x) !== false) {
				$is_semver = true;
			}
		}
		
		if($is_semver && !Composer\Semver\Semver::satisfies($version, $constraints)) {
			return false;
		}
		else if(version_compare($version, $constraints, '<')) {
			return false;
		}
		
		return true;
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