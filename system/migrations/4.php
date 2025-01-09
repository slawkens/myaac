<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	if (!$db->hasColumn(TABLE_PREFIX . 'monsters', 'id')) {
		$db->addColumn(TABLE_PREFIX . 'monsters', 'id', "int(11) NOT NULL AUTO_INCREMENT primary key FIRST");
	}
};

$down = function () use ($db) {
	if ($db->hasColumn(TABLE_PREFIX . 'monsters', 'id')) {
		$db->dropColumn(TABLE_PREFIX . 'monsters', 'id');
	}
};
