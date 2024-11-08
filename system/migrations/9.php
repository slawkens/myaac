<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	$db->modifyColumn(TABLE_PREFIX . 'bugtracker', 'type', "INT(11) NOT NULL DEFAULT 0");
	$db->modifyColumn(TABLE_PREFIX . 'bugtracker', 'status', "INT(11) NOT NULL DEFAULT 0");
	$db->modifyColumn(TABLE_PREFIX . 'bugtracker', 'id', "INT(11) NOT NULL DEFAULT 0");
	$db->modifyColumn(TABLE_PREFIX . 'bugtracker', 'subject', "VARCHAR(255) NOT NULL DEFAULT ''");
	$db->modifyColumn(TABLE_PREFIX . 'bugtracker', 'reply', "INT(11) NOT NULL DEFAULT 0");
	$db->modifyColumn(TABLE_PREFIX . 'bugtracker', 'who', "INT(11) NOT NULL DEFAULT 0");
	$db->modifyColumn(TABLE_PREFIX . 'bugtracker', 'tag', "INT(11) NOT NULL DEFAULT 0");
};

$down = function () {
	// nothing to do here
};
