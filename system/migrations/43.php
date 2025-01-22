<?php
/**
 * @var OTS_DB_MySQL $db
 */

// 2025-01-22
// change columns to VARCHAR
$up = function () use ($db) {
	$db->query("UPDATE guilds set description = '' WHERE description is NULL;"); // prevent truncate error when column is NULL
	$db->modifyColumn('guilds', 'description', "VARCHAR(5000) NOT NULL DEFAULT ''");

	$db->query("UPDATE players set comment = '' WHERE comment is NULL;");
	$db->modifyColumn('players', 'comment', "VARCHAR(5000) NOT NULL DEFAULT ''");
};

$down = function () use ($db) {
	$db->modifyColumn('guilds', 'description', "TEXT NOT NULL");
	$db->modifyColumn('players', 'comment', "TEXT NOT NULL");
};

