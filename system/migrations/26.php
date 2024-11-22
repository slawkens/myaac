<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	if ($db->hasColumn(TABLE_PREFIX . 'spells', 'spell')) {
		$db->modifyColumn(TABLE_PREFIX . 'spells', 'spell', "VARCHAR(255) NOT NULL DEFAULT ''");
	}

	if ($db->hasColumn(TABLE_PREFIX . 'spells', 'words')) {
		$db->modifyColumn(TABLE_PREFIX . 'spells', 'words', "VARCHAR(255) NOT NULL DEFAULT ''");
	}

	if (!$db->hasColumn(TABLE_PREFIX . 'spells', 'conjure_id')) {
		$db->addColumn(TABLE_PREFIX . 'spells', 'conjure_id', 'INT(11) NOT NULL DEFAULT 0 AFTER `soul`');
	}

	if (!$db->hasColumn(TABLE_PREFIX . 'spells', 'reagent')) {
		$db->addColumn(TABLE_PREFIX . 'spells', 'reagent', 'INT(11) NOT NULL DEFAULT 0 AFTER `conjure_count`');
	}
};

$down = function () use ($db) {
	if ($db->hasColumn(TABLE_PREFIX . 'spells', 'conjure_id')) {
		$db->dropColumn(TABLE_PREFIX . 'spells', 'conjure_id');
	}

	if ($db->hasColumn(TABLE_PREFIX . 'spells', 'reagent')) {
		$db->dropColumn(TABLE_PREFIX . 'spells', 'reagent');
	}
};
