<?php
$balance = ($db->hasColumn('players', 'balance') ? $db->query('SELECT `balance`, `id`, `name`,`level` FROM `players` ORDER BY `balance` DESC LIMIT 10;') : 0);

$twig->display('balance.html.twig', array(
	'balance' => $balance
));
