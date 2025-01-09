<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	$db->modifyColumn(TABLE_PREFIX . 'account_actions', 'ip', "INT(11) NOT NULL DEFAULT 0");
	$db->modifyColumn(TABLE_PREFIX . 'account_actions', 'date', "INT(11) NOT NULL DEFAULT 0");
	$db->modifyColumn(TABLE_PREFIX . 'account_actions', 'action', "VARCHAR(255) NOT NULL DEFAULT ''");

	$db->query(file_get_contents(__DIR__ . '/1-hooks.sql'));
};

$down = function () use ($db) {
	$db->dropTable(TABLE_PREFIX . 'hooks');
};
