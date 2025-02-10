<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	if ($db->hasColumn(TABLE_PREFIX . 'spells', 'spell')) {
		$db->dropColumn(TABLE_PREFIX . 'spells', 'spell');
	}
};

$down = function () use ($db) {
	if (!$db->hasColumn(TABLE_PREFIX . 'spells', 'spell')) {
		$db->addColumn(TABLE_PREFIX . 'spells', 'spell', "VARCHAR(255) NOT NULL DEFAULT ''");
	}
};
