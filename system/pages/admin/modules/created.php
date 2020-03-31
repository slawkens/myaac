<?php
$players = ($db->hasColumn('accounts', 'created') ? $db->query('SELECT `created`, `' . (USE_ACCOUNT_NAME ? 'name' : 'id') . '` as `name` FROM `accounts` ORDER BY `created` DESC LIMIT 10;') : 0);

$twig->display('created.html.twig', array(
	'players' => $players,
));
