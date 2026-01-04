<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	if (!$db->hasColumn(TABLE_PREFIX . 'menu', 'access')) {
		$db->addColumn(TABLE_PREFIX . 'menu', 'access', 'TINYINT NOT NULL DEFAULT 0 AFTER `link`');
	}
};

$down = function () use ($db) {
	if ($db->hasColumn(TABLE_PREFIX . 'menu', 'access')) {
		$db->dropColumn(TABLE_PREFIX . 'menu', 'access');
	}
};
