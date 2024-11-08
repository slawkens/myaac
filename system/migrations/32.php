<?php
/**
 * @var OTS_DB_MySQL $db
 */

// Increase size of page in myaac_visitors table

$up = function () use ($db) {
	$db->modifyColumn(TABLE_PREFIX . 'visitors', 'page', 'VARCHAR(2048) NOT NULL');
};

$down = function () {
	// nothing to be done, as we have just extended the size of a column
};
