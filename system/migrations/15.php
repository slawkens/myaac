<?php
/**
 * @var OTS_DB_MySQL $db
 */

// add new forum.guild and forum.access fields

$up = function () use ($db) {
	if (!$db->hasColumn(TABLE_PREFIX . 'forum_boards', 'guild')) {
		$db->addColumn(TABLE_PREFIX . 'forum_boards', 'guild', 'TINYINT(1) NOT NULL DEFAULT 0 AFTER `closed`');
	}

	if (!$db->hasColumn(TABLE_PREFIX . 'forum_boards', 'access')) {
		$db->addColumn(TABLE_PREFIX . 'forum_boards', 'access', 'TINYINT(1) NOT NULL DEFAULT 0 AFTER `guild`');
	}
};

$down = function () use ($db) {
	if ($db->hasColumn(TABLE_PREFIX . 'forum_boards', 'guild')) {
		$db->dropColumn(TABLE_PREFIX . 'forum_boards', 'guild');
	}

	if ($db->hasColumn(TABLE_PREFIX . 'forum_boards', 'access')) {
		$db->dropColumn(TABLE_PREFIX . 'forum_boards', 'access');
	}
};
