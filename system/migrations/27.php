<?php


use MyAAC\Models\Pages;

$up = function () {
	$downloadsModel = Pages::where('name', 'downloads')->first();
	if (!$downloadsModel) {
		Pages::create([
			'name' => 'downloads',
			'title' => 'Downloads',
			'body' => file_get_contents(__DIR__ . '/27-downloads.html'),
			'date' => time(),
			'player_id' => 1,
			'php' => 0,
			'access' => 0,
			'hide' => 0,
		]);
	}

	$commandsModel = Pages::where('name', 'commands')->first();
	if (!$commandsModel) {
		Pages::create([
			'name' => 'commands',
			'title' => 'Commands',
			'body' => file_get_contents(__DIR__ . '/27-commands.html'),
			'date' => time(),
			'player_id' => 1,
			'php' => 0,
			'access' => 0,
			'hide' => 0,
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
