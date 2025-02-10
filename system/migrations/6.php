<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	if (!$db->hasColumn(TABLE_PREFIX . 'hooks', 'enabled')) {
		$db->addColumn(TABLE_PREFIX . 'hooks', 'enabled', 'INT(1) NOT NULL DEFAULT 1');
	}
};

$down = function () use ($db) {
	if ($db->hasColumn(TABLE_PREFIX . 'hooks', 'enabled')) {
		$db->dropColumn(TABLE_PREFIX . 'hooks', 'enabled');
	}
};
