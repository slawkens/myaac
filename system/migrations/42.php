<?php
/**
 * @var OTS_DB_MySQL $db
 */

// 2025-09-01
// resize forum.post_ip to support ipv6
$up = function () use ($db) {
	$db->modifyColumn(TABLE_PREFIX . 'forum', 'post_ip', "varchar(45) NOT NULL default '0.0.0.0'");
};

$down = function () {
	// there is no downgrade for this
};

