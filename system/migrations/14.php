<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	// change monsters.file_path field to loot
	if ($db->hasColumn(TABLE_PREFIX . 'monsters', 'file_path')) {
		$db->changeColumn(TABLE_PREFIX . 'monsters', 'file_path', 'loot', 'VARCHAR(5000)');
	}

	// update loot to empty string
	$db->query("UPDATE `" . TABLE_PREFIX . "monsters` SET `loot` = '';");

	// drop monsters.gfx_name field
	$db->dropColumn(TABLE_PREFIX . 'monsters', 'gfx_name');

	// rename hide_creature to hidden
	if ($db->hasColumn(TABLE_PREFIX . 'monsters', 'hide_creature')) {
		$db->changeColumn(TABLE_PREFIX . 'monsters', 'hide_creature', 'hidden', "TINYINT(1) NOT NULL DEFAULT 0");
	}
};

$down = function () use ($db) {
	if ($db->hasColumn(TABLE_PREFIX . 'monsters', 'loot')) {
		$db->changeColumn(TABLE_PREFIX . 'monsters', 'loot', 'file_path', 'VARCHAR(5000)');
	}

	// update file_path to empty string
	$db->query("UPDATE `" . TABLE_PREFIX . "monsters` SET `file_path` = '';");

	// add monsters.gfx_name field
	$db->addColumn(TABLE_PREFIX . 'monsters', 'gfx_name', 'varchar(255) NOT NULL AFTER `race`');

	// rename hidden to hide_creature
	if ($db->hasColumn(TABLE_PREFIX . 'monsters', 'hidden')) {
		$db->changeColumn(TABLE_PREFIX . 'monsters', 'hidden', 'hide_creature', 'TINYINT(1) NOT NULL DEFAULT 0');
	}
};
