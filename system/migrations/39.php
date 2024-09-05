<?php

// 2024-01-27
// change hidden to hide (Eloquent model reserved keyword)

$up = function () use ($db) {
	if (!$db->hasColumn('players', 'hide')) {
		$db->exec("ALTER TABLE `players` CHANGE `hidden` `hide` TINYINT(1) NOT NULL DEFAULT 0;");
	}

	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "changelog` CHANGE `hidden` `hide` TINYINT(1) NOT NULL DEFAULT 0;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "faq` CHANGE `hidden` `hide` TINYINT(1) NOT NULL DEFAULT 0;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "forum_boards` CHANGE `hidden` `hide` TINYINT(1) NOT NULL DEFAULT 0;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters` CHANGE `hidden` `hide` TINYINT(1) NOT NULL DEFAULT 0;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "news` CHANGE `hidden` `hide` TINYINT(1) NOT NULL DEFAULT 0;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "news_categories` CHANGE `hidden` `hide` TINYINT(1) NOT NULL DEFAULT 0;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "pages` CHANGE `hidden` `hide` TINYINT(1) NOT NULL DEFAULT 0;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "gallery` CHANGE `hidden` `hide` TINYINT(1) NOT NULL DEFAULT 0;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "spells` CHANGE `hidden` `hide` TINYINT(1) NOT NULL DEFAULT 0;");
};

$down = function () use ($db) {
	if (!$db->hasColumn('players', 'hide')) {
		$db->exec("ALTER TABLE `players` CHANGE `hide` `hidden` TINYINT(1) NOT NULL DEFAULT 0;");
	}

	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "changelog` CHANGE `hide` `hidden` TINYINT(1) NOT NULL DEFAULT 0;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "faq` CHANGE `hide` `hidden` TINYINT(1) NOT NULL DEFAULT 0;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "forum_boards` CHANGE `hide` `hidden` TINYINT(1) NOT NULL DEFAULT 0;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "monsters` CHANGE `hide` `hidden` TINYINT(1) NOT NULL DEFAULT 0;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "news` CHANGE `hide` `hidden` TINYINT(1) NOT NULL DEFAULT 0;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "news_categories` CHANGE `hide` `hide` TINYINT(1) NOT NULL DEFAULT 0;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "pages` CHANGE `hide` `hidden` TINYINT(1) NOT NULL DEFAULT 0;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "gallery` CHANGE `hide` `hidden` TINYINT(1) NOT NULL DEFAULT 0;");
	$db->exec("ALTER TABLE `" . TABLE_PREFIX . "spells` CHANGE `hide` `hidden` TINYINT(1) NOT NULL DEFAULT 0;");
};
