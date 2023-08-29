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

function is_sub_dir($path = NULL, $parent_folder = BASE) {

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
use MyAAC\Models\Menu;

class Plugins {
	private static $warnings = [];
	private static $error = null;
	private static $plugin_json = [];

	public static function getRoutes()
	{
		$cache = Cache::getInstance();
		if ($cache->enabled()) {
			$tmp = '';
			if ($cache->fetch('plugins_routes', $tmp)) {
				return unserialize($tmp);
			}
		}

		$routes = [];
		foreach(self::getAllPluginsJson() as $plugin) {
			$warningPreTitle = 'Plugin: ' . $plugin['name'] . ' - ';

			if (isset($plugin['routes'])) {
				foreach ($plugin['routes'] as $_name => $info) {
					// default method: get
					$method = $info['method'] ?? ['GET'];
					if ($method !== '*') {
						$methods = is_string($method) ? explode(',', $info['method']) : $method;
						foreach ($methods as $method) {
							$method = strtolower($method);
							if (!in_array($method, ['get', 'post', 'put', 'patch', 'delete', 'head'])) {
								self::$warnings[] = $warningPreTitle . 'Not allowed method ' . $method . '... Disabling this route...';
							}
						}
					}
					else {
						$methods = '*'; // all available methods
					}

					if (!isset($info['priority'])) {
						$info['priority'] = 100; // default priority
					}

					if (isset($info['redirect_from'])) {
						removeIfFirstSlash($info['redirect_from']);

						$info['pattern'] = $info['redirect_from'];
						if (!isset($info['redirect_to'])) {
							self::$warnings[] = $warningPreTitle . 'redirect set without "redirect_to".';
						}
						else {
							removeIfFirstSlash($info['redirect_to']);
							$info['file'] = '__redirect__/' . $info['redirect_to'];
						}
					}

					// replace first occurence of / in pattern if found (will be auto-added later)
					removeIfFirstSlash($info['pattern']);

					foreach ($routes as $id => &$route) {
						if($route[1] == $info['pattern']) {
							if($info['priority'] < $route[3]) {
								self::$warnings[] = $warningPreTitle . "Duplicated route with lower priority: {$info['pattern']}. Disabling this route...";
								continue 2;
							}
							else {
								self::$warnings[] = $warningPreTitle . "Duplicated route with lower priority: {$route[1]} ({$route[3]}). Disabling this route...";
								unset($routes[$id]);
							}
						}
					}

					$routes[] = [$methods, $info['pattern'], $info['file'], $info['priority']];
				}
			}
		}
/*
		usort($routes, function ($a, $b)
		{
			// key 3 is priority
			if ($a[3] == $b[3]) {
				return 0;
			}

			return ($a[3] > $b[3]) ? -1 : 1;
		});
*/
		// cleanup before passing back
		// priority is not needed anymore
		foreach ($routes as &$route) {
			unset($route[3]);
		}

		if ($cache->enabled()) {
			$cache->set('plugins_routes', serialize($routes), 600);
		}

		return $routes;
	}

	public static function getHooks()
	{
		$cache = Cache::getInstance();
		if ($cache->enabled()) {
			$tmp = '';
			if ($cache->fetch('plugins_hooks', $tmp)) {
				return unserialize($tmp);
			}
		}

		$hooks = [];
		foreach(self::getAllPluginsJson() as $plugin) {
			if (isset($plugin['hooks'])) {
				foreach ($plugin['hooks'] as $_name => $info) {
					if (str_contains($info['type'], 'HOOK_')) {
						$info['type'] = str_replace('HOOK_', '', $info['type']);
					}

					if (defined('HOOK_'. $info['type'])) {
						$hook = constant('HOOK_'. $info['type']);
						$hooks[] = ['name' => $_name, 'type' => $hook, 'file' => $info['file']];
					} else {
						self::$warnings[] = 'Plugin: ' . $plugin['name'] . '. Unknown event type: ' . $info['type'];
					}
				}
			}
		}

		if ($cache->enabled()) {
			$cache->set('plugins_hooks', serialize($hooks), 600);
		}

		return $hooks;
	}

	public static function getAllPluginsSettings()
	{
		$cache = Cache::getInstance();
		if ($cache->enabled()) {
			$tmp = '';
			if ($cache->fetch('plugins_settings', $tmp)) {
				return unserialize($tmp);
			}
		}

		$settings = [];
		foreach (self::getAllPluginsJson() as $plugin) {
			if (isset($plugin['settings'])) {
				$settingsFile = require BASE . $plugin['settings'];
				if (!isset($settingsFile['key'])) {
					warning("Settings file for plugin - {$plugin['name']} does not contain 'key' field");
					continue;
				}

				$settings[$settingsFile['key']] = ['pluginFilename' => $plugin['filename'], 'settingsFilename' => $plugin['settings']];
			}
		}

		if ($cache->enabled()) {
			$cache->set('plugins_settings', serialize($settings), 600); // cache for 10 minutes
		}

		return $settings;
	}

	public static function getAllPluginsJson($disabled = false)
	{
		$cache = Cache::getInstance();
		if ($cache->enabled()) {
			$tmp = '';
			if ($cache->fetch('plugins', $tmp)) {
				return unserialize($tmp);
			}
		}

		$plugins = [];
		foreach (get_plugins($disabled) as $filename) {
			$plugin = self::getPluginJson($filename);

			if (!$plugin) {
				continue;
			}

			$plugin['filename'] = $filename;
			$plugins[] = $plugin;
		}

		if ($cache->enabled()) {
			$cache->set('plugins', serialize($plugins), 600); // cache for 10 minutes
		}

		return $plugins;
	}

	public static function getPluginSettings($filename)
	{
		$plugin_json = self::getPluginJson($filename);
		if (!$plugin_json) {
			return false;
		}

		if (!isset($plugin_json['settings']) || !file_exists(BASE . $plugin_json['settings'])) {
			return false;
		}

		return $plugin_json['settings'];
	}

	public static function getPluginJson($filename = null)
	{
		if(!isset($filename)) {
			return self::$plugin_json;
		}

		$pathToPlugin = PLUGINS . $filename . '.json';
		if (!file_exists($pathToPlugin)) {
			self::$warnings[] = "Cannot load $filename.json. File doesn't exist.";
			return false;
		}

		$string = file_get_contents($pathToPlugin);
		$plugin_json = json_decode($string, true);
		if ($plugin_json == null) {
			self::$warnings[] = "Cannot load $filename.json. File might be not a valid json code.";
			return false;
		}

		if (isset($plugin_json['enabled']) && !getBoolean($plugin_json['enabled'])) {
			self::$warnings[] = 'Skipping ' . $filename . '... The plugin is disabled.';
			return false;
		}

		return $plugin_json;
	}

	public static function install($file): bool
	{
		global $db;

		if(!\class_exists('ZipArchive')) {
			throw new RuntimeException('Please install PHP zip extension. Plugins upload disabled until then.');
		}

		$zip = new ZipArchive();
		if($zip->open($file) !== true) {
			self::$error = 'There was a problem with opening zip archive.';
			return false;
		}

		for ($i = 0; $i < $zip->numFiles; $i++) {
			$tmp = $zip->getNameIndex($i);
			if(pathinfo($tmp, PATHINFO_DIRNAME) == 'plugins' && pathinfo($tmp, PATHINFO_EXTENSION) == 'json')
				$json_file = $tmp;
		}

		if(!isset($json_file)) {
			self::$error = 'Cannot find plugin info .json file. Installation is discontinued.';
			return false;
		}

		$plugin_temp_dir = CACHE . 'plugins/' . str_replace('.zip', '', basename($file)) . '/';
		if(!$zip->extractTo($plugin_temp_dir)) { // place in cache dir
			self::$error = 'There was a problem with extracting zip archive to cache directory.';
			$zip->close();
			return false;
		}

		self::$error = 'There was a problem with extracting zip archive.';
		$file_name = $plugin_temp_dir . $json_file;
		if(!file_exists($file_name)) {
			self::$error = "Cannot load " . $file_name . ". File doesn't exist.";
			$zip->close();
			return false;
		}

		$pluginFilename = str_replace('.json', '', basename($json_file));
		if (self::existDisabled($pluginFilename)) {
			success('The plugin already existed, but was disabled. It has been enabled again and will be now reinstalled.');
			self::enable($pluginFilename);
		}

		$string = file_get_contents($file_name);
		$plugin_json = json_decode($string, true);
		self::$plugin_json = $plugin_json;
		if ($plugin_json == null) {
			self::$warnings[] = 'Cannot load ' . $file_name . '. File might be not a valid json code.';
		}
		else {
			$continue = true;

			if(!isset($plugin_json['name']) || empty(trim($plugin_json['name']))) {
				self::$warnings[] = 'Plugin "name" tag is not set.';
			}
			if(!isset($plugin_json['description']) || empty(trim($plugin_json['description']))) {
				self::$warnings[] = 'Plugin "description" tag is not set.';
			}
			if(!isset($plugin_json['version']) || empty(trim($plugin_json['version']))) {
				self::$warnings[] = 'Plugin "version" tag is not set.';
			}
			if(!isset($plugin_json['author']) || empty(trim($plugin_json['author']))) {
				self::$warnings[] = 'Plugin "author" tag is not set.';
			}
			if(!isset($plugin_json['contact']) || empty(trim($plugin_json['contact']))) {
				self::$warnings[] = 'Plugin "contact" tag is not set.';
			}

			if(isset($plugin_json['require'])) {
				$require = $plugin_json['require'];

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
					return false;
				}

				$php_satisfied = true;
				if(isset($require['php_'])) {
					$require_php = $require['php_'];
					if(!Semver::satisfies(phpversion(), $require_php)) {
						$php_satisfied = false;
					}
				}
				else if(isset($require['php'])) {
					$require_php = $require['php'];
					if(version_compare(phpversion(), $require_php, '<')) {
						$php_satisfied = false;
					}
				}

				if(!$php_satisfied) {
					self::$error = "Your PHP version doesn't meet the requirement of this plugin. Required version is: " . $require_php . ", and you're using version " . phpversion() . ".";
					$continue = false;
				}

				$database_satisfied = true;
				if(isset($require['database_'])) {
					$require_database = $require['database_'];
					if(!Semver::satisfies(DATABASE_VERSION, $require_database)) {
						$database_satisfied = false;
					}
				}
				else if(isset($require['database'])) {
					$require_database = $require['database'];
					if(version_compare(DATABASE_VERSION, $require_database, '<')) {
						$database_satisfied = false;
					}
				}

				if(!$database_satisfied) {
					self::$error = "Your database version doesn't meet the requirement of this plugin. Required version is: " . $require_database . ", and you're using version " . DATABASE_VERSION . ".";
					$continue = false;
				}

				if($continue) {
					foreach($require as $req => $version) {
						$req = strtolower(trim($req));
						$version = trim($version);

						if(in_array($req, array('myaac', 'myaac_', 'php', 'php_', 'database', 'database_'))) {
							continue;
						}

						if(in_array($req, array('php-ext', 'php-extension'))) { // require php extension
							$tmpDisplayError = false;
							$explode = explode(',', $version);

							foreach ($explode as $item) {
								if(!extension_loaded($item)) {
									$errors[] = "This plugin requires php extension: " . $item . " to be installed.";
									$tmpDisplayError = true;
								}
							}

							if ($tmpDisplayError) {
								self::$error = implode('<br/>', $errors);
								$continue = false;
								break;
							}
						}
						else if($req == 'table') {
							$tmpDisplayError = false;
							$explode = explode(',', $version);
							foreach ($explode as $item) {
								if(!$db->hasTable($item)) {
									$errors[] = "This plugin requires table: " . $item . " to exist in the database.";
									$tmpDisplayError = true;
								}
							}

							if ($tmpDisplayError) {
								self::$error = implode('<br/>', $errors);
								$continue = false;
								break;
							}
						}
						else if($req == 'column') {
							$tmpDisplayError = false;
							$explode = explode(',', $version);
							foreach ($explode as $item) {
								$tmp = explode('.', $item);

								if(count($tmp) == 2) {
									if(!$db->hasColumn($tmp[0], $tmp[1])) {
										$errors[] = "This plugin requires database column: " . $tmp[0] . "." . $tmp[1] . " to exist in database.";
										$tmpDisplayError = true;
									}
								}
								else {
									self::$warnings[] = "Invalid plugin require column: " . $item;
								}
							}

							if ($tmpDisplayError) {
								self::$error = implode('<br/>', $errors);
								$continue = false;
								break;
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
				if(!$zip->extractTo(BASE)) { // "Real" Install
					self::$error = 'There was a problem with extracting zip archive to base directory.';
					$zip->close();
					return false;
				}

				if (isset($plugin_json['install'])) {
					if (file_exists(BASE . $plugin_json['install'])) {
						$db->revalidateCache();
						require BASE . $plugin_json['install'];
						$db->revalidateCache();
					}
					else
						self::$warnings[] = 'Cannot load install script. Your plugin might be not working correctly.';
				}

				$cache = Cache::getInstance();
				if($cache->enabled()) {
					$cache->delete('templates');
					$cache->delete('hooks');
					$cache->delete('template_menus');
				}

				return true;
			}
		}

		return false;
	}

	public static function isEnabled($pluginFileName): bool
	{
		$filenameJson = $pluginFileName . '.json';
		return !is_file(PLUGINS . 'disabled.' . $filenameJson) && is_file(PLUGINS . $filenameJson);
	}

	public static function existDisabled($pluginFileName): bool
	{
		$filenameJson = $pluginFileName . '.json';
		return is_file(PLUGINS . 'disabled.' . $filenameJson);
	}

	public static function enable($pluginFileName): bool {
		return self::enableDisable($pluginFileName, true);
	}

	public static function disable($pluginFileName): bool {
		return self::enableDisable($pluginFileName, false);
	}

	private static function enableDisable($pluginFileName, $enable): bool
	{
		$filenameJson = $pluginFileName . '.json';
		$fileExist = is_file(PLUGINS . ($enable ? 'disabled.' : '') . $filenameJson);
		if (!$fileExist) {
			self::$error = 'Cannot ' . ($enable ? 'enable' : 'disable') . ' plugin: ' . $pluginFileName . '. File does not exist.';
			return false;
		}

		$result = rename(PLUGINS . ($enable ? 'disabled.' : '') . $filenameJson, PLUGINS . ($enable ? '' : 'disabled.') . $filenameJson);
		if (!$result) {
			self::$error = 'Cannot ' . ($enable ? 'enable' : 'disable') . ' plugin: ' . $pluginFileName . '. Permission problem.';
			return false;
		}

		return true;
	}

	public static function uninstall($plugin_name): bool
	{
		$filename = BASE . 'plugins/' . $plugin_name . '.json';
		if(!file_exists($filename)) {
			self::$error = 'Plugin ' . $plugin_name . ' does not exist.';
			return false;
		}
		$string = file_get_contents($filename);
		$plugin_info = json_decode($string, true);
		if(!$plugin_info) {
			self::$error = 'Cannot load plugin info ' . $plugin_name . '.json';
			return false;
		}

		if(!isset($plugin_info['uninstall'])) {
			self::$error = "Plugin doesn't have uninstall options defined. Skipping...";
			return false;
		}

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
					self::$warnings[] = 'Cannot delete: ' . $file;
				}
			}

			$cache = Cache::getInstance();
			if($cache->enabled()) {
				$cache->delete('templates');
				$cache->delete('hooks');
				$cache->delete('template_menus');
			}

			return true;
		}

		return false;
	}

	public static function is_installed($plugin_name, $version): bool
	{
		$filename = BASE . 'plugins/' . $plugin_name . '.json';
		if(!file_exists($filename)) {
			return false;
		}

		$string = file_get_contents($filename);
		$plugin_info = json_decode($string, true);
		if(!$plugin_info) {
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

	public static function clearWarnings() {
		self::$warnings = [];
	}

	public static function getError() {
		return self::$error;
	}

	/**
	 * Install menus
	 * Helper function for plugins
	 *
	 * @param string $templateName
	 * @param array $categories
	 */
	public static function installMenus($templateName, $categories)
	{
		// check if menus already exist
		$menuInstalled = Menu::where('template', $templateName)->select('id')->first();
		if ($menuInstalled) {
			return;
		}

		foreach ($categories as $category => $menus) {
			$i = 0;
			foreach ($menus as $name => $link) {
				$color = '';
				$blank = 0;

				if (is_array($link)) {
					if (isset($link['name'])) {
						$name = $link['name'];
					}
					if (isset($link['color'])) {
						$color = $link['color'];
					}
					if (isset($link['blank'])) {
						$blank = $link['blank'] ? 1 : 0;
					}

					$link = $link['link'];
				}

				$insert_array = [
					'template' => $templateName,
					'name' => $name,
					'link' => $link,
					'category' => $category,
					'ordering' => $i++,
					'blank' => $blank,
					'color' => $color,
				];

				Menu::create($insert_array);
			}
		}
	}
}
