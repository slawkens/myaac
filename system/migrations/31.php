<?php

if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'elements')) {
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `elements` TEXT NOT NULL AFTER `immunities`;");
}

if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'pushable')) {
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `pushable` TINYINT(1) NOT NULL DEFAULT '0' AFTER `convinceable`;");
}

if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'canpushitems')) {
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `canpushitems` TINYINT(1) NOT NULL DEFAULT '0' AFTER `pushable`;");
}

if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'canpushcreatures')) {
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `canpushcreatures` TINYINT(1) NOT NULL DEFAULT '0' AFTER `canpushitems`;");
}

if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'canwalkonenergy')) {
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `canwalkonenergy` TINYINT(1) NOT NULL DEFAULT '0' AFTER `canpushitems`;");
}

if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'canwalkonpoison')) {
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `canwalkonpoison` TINYINT(1) NOT NULL DEFAULT '0' AFTER `canwalkonenergy`;");
}

if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'canwalkonfire')) {
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `canwalkonfire` TINYINT(1) NOT NULL DEFAULT '0' AFTER `canwalkonpoison`;");
}

if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'runonhealth')) {
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `runonhealth` TINYINT(1) NOT NULL DEFAULT '0' AFTER `canwalkonfire`;");
}

if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'hostile')) {
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `hostile` TINYINT(1) NOT NULL DEFAULT '0' AFTER `runonhealth`;");
}

if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'attackable')) {
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `attackable` TINYINT(1) NOT NULL DEFAULT '0' AFTER `hostile`;");
}

if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'rewardboss')) {
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `rewardboss` TINYINT(1) NOT NULL DEFAULT '0' AFTER `attackable`;");
}

if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'defense')) {
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `defense` INT(11) NOT NULL DEFAULT '0' AFTER `rewardboss`;");
}

if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'armor')) {
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `armor` INT(11) NOT NULL DEFAULT '0' AFTER `defense`;");
}

if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'summons')) {
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `summons` TEXT NOT NULL AFTER `loot`;");
}
