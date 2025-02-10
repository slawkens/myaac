<?php
/**
 * @var OTS_DB_MySQL $db
 */

// Increase size of ip in myaac_visitors table
// according to this answer: https://stackoverflow.com/questions/166132/maximum-length-of-the-textual-representation-of-an-ipv6-address
// the size of ipv6 can be maximal 45 chars

$up = function () use ($db) {
	$db->modifyColumn(TABLE_PREFIX . 'visitors', 'ip', 'VARCHAR(45) NOT NULL');
};

$down = function () {
	// nothing to be done, as we have just extended the size of a column
};
