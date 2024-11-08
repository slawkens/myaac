<?php
/**
 * @var OTS_DB_MySQL $db
 */

// add look column
$up = function () use ($db) {
	if (!$db->hasColumn(TABLE_PREFIX . 'monsters', 'look')) {
		$db->addColumn(TABLE_PREFIX . 'monsters', 'look', "VARCHAR(255) NOT NULL DEFAULT '' AFTER `health`");
	}
};

$down = function () use ($db) {
	if ($db->hasColumn(TABLE_PREFIX . 'monsters', 'look')) {
		$db->dropColumn(TABLE_PREFIX . 'monsters', 'look');
	}
};
