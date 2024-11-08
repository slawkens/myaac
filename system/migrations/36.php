<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	// add settings table
	if (!$db->hasTable(TABLE_PREFIX . 'settings')) {
		$db->exec(file_get_contents(__DIR__ . '/36-settings.sql'));
	}
};

$down = function () {
	// will break the aac
	//if ($db->hasTable(TABLE_PREFIX . 'settings')) {
	//	$db->dropTable(TABLE_PREFIX . 'settings');
	//}
};
