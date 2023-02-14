<?php
/**
 * Menus
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Settings';

require_once SYSTEM . 'clients.conf.php';
if (empty($_GET['plugin'])) {
	error('Please select plugin from left Panel.');
	return;
}

$plugin = $_GET['plugin'];

if($plugin != 'core') {
	$pluginSettings = Plugins::getPluginSettings($plugin);
	if (!$pluginSettings) {
		error('This plugin does not exist or does not have options defined.');
		return;
	}

	$settingsFilePath = PLUGINS . $plugin . '/settings.php';
}
else {
	$settingsFilePath = SYSTEM . 'settings.php';
}

if (!file_exists($settingsFilePath)) {
	error('This plugin does not exist or does not have settings defined.');
	return;
}

if($plugin === 'core') {
	$settingsFile = require $settingsFilePath;
}
else {
	$settingsFile = require $settingsFilePath;
}

if (!is_array($settingsFile)) {
	return;
}

if (isset($_POST['save'])) {
	$db->query('DELETE FROM `' . TABLE_PREFIX . 'settings` WHERE `plugin_name` = ' . $db->quote($plugin) . ';');
	foreach ($_POST['settings'] as $key => $value) {
		try {
			$db->insert(TABLE_PREFIX . 'settings', ['plugin_name' => $plugin, 'key' => $key, 'value' => $value]);
		} catch (PDOException $error) {
			warning('Error while saving setting (' . $plugin . ' - ' . $key . '): ' . $error->getMessage());
		}
	}

	$cache = Cache::getInstance();
	if ($cache->enabled()) {
		$cache->delete('settings');
	}
	success('Saved at ' . date('H:i'));
}

$title = ($plugin == 'core' ? 'Settings' : 'Plugin Settings - ' . $plugin);

$query = 'SELECT `key`, `value` FROM `' . TABLE_PREFIX . 'settings` WHERE `plugin_name` = ' . $db->quote($plugin) . ';';
$query = $db->query($query);

$settingsDb = [];
if($query->rowCount() > 0) {
	foreach($query->fetchAll(PDO::FETCH_ASSOC) as $value) {
		$settingsDb[$value['key']] = $value['value'];
	}
}

?>

<form method="post">
	<div class="row">
		<div class="col-md-12">
			<div class="box">
				<div class="box-body">
					<button name="save" type="submit" class="btn btn-primary">Save</button>
				</div>
				<br/>
				<ul class="nav nav-tabs" id="myTab">
				<?php
					$i = 0;
					foreach($settingsFile as $key => $setting) {
					if ($setting['type'] === 'category') {
						?>
							<li class="nav-item">
								<button class="nav-link<?= ($i === 0 ? ' active' : ''); ?>" id="home-tab-<?= $i++; ?>" data-toggle="tab" data-target="#tab-<?= str_replace(' ', '', $setting['title']); ?>" type="button"><?= $setting['title']; ?></button>
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
					foreach($settingsFile as $key => $setting) {
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
							<th style="width: 10%">Name</th>
							<th style="width: 30%">Value</th>
							<th>Description</th>
						</tr>
						</thead>
						<tbody>
						<?php
						continue;
						}
						?>
						<tr>
							<td><label for="<?= $key ?>" class="control-label"><?= $setting['name'] ?></label></td>
							<td>
								<?php
								if ($setting['type'] === 'boolean') {
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

								if ($setting['type'] === 'options') {
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
								?>
							</td>
							<td>
								<div class="well">
									<?= $setting['desc'] ?>
								</div>
							</td>
						</tr>
						<?php
					}
					?>
					</tbody>
				</table>
					</div>
				</div>
				<div class="box-footer">
					<button name="save" type="submit" class="btn btn-primary">Save</button>
				</div>
			</div>
		</div>
	</div>
</form>
