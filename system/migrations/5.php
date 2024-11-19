<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	if ($db->hasColumn(TABLE_PREFIX . 'spells', 'cities')) {
		$db->dropColumn(TABLE_PREFIX . 'spells', 'cities');
	}
};

$up = function () use ($db) {
	if ($db->hasColumn(TABLE_PREFIX . 'spells', 'cities')) {
		$db->addColumn(TABLE_PREFIX . 'spells', 'cities', 'VARCHAR(32) NOT NULL');
	}
};
