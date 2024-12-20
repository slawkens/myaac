<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	if (!$db->hasColumn(TABLE_PREFIX . 'pages', 'enable_tinymce')) {
		$db->addColumn(TABLE_PREFIX . 'pages', 'enable_tinymce', "TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 - enabled, 0 - disabled' AFTER `php`");
	}
};

$down = function () use ($db) {
	if ($db->hasColumn(TABLE_PREFIX . 'pages', 'enable_tinymce')) {
		$db->dropColumn(TABLE_PREFIX . 'pages', 'enable_tinymce');
	}
};
