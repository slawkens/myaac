<?php
/**
 * @var OTS_DB_MySQL $db
 */

// 2023-11-11
// execute highscores_ids_hidden once again, cause of settings
$up = function () {
	require_once __DIR__ . '/20.php';
	updateHighscoresIdsHidden();
};

$down = function () {
	// there is no downgrade for this
};

