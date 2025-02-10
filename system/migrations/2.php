<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	$db->modifyColumn(TABLE_PREFIX . 'faq', 'answer', "VARCHAR(1020) NOT NULL DEFAULT ''");
	$db->modifyColumn(TABLE_PREFIX . 'movies', 'title', "VARCHAR(100) NOT NULL DEFAULT ''");
	$db->modifyColumn(TABLE_PREFIX . 'news', 'title', "VARCHAR(100) NOT NULL DEFAULT ''");
	$db->modifyColumn(TABLE_PREFIX . 'news', 'body', "TEXT NOT NULL");
};
