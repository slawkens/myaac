<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	$db->modifyColumn(TABLE_PREFIX . 'monsters', 'loot', 'text NOT NULL');
};

$down = function () {
	// nothing to do
};
