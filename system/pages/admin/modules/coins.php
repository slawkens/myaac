<?php
$coins = ($db->hasColumn('accounts', 'coins') ?  $db->query('SELECT `coins`, `' . (USE_ACCOUNT_NAME ? 'name' : 'id') . '` as `name` FROM `accounts` ORDER BY `coins` DESC LIMIT 10;') : 0);

$twig->display('coins.html.twig', array(
	'coins' => $coins
));
