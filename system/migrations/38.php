<?php
/**
 * @var OTS_DB_MySQL $db
 */

// 2023-11-11
// execute highscores_ids_hidden once again, cause of settings
$up = function () use ($db) {
	require __DIR__ . '/20.php';
};

$down = function () {
	// there is no downgrade for this
};

