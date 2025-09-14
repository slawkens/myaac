<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	if (!$db->hasColumn(TABLE_PREFIX . 'hooks', 'ordering')) {
		$db->addColumn(TABLE_PREFIX . 'hooks', 'ordering', "INT(11) NOT NULL DEFAULT 0 AFTER `file`");
	}

	if (!$db->hasTable(TABLE_PREFIX . 'admin_menu')) {
		$db->query(file_get_contents(__DIR__ . '/10-admin_menu.sql'));
	}
};

$down = function () use ($db) {
	if ($db->hasColumn(TABLE_PREFIX . 'hooks', 'ordering')) {
		$db->dropColumn(TABLE_PREFIX . 'hooks', 'ordering');
	}

	if ($db->hasTable(TABLE_PREFIX . 'admin_menu')) {
		$db->dropTable(TABLE_PREFIX . 'admin_menu');
	}
};
