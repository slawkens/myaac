<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	if ($db->hasTable(TABLE_PREFIX . 'gallery')) {
		$db->dropTable(TABLE_PREFIX . 'gallery');
	}
};

$down = function () use ($db) {
	if (!$db->hasTable(TABLE_PREFIX . 'gallery')) {
		$db->query(file_get_contents(__DIR__ . '/50-gallery.sql'));
	}
};
