<?php
/**
 * @var OTS_DB_MySQL $db
 */

use MyAAC\Models\Pages;

$up = function () use ($db) {
	$downloadsModel = Pages::where('name', 'downloads')->first();
	if (!$downloadsModel) {
		$db->insert(TABLE_PREFIX . 'pages', [
			'name' => 'downloads',
			'title' => 'Downloads',
			'body' => file_get_contents(__DIR__ . '/27-downloads.html'),
			'date' => time(),
			'player_id' => 1,
			'php' => 0,
			'access' => 0,
			($db->hasColumn(TABLE_PREFIX . 'pages', 'hide') ? 'hide' : 'hidden') => 0,
		]);
	}

	$commandsModel = Pages::where('name', 'commands')->first();
	if (!$commandsModel) {
		$db->insert(TABLE_PREFIX . 'pages', [
			'name' => 'commands',
			'title' => 'Commands',
			'body' => file_get_contents(__DIR__ . '/27-commands.html'),
			'date' => time(),
			'player_id' => 1,
			'php' => 0,
			'access' => 0,
			($db->hasColumn(TABLE_PREFIX . 'pages', 'hide') ? 'hide' : 'hidden') => 0,
		]);
	}
};

$down = function () {
	$downloadsModel = Pages::where('name', 'downloads')->first();
	if ($downloadsModel) {
		$downloadsModel->delete();
	}

	$commandsModel = Pages::where('name', 'commands')->first();
	if ($commandsModel) {
		$commandsModel->delete();
	}
};
