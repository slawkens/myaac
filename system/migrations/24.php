<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	if ($db->hasTable(TABLE_PREFIX . 'items')) {
		$db->dropTable(TABLE_PREFIX . 'items');
	}
};

$down = function () use ($db) {
	if (!$db->hasTable(TABLE_PREFIX . 'items')) {
		$db->exec(file_get_contents(__DIR__ . '/24-items.sql'));
	}
};
