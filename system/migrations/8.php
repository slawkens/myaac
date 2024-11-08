<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	if ($db->hasTable(TABLE_PREFIX . 'forum_sections')) {
		$db->renameTable(TABLE_PREFIX . 'forum_sections', TABLE_PREFIX . 'forum_boards');
	}

	$query = $db->query('SELECT `id` FROM `' . TABLE_PREFIX . 'forum_boards` WHERE `ordering` > 0;');
	if ($query->rowCount() == 0) {
		$boards = [
			'News',
			'Trade',
			'Quests',
			'Pictures',
			'Bug Report'
		];

		foreach ($boards as $id => $board) {
			$db->query('UPDATE `' . TABLE_PREFIX . 'forum_boards` SET `ordering` = ' . $id . ' WHERE `name` = ' . $db->quote($board));
		}
	}
};

$down = function () use ($db) {
	if ($db->hasTable(TABLE_PREFIX . 'forum_boards')) {
		$db->renameTable(TABLE_PREFIX . 'forum_boards', TABLE_PREFIX . 'forum_sections');
	}
};
