<?php
/**
 * @var OTS_DB_MySQL $db
 */

use MyAAC\Models\Pages;

$up = function () use ($db) {
	$otsInfoModel = Pages::where('name', 'ots-info')->first();
	if (!$otsInfoModel) {
		$db->insert(TABLE_PREFIX . 'pages', [
			'name' => 'ots-info',
			'title' => 'OTS Info',
			'body' => file_get_contents(__DIR__ . '/53-ots-info.html'),
			'date' => time(),
			'player_id' => 1,
			'php' => 0,
			'enable_tinymce' => 1,
			'access' => 0,
			($db->hasColumn(TABLE_PREFIX . 'pages', 'hide') ? 'hide' : 'hidden') => 0,
		]);
	}
};

$down = function () {
	$otsInfoModel = Pages::where('name', 'ots-info')->first();
	if ($otsInfoModel) {
		$otsInfoModel->delete();
	}
};
