<?php
/**
 * @var OTS_DB_MySQL $db
 */

use MyAAC\Plugins;

$up = function () use ($db) {
	if (!$db->hasTable(TABLE_PREFIX . 'menu')) {
		$db->exec(file_get_contents(__DIR__ . '/17-menu.sql'));
	}

	Plugins::installMenus('kathrine', require TEMPLATES . 'kathrine/menus.php');
	Plugins::installMenus('tibiacom', require TEMPLATES . 'tibiacom/menus.php');
};

$down = function () use ($db) {
	$db->dropTable(TABLE_PREFIX . 'menu');
};

