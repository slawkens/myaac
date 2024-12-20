<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	if ($db->hasColumn(TABLE_PREFIX . 'screenshots', 'name')) {
		$db->dropColumn(TABLE_PREFIX . 'screenshots', 'name');
	}
};

$up = function () use ($db) {
	if (!$db->hasColumn(TABLE_PREFIX . 'screenshots', 'name')) {
		$db->addColumn(TABLE_PREFIX . 'screenshots', 'name', 'VARCHAR(30) NOT NULL');
	}
};
