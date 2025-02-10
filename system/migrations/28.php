<?php
/**
 * @var OTS_DB_MySQL $db
 */

use MyAAC\Cache\Cache;

$up = function () use ($db) {
	$db->dropTable(TABLE_PREFIX . 'hooks');

	$cache = Cache::getInstance();
	if($cache->enabled()) {
		$cache->delete('hooks');
	}
};

$down = function () use ($db) {
	$db->exec(file_get_contents(__DIR__ . '/28-hooks.sql'));

	$cache = Cache::getInstance();
	if($cache->enabled()) {
		$cache->delete('hooks');
	}
};

