<?php
$players = ($db->hasColumn('players', 'lastlogin') ? $db->query('SELECT name, level, lastlogin FROM players ORDER BY lastlogin DESC LIMIT 10;') : 0);
$twig->display('lastlogin.html.twig', array(
	'players' => $players,
));
