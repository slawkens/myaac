<?php

// add look column
$up = function () use ($db) {
	$db->exec('ALTER TABLE `' . TABLE_PREFIX . "monsters` ADD `look` VARCHAR(255) NOT NULL DEFAULT '' AFTER `health`;");
};

$down = function () use ($db) {
	$db->exec('ALTER TABLE `' . TABLE_PREFIX . "monsters` DROP COLUMN `look`;");
};
