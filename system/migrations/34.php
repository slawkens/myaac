<?php
// add user_agent column into visitors

$up = function () use ($db) {
	$db->exec('ALTER TABLE `' . TABLE_PREFIX . "visitors` ADD `user_agent` VARCHAR(255) NOT NULL DEFAULT '';");
};

$down = function () use ($db) {
	$db->exec('ALTER TABLE `' . TABLE_PREFIX . "monsters` DROP COLUMN `user_agent`;");
};
