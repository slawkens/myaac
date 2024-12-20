<?php
/**
 * Change database tables character set to utf8mb4
 * Previously it was utf8 (utf8mb3)
 * utf8 will become utf8mb4 in future releases of mysql
 */
$tables = [
	'account_actions', 'admin_menu',
	'changelog', 'config',
	'faq', 'forum_boards', 'forum',
	'gallery',
	'menu', 'monsters',
	'news', 'news_categories', 'notepad',
	'pages',
	'settings', 'spells',
	'visitors', 'weapons',
];

$up = function () use ($db, $tables)
{
	foreach ($tables as $table) {
		if ($db->hasTable(TABLE_PREFIX . $table)) {
			$db->exec('ALTER TABLE ' . TABLE_PREFIX . $table . ' CONVERT TO CHARACTER SET utf8mb4');
		}
	}
};

$down = function () use ($db, $tables)
{
	foreach ($tables as $table) {
		if ($db->hasTable(TABLE_PREFIX . $table)) {
			$db->exec('ALTER TABLE ' . TABLE_PREFIX . $table . ' CONVERT TO CHARACTER SET utf8');
		}
	}
};
