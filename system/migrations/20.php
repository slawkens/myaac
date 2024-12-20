<?php

use MyAAC\Settings;

function updateHighscoresIdsHidden(): void
{
	global $db;

	if (!$db->hasTable('players')) {
		return;
	}

	$query = $db->query("SELECT `id` FROM `players` WHERE (`name` = " . $db->quote("Rook Sample") . " OR `name` = " . $db->quote("Sorcerer Sample") . " OR `name` = " . $db->quote("Druid Sample") . " OR `name` = " . $db->quote("Paladin Sample") . " OR `name` = " . $db->quote("Knight Sample") . " OR `name` = " . $db->quote("Account Manager") . ") ORDER BY `id`;");

	$highscores_ignored_ids = array();
	if ($query->rowCount() > 0) {
		foreach ($query->fetchAll() as $result)
			$highscores_ignored_ids[] = $result['id'];
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
