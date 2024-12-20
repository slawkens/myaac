<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	if (!$db->hasColumn(TABLE_PREFIX . 'menu', 'blank')) {
		$db->addColumn(TABLE_PREFIX . 'menu', 'blank', 'TINYINT(1) NOT NULL DEFAULT 0 AFTER `link`');
	}

	if (!$db->hasColumn(TABLE_PREFIX . 'menu', 'color')) {
		$db->addColumn(TABLE_PREFIX . 'menu', 'color', "CHAR(6) NOT NULL DEFAULT '' AFTER `blank`");
	}
};

$down = function () use ($db) {
	if ($db->hasColumn(TABLE_PREFIX . 'menu', 'blank')) {
		$db->dropColumn(TABLE_PREFIX . 'menu', 'blank');
	}

	if ($db->hasColumn(TABLE_PREFIX . 'menu', 'color')) {
		$db->dropColumn(TABLE_PREFIX . 'menu', 'color');
	}
};
