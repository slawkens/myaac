<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	$db->dropTable(TABLE_PREFIX . 'hooks');

	$cache = app()->get('cache');
	if($cache->enabled()) {
		$cache->delete('hooks');
	}
};

$down = function () use ($db) {
	$db->exec(file_get_contents(__DIR__ . '/28-hooks.sql'));

	$cache = app()->get('cache');
	if($cache->enabled()) {
		$cache->delete('hooks');
	}
};

