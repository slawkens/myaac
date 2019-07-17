<?php
/**
 * Plugins class
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
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

function is_sub_dir($path = NULL, $parent_folder = SITE_PATH) {

	//Get directory path minus last folder
	$dir = dirname($path);
	$folder = substr($path, strlen($dir));

	//Check the the base dir is valid
	$dir = realpath($dir);

	//Only allow valid filename characters
	$folder = preg_replace('/[^a-z0-9\.\-_]/i', '', $folder);

	//If this is a bad path or a bad end folder name
	if( !$dir OR !$folder OR $folder === '.') {
		return FALSE;
	}

	//Rebuild path
	$path = $dir. '/' . $folder;

	//If this path is higher than the parent folder
	if( strcasecmp($path, $parent_folder) > 0 ) {
		return $path;
	}

	return FALSE;
}

use Composer\Semver\Semver;

class Plugins {
	private static $warnings = array();
	private static $error = null;
	private static $plugin = array();

	public static function install($file) {
		global $db;

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
					$string = Plugins::removeComments($string);
					$plugin = json_decode($string, true);
					self::$plugin = $plugin;
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

							$myaac_satified = true;
							if(isset($require['myaac_'])) {
								$require_myaac = $require['myaac_'];
								if(!Semver::satisfies(MYAAC_VERSION, $require_myaac)) {
									$myaac_satified = false;
								}
							}
							else if(isset($require['myaac'])) {
								$require_myaac = $require['myaac'];
								if(version_compare(MYAAC_VERSION, $require_myaac, '<')) {
									$myaac_satified = false;
								}
							}

							if(!$myaac_satified) {
								self::$error = "Your AAC version doesn't meet the requirement of this plugin. Required version is: " . $require_myaac . ", and you're using version " . MYAAC_VERSION . ".";
								$continue = false;
							}

							$php_satified = true;
							if(isset($require['php_'])) {
								$require_php = $require['php_'];
								if(!Semver::satisfies(phpversion(), $require_php)) {
									$php_satified = false;
								}
							}
							else if(isset($require['php'])) {
								$require_php = $require['php'];
								if(version_compare(phpversion(), $require_php, '<')) {
									$php_satified = false;
								}
							}

							if(!$php_satified) {
								self::$error = "Your PHP version doesn't meet the requirement of this plugin. Required version is: " . $require_php . ", and you're using version " . phpversion() . ".";
								$continue = false;
							}

							$database_satified = true;
							if(isset($require['database_'])) {
								$require_database = $require['database_'];
								if(!Semver::satisfies(DATABASE_VERSION, $require_database)) {
									$database_satified = false;
								}
							}
							else if(isset($require['database'])) {
								$require_database = $require['database'];
								if(version_compare(DATABASE_VERSION, $require_database, '<')) {
									$database_satified = false;
								}
							}

							if(!$database_satified) {
								self::$error = "Your database version doesn't meet the requirement of this plugin. Required version is: " . $require_database . ", and you're using version " . DATABASE_VERSION . ".";
								$continue = false;
							}

							if($continue) {
								foreach($require as $req => $version) {
									if(in_array($req, array('myaac', 'myaac_', 'php', 'php_', 'database', 'database_'))) {
										continue;
									}

									$req = strtolower($req);
									if(in_array($req, array('php-ext', 'php-extension'))) { // require php extension
										if(!extension_loaded($version)) {
											self::$error = "This plugin requires php extension: " . $version . " to be installed.";
											$continue = false;
											break;
										}
									}
									else if($req == 'table') {
										if(!$db->hasTable($version)) {
											self::$error = "This plugin requires table: " . $version . " to exist in the database.";
											$continue = false;
											break;
										}
									}
									else if($req == 'column') {
										$tmp = explode('.', $version);
										if(count($tmp) == 2) {
											if(!$db->hasColumn($tmp[0], $tmp[1])) {
												self::$error = "This plugin requires database column: " . $tmp[0] . "." . $tmp[1] . " to exist in database.";
												$continue = false;
												break;
											}
										}
									}
									else if(strpos($req, 'ext-') !== false) {
										$tmp = explode('-', $req);
										if(count($tmp) == 2) {
											if(!extension_loaded($tmp[1]) || !Semver::satisfies(phpversion($tmp[1]), $version)) {
												self::$error = "This plugin requires php extension: " . $tmp[1] . ", version " . $version . " to be installed.";
												$continue = false;
												break;
											}
										}
									}
									else if(!self::is_installed($req, $version)) {
										self::$error = "This plugin requires another plugin to run correctly. The another plugin is: " . $req . ", with version " . $version . ".";
										$continue = false;
										break;
									}
								}
							}
						}

						if($continue) {
							if (isset($plugin['install'])) {
								if (file_exists(BASE . $plugin['install'])) {
									$db->revalidateCache();
									require BASE . $plugin['install'];
									$db->revalidateCache();
								}
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

							$cache = Cache::getInstance();
							if($cache->enabled()) {
								$cache->delete('templates');
								$cache->delete('hooks');
								$cache->delete('template_menus');
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
		global $db;

		$filename = BASE . 'plugins/' . $plugin_name . '.json';
		if(!file_exists($filename)) {
			self::$error = 'Plugin ' . $plugin_name . ' does not exist.';
			return false;
		}
		else {
			$string = file_get_contents($filename);
			$string = self::removeComments($string);
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
						if(strpos($file, '/') === 0) {
							$success = false;
							self::$error = "You cannot use absolute paths (starting with slash - '/'): " . $file;
							break;
						}

						$file = str_replace('\\', '/', BASE . $file);
						$realpath = str_replace('\\', '/', realpath(dirname($file)));
						if(!is_sub_dir($file, BASE) || $realpath != dirname($file)) {
							$success = false;
							self::$error = "You don't have rights to delete: " . $file;
							break;
						}
					}

					if($success) {
						foreach($plugin_info['uninstall'] as $file) {
							if(!deleteDirectory(BASE . $file)) {
								self::$warnings[] = 'Cannot delete: ' . $$file;
							}
						}
					}

					if (isset($plugin_info['hooks'])) {
						foreach ($plugin_info['hooks'] as $_name => $info) {
							if (defined('HOOK_'. $info['type'])) {
								//$hook = constant('HOOK_'. $info['type']);
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
						$cache = Cache::getInstance();
						if($cache->enabled()) {
							$cache->delete('templates');
						}

						return true;
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

		return Semver::satisfies($plugin_info['version'], $version);
	}

	public static function getWarnings() {
		return self::$warnings;
	}

	public static function getError() {
		return self::$error;
	}

	public static function getPlugin() {
		return self::$plugin;
	}

	public static function removeComments($string) {
		$string = preg_replace('!/\*.*?\*/!s', '', $string);
		$string = preg_replace('/\n\s*\n/', "\n", $string);
		//  Removes multi-line comments and does not create
		//  a blank line, also treats white spaces/tabs
		$string = preg_replace('!^[ \t]*/\*.*?\*/[ \t]*[\r\n]!s', '', $string);

		//  Removes single line '//' comments, treats blank characters
		$string = preg_replace('![ \t]*//.*[ \t]*[\r\n]!', '', $string);

		//  Strip blank lines
		$string = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $string);

		return $string;
	}
}