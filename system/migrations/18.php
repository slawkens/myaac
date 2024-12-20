<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	if (!$db->hasColumn(TABLE_PREFIX . 'news', 'article_text')) {
		$db->addColumn(TABLE_PREFIX . 'news', 'article_text', "VARCHAR(300) NOT NULL DEFAULT '' AFTER `comments`");
	}

	if (!$db->hasColumn(TABLE_PREFIX . 'news', 'article_image')) {
		$db->addColumn(TABLE_PREFIX . 'news', 'article_image', "VARCHAR(100) NOT NULL DEFAULT '' AFTER `article_text`");
	}
};

$down = function () use ($db) {
	if ($db->hasColumn(TABLE_PREFIX . 'news', 'article_text')) {
		$db->dropColumn(TABLE_PREFIX . 'news', 'article_text');
	}

	if ($db->hasColumn(TABLE_PREFIX . 'news', 'article_image')) {
		$db->dropColumn(TABLE_PREFIX . 'news', 'article_image');
	}
};
