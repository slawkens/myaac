<?php
/**
 * @var OTS_DB_MySQL $db
 */

use MyAAC\Plugins;

$up = function () use ($db) {
	if (!$db->hasTable(TABLE_PREFIX . 'menu')) {
		$db->exec(file_get_contents(__DIR__ . '/17-menu.sql'));
	}

	$themes = ['kathrine', 'tibiacom',];
	foreach ($themes as $theme) {
		$file = TEMPLATES . $theme . '/menus.php';
		if (is_file($file)) {
			Plugins::installMenus($theme, require $file);
		}
	}
};

$down = function () use ($db) {
	$db->dropTable(TABLE_PREFIX . 'menu');
};

