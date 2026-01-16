<?php

use MyAAC\Models\Player as PlayerModel;
use MyAAC\Settings;

function updateHighscoresIdsHidden(): void
{
	global $db;

	if (!$db->hasTable('players')) {
		return;
	}

	$players = PlayerModel::where('name', 'Rook Sample')
		->orWhere('name', 'Sorcerer Sample')
		->orWhere('name', 'Druid Sample')
		->orWhere('name', 'Paladin Sample')
		->orWhere('name', 'Knight Sample')
		->orWhere('name', 'Monk Sample')
		->orWhere('name', 'Account Manager')
		->orderBy('id')
		->select('id')
		->get();

	$highscores_ignored_ids = [];
	if (count($players) > 0) {
		foreach ($players as $result) {
			$highscores_ignored_ids[] = $result->id;
		}
	} else {
		$highscores_ignored_ids[] = 0;
	}

	$settings = Settings::getInstance();
	$settings->updateInDatabase('core', 'highscores_ids_hidden', implode(', ', $highscores_ignored_ids));
}

$up = function () {
	updateHighscoresIdsHidden();
};

$down = function () {
	$settings = Settings::getInstance();
	$settings->updateInDatabase('core', 'highscores_ids_hidden', '0');
};
