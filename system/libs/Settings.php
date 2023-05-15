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
	private $plugins = [];
	private $settings = [];
	private $cache = [];

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
				$this->settings[$setting['plugin_name']][$setting['key']] = $setting['value'];
			}
		}

		if ($cache->enabled()) {
			$cache->set('settings', serialize($this->settings), 600);
		}
	}

	public static function parse($plugin, $settings): string
	{
		global $db;

		$query = 'SELECT `key`, `value` FROM `' . TABLE_PREFIX . 'settings` WHERE `plugin_name` = ' . $db->quote($plugin) . ';';
		$query = $db->query($query);

		$settingsDb = [];
		if($query->rowCount() > 0) {
			foreach($query->fetchAll(PDO::FETCH_ASSOC) as $value) {
				$settingsDb[$value['key']] = $value['value'];
			}
		}

		ob_start();
		?>
		<ul class="nav nav-tabs" id="myTab">
			<?php
			$i = 0;
			foreach($settings as $setting) {
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
				echo '<label><input type="radio" id="' . $key . '" name="settings[' . $key . ']" value="' . ($type ? 'true' : 'false') . '" ' . ($value === $type ? 'checked' : '') . '/>' . ($type ? 'Yes' : 'No') . '</label> ';
			};

			$i = 0;
			$j = 0;
			foreach($settings as $key => $setting) {
				if ($setting['type'] === 'category') {
					if ($j++ !== 0) {
						echo '</tbody></table></div>';
					}
				?>
				<div class="tab-pane fade show<?= ($j === 1 ? ' active' : ''); ?>" id="tab-<?= str_replace(' ', '', $setting['title']); ?>">
					<?php
					continue;
				}

				if ($setting['type'] === 'section') {
					if ($i++ !== 0) {
						echo '</tbody></table>';
					}
					?>
					<h2 style="text-align: center"><strong><?= $setting['title']; ?></strong></h2>
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
					<tr>
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
					echo '<input class="form-control" type="' . $setting['type'] . '" name="settings[' . $key . ']" value="' . ($settingsDb[$key] ?? ($setting['default'] ?? '')) . '" id="' . $key . '"/>';
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

		return ob_get_clean();
	}

	#[\ReturnTypeWillChange]
	public function offsetSet($offset, $value)
	{
		if (is_null($offset)) {
			throw new \RuntimeException("Settings: You cannot set empty offset with value: $value!");
		}

		$pluginName = $offset;
		if (strpos($offset, '.')) {
			$explode = explode('.', $offset, 2);

			$pluginName = $explode[0];
			$key = $explode[1];
		}

		$this->loadPlugin($pluginName);

		// remove whole plugin settings
		if (!isset($key)) {
			$this->plugins[$pluginName] = [];

			// remove from settings
			if (isset($this->settings[$pluginName])) {
				unset($this->settings[$pluginName]);
			}

			// remove from cache
			if (isset($this->cache[$pluginName])) {
				unset($this->cache[$pluginName]);
			}
			/*foreach ($this->cache as $_key => $value) {
				if (strpos($_key, $pluginName) !== false) {
					unset($this->cache[$_key]);
				}
			}*/
		}

		$this->settings[$pluginName][$key] = $value['value'];
	}

	#[\ReturnTypeWillChange]
	public function offsetExists($offset) {
		return isset($this->settings[$offset]);
	}

	#[\ReturnTypeWillChange]
	public function offsetUnset($offset) {
		unset($this->settings[$offset]);
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

		$pluginName = $offset;
		if (strpos($offset, '.')) {
			$explode = explode('.', $offset, 2);

			$pluginName = $explode[0];
			$key = $explode[1];
		}

		$this->loadPlugin($pluginName);

		// return specified plugin settings (all)
		if(!isset($key)) {
			return $this->plugins[$pluginName];
		}

		$ret = [];
		if(isset($this->plugins[$pluginName][$key])) {
			$ret = $this->plugins[$pluginName][$key];
		}

		if(isset($this->settings[$pluginName][$key])) {
			$value = $this->settings[$pluginName][$key];

			$ret['value'] = $value;
		}
		else {
			$ret['value'] = $this->plugins[$pluginName][$key]['default'];
		}

		if(isset($ret['type'])) {
			switch($ret['type']) {
				case 'boolean':
					$ret['value'] = $ret['value'] === 'true';
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

	private function loadPlugin($pluginName)
	{
		if (!isset($this->plugins[$pluginName])) {
			if ($pluginName === 'core') {
				$settingsFilePath = SYSTEM . 'settings.php';
			} else {
				$pluginSettings = Plugins::getPluginSettings($pluginName);
				if (!$pluginSettings) {
					error('This plugin does not exist or does not have settings defined.');
					return;
				}

				$settingsFilePath = BASE . $pluginSettings;
			}

			if (!file_exists($settingsFilePath)) {
				throw new \RuntimeException('Failed to load settings file for plugin: ' . $pluginName);
			}

			$tmp = require $settingsFilePath;
			$this->plugins[$pluginName] = $tmp['settings'];
		}
	}
}
