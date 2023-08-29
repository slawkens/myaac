<?php

use MyAAC\Models\Settings as ModelsSettings;

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
	static private $instance;
	private $settingsFile = [];
	private $settingsDatabase = [];
	private $cache = [];
	private $valuesAsked = [];
	private $errors = [];

	/**
	 * @return Settings
	 */
	public static function getInstance(): Settings
	{
		if (!self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function load()
	{
		$cache = Cache::getInstance();
		if ($cache->enabled()) {
			$tmp = '';
			if ($cache->fetch('settings', $tmp)) {
				$this->settingsDatabase = unserialize($tmp);
				return;
			}
		}

		$settings = ModelsSettings::all();
		foreach ($settings as $setting)
		{
			$this->settingsDatabase[$setting->name][$setting->key] = $setting->value;
		}

		if ($cache->enabled()) {
			$cache->set('settings', serialize($this->settingsDatabase), 600);
		}
	}

	public function save($pluginName, $values) {
		if (!isset($this->settingsFile[$pluginName])) {
			throw new RuntimeException('Error on save settings: plugin does not exist');
		}

		$settings = $this->settingsFile[$pluginName];

		global $hooks;
		if (!$hooks->trigger(HOOK_ADMIN_SETTINGS_BEFORE_SAVE, [
			'name' => $pluginName,
			'values' => $values,
			'settings' => $settings,
		])) {
			return false;
		}

		if (isset($settings['callbacks']['beforeSave'])) {
			if (!$settings['callbacks']['beforeSave']($settings, $values)) {
				return false;
			}
		}

		$this->errors = [];
		ModelsSettings::where('name', $pluginName)->delete();
		foreach ($values as $key => $value) {
			$errorMessage = '';
			if (isset($settings['settings'][$key]['callbacks']['beforeSave']) && !$settings['settings'][$key]['callbacks']['beforeSave']($key, $value, $errorMessage)) {
				$this->errors[] = $errorMessage;
				continue;
			}

			try {
				ModelsSettings::create([
					'name' => $pluginName,
					'key' => $key,
					'value' => $value
				]);
			} catch (PDOException $error) {
				$this->errors[] = 'Error while saving setting (' . $pluginName . ' - ' . $key . '): ' . $error->getMessage();
			}
		}

		$cache = Cache::getInstance();
		if ($cache->enabled()) {
			$cache->delete('settings');
		}

		return true;
	}

	public function updateInDatabase($pluginName, $key, $value)
	{
		ModelsSettings::where(['name' => $pluginName, 'key' => $key])->update(['value' => $value]);
	}

	public function deleteFromDatabase($pluginName, $key = null)
	{
		if (!isset($key)) {
			ModelsSettings::where('name', $pluginName)->delete();
		}
		else {
			ModelsSettings::where('name', $pluginName)->where('key', $key)->delete();
		}
	}

	public static function display($plugin, $settings): array
	{
		$settingsDb = ModelsSettings::where('name', $plugin)->pluck('value', 'key')->toArray();
		$config = [];
		require BASE . 'config.local.php';

		foreach ($config as $key => $value) {
			if (is_bool($value)) {
				$settingsDb[$key] = $value ? 'true' : 'false';
			}
			else {
				$settingsDb[$key] = (string)$value;
			}
		}

		$javascript = '';
		ob_start();
		?>
		<ul class="nav nav-tabs" id="myTab">
			<?php
			$i = 0;
			foreach($settings as $setting) {
				if (isset($setting['script'])) {
					$javascript .= $setting['script'] . PHP_EOL;
				}

				if ($setting['type'] === 'category') {
					?>
					<li class="nav-item">
						<a class="nav-link<?= ($i === 0 ? ' active' : ''); ?>" id="home-tab-<?= $i++; ?>" data-toggle="tab" href="#tab-<?= str_replace(' ', '', $setting['title']); ?>" type="button"><?= $setting['title']; ?></a>
					</li>
					<?php
				}
			}
			?>
		</ul>
		<div class="tab-content" id="tab-content">
			<?php

			$checkbox = function ($key, $type, $value) {
				echo '<label><input type="radio" id="' . $key . '_' . ($type ? 'yes' : 'no') . '" name="settings[' . $key . ']" value="' . ($type ? 'true' : 'false') . '" ' . ($value === $type ? 'checked' : '') . '/>' . ($type ? 'Yes' : 'No') . '</label> ';
			};

			$i = 0;
			$j = 0;
			foreach($settings as $key => $setting) {
				if ($setting['type'] === 'category') {
					if ($j++ !== 0) { // close previous category
						echo '</tbody></table></div>';
					}
				?>
				<div class="tab-pane fade show<?= ($j === 1 ? ' active' : ''); ?>" id="tab-<?= str_replace(' ', '', $setting['title']); ?>">
					<?php
					continue;
				}

				if ($setting['type'] === 'section') {
					if ($i++ !== 0) { // close previous section
						echo '</tbody></table>';
					}
					?>
					<h3 id="row_<?= $key ?>" style="text-align: center"><strong><?= $setting['title']; ?></strong></h3>
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th style="width: 13%">Name</th>
								<th style="width: 30%">Value</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
						<?php
					continue;
				}

				if (!isset($setting['hidden']) || !$setting['hidden']) {
				?>
					<tr id="row_<?= $key ?>">
						<td><label for="<?= $key ?>" class="control-label"><?= $setting['name'] ?></label></td>
						<td>
							<?php
				}
				if (isset($setting['hidden']) && $setting['hidden']) {
					$value = '';
					if ($setting['type'] === 'boolean') {
						$value = ($setting['default'] ? 'true' : 'false');
					}
					else if (in_array($setting['type'], ['text', 'number', 'email', 'password', 'textarea'])) {
						$value = $setting['default'];
					}
					else if ($setting['type'] === 'options') {
						$value = $setting['options'][$setting['default']];
					}

					echo '<input type="hidden" name="settings[' . $key . ']" value="' . $value . '" id="' . $key . '"';
				}
				else if ($setting['type'] === 'boolean') {
					if(isset($settingsDb[$key])) {
						if($settingsDb[$key] === 'true') {
							$value = true;
						}
						else {
							$value = false;
						}
					}
					else {
						$value = ($setting['default'] ?? false);
					}

					$checkbox($key, true, $value);
					$checkbox($key, false, $value);
				}

				else if (in_array($setting['type'], ['text', 'number', 'email', 'password'])) {
					if ($setting['type'] === 'number') {
						$min = (isset($setting['min']) ? ' min="' . $setting['min'] . '"' : '');
						$max = (isset($setting['max']) ? ' max="' . $setting['max'] . '"' : '');
						$step = (isset($setting['step']) ? ' step="' . $setting['step'] . '"' : '');
					}
					else {
						$min = $max = $step = '';
					}

					echo '<input class="form-control" type="' . $setting['type'] . '" name="settings[' . $key . ']" value="' . ($settingsDb[$key] ?? ($setting['default'] ?? '')) . '" id="' . $key . '"' . $min . $max . $step . '/>';
				}

				else if($setting['type'] === 'textarea') {
					$value = ($settingsDb[$key] ?? ($setting['default'] ?? ''));
					$valueWithSpaces = array_map('trim', preg_split('/\r\n|\r|\n/', trim($value)));
					$rows = count($valueWithSpaces);
					if ($rows < 2) {
						$rows = 2; // always min 2 rows for textarea
					}
					echo '<textarea class="form-control" rows="' . $rows . '" name="settings[' . $key . ']" id="' . $key . '">' . $value . '</textarea>';
				}

				else if ($setting['type'] === 'options') {
					if ($setting['options'] === '$templates') {
						$templates = [];
						foreach (get_templates() as $value) {
							$templates[$value] = $value;
						}

						$setting['options'] = $templates;
					}

					else if($setting['options'] === '$clients') {
						$clients = [];
						foreach((array)config('clients') as $client) {

							$client_version = (string)($client / 100);
							if(strpos($client_version, '.') === false)
								$client_version .= '.0';

							$clients[$client] = $client_version;
						}

						$setting['options'] = $clients;
					}
					else if ($setting['options'] == '$timezones') {
						$timezones = [];
						foreach (DateTimeZone::listIdentifiers() as $value) {
							$timezones[$value] = $value;
						}

						$setting['options'] = $timezones;
					}

					else {
						if (is_string($setting['options'])) {
							$setting['options'] = explode(',', $setting['options']);
							foreach ($setting['options'] as &$option) {
								$option = trim($option);
							}
						}
					}

					echo '<select class="form-control" name="settings[' . $key . ']" id="' . $key . '">';
					foreach ($setting['options'] as $value => $option) {
						$compareTo = ($settingsDb[$key] ?? ($setting['default'] ?? ''));
						if($value === 'true') {
							$selected = $compareTo === true;
						}
						else if($value === 'false') {
							$selected = $compareTo === false;
						}
						else {
							$selected = $compareTo == $value;
						}

						echo '<option value="' . $value . '" ' . ($selected ? 'selected' : '') . '>' . $option . '</option>';
					}
					echo '</select>';
				}

				if (!isset($setting['hidden']) || !$setting['hidden']) {
				?>
						</td>
						<td>
							<div class="well setting-default"><?php
								echo ($setting['desc'] ?? '');
								echo '<br/>';
								echo '<strong>Default:</strong> ';

								if ($setting['type'] === 'boolean') {
									echo ($setting['default'] ? 'Yes' : 'No');
								}
								else if (in_array($setting['type'], ['text', 'number', 'email', 'password', 'textarea'])) {
									echo $setting['default'];
								}
								else if ($setting['type'] === 'options') {
									if (!empty($setting['default'])) {
										echo $setting['options'][$setting['default']];
									}
								}
								?></div>
						</td>
					</tr>
					<?php
				}
			}
					?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="box-footer">
			<button name="save" type="submit" class="btn btn-primary">Save</button>
		</div>
		<?php

		return ['content' => ob_get_clean(), 'script' => $javascript];
	}

	#[\ReturnTypeWillChange]
	public function offsetSet($offset, $value)
	{
		if (is_null($offset)) {
			throw new \RuntimeException("Settings: You cannot set empty offset with value: $value!");
		}

		$this->loadPlugin($offset);

		$pluginKeyName = $this->valuesAsked['pluginKeyName'];
		$key = $this->valuesAsked['key'];

		// remove whole plugin settings
		if (!isset($value)) {
			$this->offsetUnset($offset);
			$this->deleteFromDatabase($pluginKeyName, $key);
			return;
		}

		$this->settingsDatabase[$pluginKeyName][$key] = $value;
		$this->updateInDatabase($pluginKeyName, $key, $value);
	}

	#[\ReturnTypeWillChange]
	public function offsetExists($offset): bool
	{
		$this->loadPlugin($offset);

		$pluginKeyName = $this->valuesAsked['pluginKeyName'];
		$key = $this->valuesAsked['key'];

		// remove specified plugin settings (all)
		if(is_null($key)) {
			return isset($this->settingsDatabase[$offset]);
		}

		return isset($this->settingsDatabase[$pluginKeyName][$key]);
	}

	#[\ReturnTypeWillChange]
	public function offsetUnset($offset)
	{
		$this->loadPlugin($offset);

		$pluginKeyName = $this->valuesAsked['pluginKeyName'];
		$key = $this->valuesAsked['key'];

		if (isset($this->cache[$offset])) {
			unset($this->cache[$offset]);
		}

		// remove specified plugin settings (all)
		if(!isset($key)) {
			unset($this->settingsFile[$pluginKeyName]);
			unset($this->settingsDatabase[$pluginKeyName]);
			$this->deleteFromDatabase($pluginKeyName);
			return;
		}

		unset($this->settingsFile[$pluginKeyName]['settings'][$key]);
		unset($this->settingsDatabase[$pluginKeyName][$key]);
		$this->deleteFromDatabase($pluginKeyName, $key);
	}

	/**
	 * Get settings
	 * Usage: $setting['plugin_name.key']
	 * Example: $settings['shop_system.paypal_email']
	 *
	 * @param mixed $offset
	 * @return array|mixed
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet($offset)
	{
		// try cache hit
		if(isset($this->cache[$offset])) {
			return $this->cache[$offset];
		}

		$this->loadPlugin($offset);

		$pluginKeyName = $this->valuesAsked['pluginKeyName'];
		$key = $this->valuesAsked['key'];

		// return specified plugin settings (all)
		if(!isset($key)) {
			if (!isset($this->settingsFile[$pluginKeyName]['settings'])) {
				throw new RuntimeException('Unknown plugin settings: ' . $pluginKeyName);
			}
			return $this->settingsFile[$pluginKeyName]['settings'];
		}

		$ret = [];
		if(isset($this->settingsFile[$pluginKeyName]['settings'][$key])) {
			$ret = $this->settingsFile[$pluginKeyName]['settings'][$key];
		}

		if(isset($this->settingsDatabase[$pluginKeyName][$key])) {
			$value = $this->settingsDatabase[$pluginKeyName][$key];

			$ret['value'] = $value;
		}
		else {
			$ret['value'] = $this->settingsFile[$pluginKeyName]['settings'][$key]['default'];
		}

		if(isset($ret['type'])) {
			switch($ret['type']) {
				case 'boolean':
					$ret['value'] = getBoolean($ret['value']);
					break;

				case 'number':
					if (!isset($ret['step']) || (int)$ret['step'] == 1) {
						$ret['value'] = (int)$ret['value'];
					}
					break;

				default:
					break;
			}
		}

		if (isset($ret['callbacks']['get'])) {
			$ret['value'] = $ret['callbacks']['get']($ret['value']);
		}

		$this->cache[$offset] = $ret;
		return $ret;
	}

	private function updateValuesAsked($offset)
	{
		$pluginKeyName = $offset;
		if (strpos($offset, '.')) {
			$explode = explode('.', $offset, 2);

			$pluginKeyName = $explode[0];
			$key = $explode[1];

			$this->valuesAsked = ['pluginKeyName' => $pluginKeyName, 'key' => $key];
		}
		else {
			$this->valuesAsked = ['pluginKeyName' => $pluginKeyName, 'key' => null];
		}
	}

	private function loadPlugin($offset)
	{
		$this->updateValuesAsked($offset);

		$pluginKeyName = $this->valuesAsked['pluginKeyName'];
		$key = $this->valuesAsked['key'];

		if (!isset($this->settingsFile[$pluginKeyName])) {
			if ($pluginKeyName === 'core') {
				$settingsFilePath = SYSTEM . 'settings.php';
			} else {
				//$pluginSettings = Plugins::getPluginSettings($pluginKeyName);
				$settings = Plugins::getAllPluginsSettings();
				if (!isset($settings[$pluginKeyName])) {
					warning("Setting $pluginKeyName does not exist or does not have settings defined.");
					return;
				}

				$settingsFilePath = BASE . $settings[$pluginKeyName]['settingsFilename'];
			}

			if (!file_exists($settingsFilePath)) {
				throw new \RuntimeException('Failed to load settings file for plugin: ' . $pluginKeyName);
			}

			$this->settingsFile[$pluginKeyName] = require $settingsFilePath;
		}
	}

	public static function saveConfig($config, $filename, &$content = '')
	{
		$content = "<?php" . PHP_EOL .
			"\$config['installed'] = true;" . PHP_EOL;

		foreach ($config as $key => $value) {
			$content .= "\$config['$key'] = ";
			$content .= var_export($value, true);
			$content .= ';' . PHP_EOL;
		}

		$success = file_put_contents($filename, $content);

		// we saved new config.php, need to revalidate cache (only if opcache is enabled)
		if (function_exists('opcache_invalidate')) {
			opcache_invalidate($filename);
		}

		return $success;
	}

	public static function testDatabaseConnection($config): bool
	{
		$user = null;
		$password = null;
		$dns = [];

		if( isset($config['database_name']) ) {
			$dns[] = 'dbname=' . $config['database_name'];
		}

		if( isset($config['database_user']) ) {
			$user = $config['database_user'];
		}

		if( isset($config['database_password']) ) {
			$password = $config['database_password'];
		}

		if( isset($config['database_host']) ) {
			$dns[] = 'host=' . $config['database_host'];
		}

		if( isset($config['database_port']) ) {
			$dns[] = 'port=' . $config['database_port'];
		}

		try {
			$connectionTest = new PDO('mysql:' . implode(';', $dns), $user, $password);
			$connectionTest->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $error) {
			error('MySQL connection failed. Settings has been reverted.');
			error($error->getMessage());
			return false;
		}

		return true;
	}

	public function getErrors() {
		return $this->errors;
	}
}
