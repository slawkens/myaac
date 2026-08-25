<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	// add new item_id field for runes
	if (!$db->hasColumn(TABLE_PREFIX . 'spells', 'item_id')) {
		$db->addColumn(TABLE_PREFIX . 'spells', 'item_id', 'INT(11) NOT NULL DEFAULT 0 AFTER `conjure_count`');
	}

	// change unique index from spell to name
	$db->query("ALTER TABLE `" . TABLE_PREFIX . "spells` DROP INDEX `spell`;");
	$db->query("ALTER TABLE `" . TABLE_PREFIX . "spells` ADD UNIQUE INDEX (`name`);");

	// change comment of spells.type
	$db->modifyColumn(TABLE_PREFIX . 'spells', 'type', "TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 - instant, 2 - conjure, 3 - rune'");

	// new items table
	if (!$db->hasTable(TABLE_PREFIX . 'items')) {
		$db->query(file_get_contents(__DIR__ . '/12-items.sql'));
	}

	// new weapons table
	if (!$db->hasTable(TABLE_PREFIX . 'weapons')) {
		$db->query(file_get_contents(__DIR__ . '/12-weapons.sql'));
	}

	// modify vocations to support json data
	$db->modifyColumn(TABLE_PREFIX . 'spells', 'vocations', "VARCHAR(100) NOT NULL DEFAULT ''");
};

$down = function () use ($db) {
	// remove item_id field for runes
	if ($db->hasColumn(TABLE_PREFIX . 'spells', 'item_id')) {
		$db->dropColumn(TABLE_PREFIX . 'spells', 'item_id');
	}

	// change unique index from spell to name
	$db->query("ALTER TABLE `" . TABLE_PREFIX . "spells` DROP INDEX `name`;");
	$db->query("ALTER TABLE `" . TABLE_PREFIX . "spells` ADD INDEX (`spell`);");

	$db->dropTable(TABLE_PREFIX . 'items');
	$db->dropTable(TABLE_PREFIX . 'weapons');
};
