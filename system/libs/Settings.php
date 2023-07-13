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
	static private $instance;
	private $settingsArray = [];
	private $settings = [];
	private $cache = [];
	private $valuesAsked = [];

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
				$this->settings = unserialize($tmp);
				return;
			}
		}

		global $db;
		$settings = $db->query('SELECT * FROM `' . TABLE_PREFIX . 'settings`');

		if($settings->rowCount() > 0) {
			foreach ($settings->fetchAll(PDO::FETCH_ASSOC) as $setting) {
				$this->settings[$setting['name']][$setting['key']] = $setting['value'];
			}
		}

		if ($cache->enabled()) {
			$cache->set('settings', serialize($this->settings), 600);
		}
	}

	public function updateInDatabase($pluginName, $key, $value)
	{
		global $db;
		$db->update(TABLE_PREFIX . 'settings', ['value' => $value], ['name' => $pluginName, 'key' => $key]);
	}

	public function deleteFromDatabase($pluginName, $key = null)
	{
		global $db;

		if (!isset($key)) {
			$db->delete(TABLE_PREFIX . 'settings', ['name' => $pluginName], -1);
		}
		else {
			$db->delete(TABLE_PREFIX . 'settings', ['name' => $pluginName, 'key' => $key]);
		}
	}

	public static function display($plugin, $settings): array
	{
		global $db;

		$query = 'SELECT `key`, `value` FROM `' . TABLE_PREFIX . 'settings` WHERE `name` = ' . $db->quote($plugin) . ';';
		$query = $db->query($query);

		$settingsDb = [];
		if($query->rowCount() > 0) {
			foreach($query->fetchAll(PDO::FETCH_ASSOC) as $value) {
				$settingsDb[$value['key']] = $value['value'];
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
					echo '<textarea class="form-control" name="settings[' . $key . ']" id="' . $key . '">' . ($settingsDb[$key] ?? ($setting['default'] ?? '')) . '</textarea>';
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
						$compareTo = (isset($settingsDb[$key]) ? $settingsDb[$key] : (isset($setting['default']) ? $setting['default'] : ''));
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
							<div class="well">
								<?php
								echo $setting['desc'];
								echo '<br/>';
								echo '<strong>Default:</strong> ';
								if ($setting['type'] === 'boolean') {
									echo ($setting['default'] ? 'Yes' : 'No');
								}
								else if (in_array($setting['type'], ['text', 'number', 'email', 'password', 'textarea'])) {
									echo $setting['default'];
								}
								else if ($setting['type'] === 'options') {
									echo $setting['options'][$setting['default']];
								}
								?>
							</div>
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

		$this->settings[$pluginKeyName][$key] = $value;
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
			return isset($this->settings[$offset]);
		}

		return isset($this->settings[$pluginKeyName][$key]);
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
			unset($this->settingsArray[$pluginKeyName]);
			unset($this->settings[$pluginKeyName]);
			$this->deleteFromDatabase($pluginKeyName);
			return;
		}

		unset($this->settingsArray[$pluginKeyName][$key]);
		unset($this->settings[$pluginKeyName][$key]);
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
			return $this->settingsArray[$pluginKeyName];
		}

		$ret = [];
		if(isset($this->settingsArray[$pluginKeyName][$key])) {
			$ret = $this->settingsArray[$pluginKeyName][$key];
		}

		if(isset($this->settings[$pluginKeyName][$key])) {
			$value = $this->settings[$pluginKeyName][$key];

			$ret['value'] = $value;
		}
		else {
			$ret['value'] = $this->settingsArray[$pluginKeyName][$key]['default'];
		}

		if(isset($ret['type'])) {
			switch($ret['type']) {
				case 'boolean':
					$ret['value'] = getBoolean($ret['value']);
					break;

				case 'number':
					$ret['value'] = (int)$ret['value'];
					break;

				default:
					break;
			}
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

		if (!isset($this->settingsArray[$pluginKeyName])) {
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

			$tmp = require $settingsFilePath;
			$this->settingsArray[$pluginKeyName] = $tmp['settings'];
		}
	}
}
