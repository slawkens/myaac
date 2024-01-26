<?php

use MyAAC\Plugins;

$order = 10;

$settingsMenu = [];

$settingsMenu[] = [
	'name' => 'MyAAC',
	'link' => 'settings&plugin=core',
	'icon' => 'list',
	'order' => $order,
];

foreach (Plugins::getAllPluginsSettings() as $setting) {
	$file = BASE . $setting['settingsFilename'];
	if (!file_exists($file)) {
		warning('Plugin setting: ' . $file . ' - cannot be loaded.');
		continue;
	}

	$order += 10;

	$settings = require $file;

	$settingsMenu[] = [
		'name' => $settings['name'],
		'link' => 'settings&plugin=' . $setting['pluginFilename'],
		'icon' => 'list',
		'order' => $order,
	];
}

unset($settings, $file, $order);

return $settingsMenu;
