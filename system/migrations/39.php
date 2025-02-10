<?php
/**
 * @var OTS_DB_MySQL $db
 */

// 2024-01-27
// change hidden to hide (Eloquent model reserved keyword)

$definition = 'TINYINT(1) NOT NULL DEFAULT 0';

$up = function () use ($db, $definition) {
	if (!$db->hasColumn('players', 'hide')) {
		$db->changeColumn('players', 'hidden', 'hide', $definition);
	}

	$db->changeColumn(TABLE_PREFIX . 'changelog', 'hidden', 'hide', $definition);
	$db->changeColumn(TABLE_PREFIX . 'faq', 'hidden', 'hide', $definition);
	$db->changeColumn(TABLE_PREFIX . 'forum_boards', 'hidden', 'hide', $definition);
	$db->changeColumn(TABLE_PREFIX . 'monsters', 'hidden', 'hide', $definition);
	$db->changeColumn(TABLE_PREFIX . 'news', 'hidden', 'hide', $definition);
	$db->changeColumn(TABLE_PREFIX . 'news_categories', 'hidden', 'hide', $definition);
	$db->changeColumn(TABLE_PREFIX . 'pages', 'hidden', 'hide', $definition);
	$db->changeColumn(TABLE_PREFIX . 'gallery', 'hidden', 'hide', $definition);
	$db->changeColumn(TABLE_PREFIX . 'spells', 'hidden', 'hide', $definition);
};

$down = function () use ($db, $definition) {
	if (!$db->hasColumn('players', 'hidden')) {
		$db->changeColumn('players', 'hide', 'hidden', $definition);
	}

	$db->changeColumn(TABLE_PREFIX . 'changelog', 'hide', 'hidden', $definition);
	$db->changeColumn(TABLE_PREFIX . 'faq', 'hide', 'hidden', $definition);
	$db->changeColumn(TABLE_PREFIX . 'forum_boards', 'hide', 'hidden', $definition);
	$db->changeColumn(TABLE_PREFIX . 'monsters', 'hide', 'hidden', $definition);
	$db->changeColumn(TABLE_PREFIX . 'news', 'hide', 'hidden', $definition);
	$db->changeColumn(TABLE_PREFIX . 'news_categories', 'hide', 'hidden', $definition);
	$db->changeColumn(TABLE_PREFIX . 'pages', 'hide', 'hidden', $definition);
	$db->changeColumn(TABLE_PREFIX . 'gallery', 'hide', 'hidden', $definition);
	$db->changeColumn(TABLE_PREFIX . 'spells', 'hide', 'hidden', $definition);
};
