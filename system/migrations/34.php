<?php
/**
 * @var OTS_DB_MySQL $db
 */

// add user_agent column into visitors

$up = function () use ($db) {
	if (!$db->hasColumn(TABLE_PREFIX . 'visitors', 'user_agent')) {
		$db->addColumn(TABLE_PREFIX . 'visitors', 'user_agent', "VARCHAR(255) NOT NULL DEFAULT ''");
	}
};

$down = function () use ($db) {
	if ($db->hasColumn(TABLE_PREFIX . 'visitors', 'user_agent')) {
		$db->dropColumn(TABLE_PREFIX . 'visitors', 'user_agent');
	}
};
