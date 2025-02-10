<?php
/**
 * @var OTS_DB_MySQL $db
 */

// change size of spells.vocations

$up = function () use ($db) {
	$db->modifyColumn(TABLE_PREFIX . 'spells', 'vocations', "VARCHAR(300) NOT NULL DEFAULT ''");
};

$down = function () {
	// nothing to do here
};
