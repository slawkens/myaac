<?php

if(!$db->hasColumn(TABLE_PREFIX . 'monsters', 'elements')) {
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `elements` TEXT NOT NULL AFTER `immunities`;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `pushable` TINYINT(1) NOT NULL DEFAULT '0' AFTER `convinceable`;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `canpushitems` TINYINT(1) NOT NULL DEFAULT '0' AFTER `pushable`;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `canpushcreatures` TINYINT(1) NOT NULL DEFAULT '0' AFTER `canpushitems`;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `canwalkonenergy` TINYINT(1) NOT NULL DEFAULT '0' AFTER `canpushitems`;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `canwalkonpoison` TINYINT(1) NOT NULL DEFAULT '0' AFTER `canwalkonenergy`;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `canwalkonfire` TINYINT(1) NOT NULL DEFAULT '0' AFTER `canwalkonpoison`;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `runonhealth` TINYINT(1) NOT NULL DEFAULT '0' AFTER `canwalkonfire`;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `hostile` TINYINT(1) NOT NULL DEFAULT '0' AFTER `runonhealth`;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `attackable` TINYINT(1) NOT NULL DEFAULT '0' AFTER `hostile`;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `rewardboss` TINYINT(1) NOT NULL DEFAULT '0' AFTER `attackable`;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `defense` INT(11) NOT NULL DEFAULT '0' AFTER `rewardboss`;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `armor` INT(11) NOT NULL DEFAULT '0' AFTER `defense`;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters`  ADD `summons` TEXT NOT NULL AFTER `loot`;");
}
