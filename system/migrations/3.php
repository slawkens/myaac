<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	if (!$db->hasColumn(TABLE_PREFIX . 'account_actions', 'ipv6')) {
		$db->addColumn(TABLE_PREFIX . 'account_actions', 'ipv6', "BINARY(16) NOT NULL DEFAULT 0");
	}
};

$down = function () {
	// we don't want data loss
	//$db->dropColumn(TABLE_PREFIX . 'account_actions', 'ipv6');
};
