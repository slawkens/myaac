<?php

use MyAAC\Models\Player;

defined('MYAAC') or die('Direct access not allowed!');

$players = 0;

if ($db->hasColumn('players', 'lastlogin')) {
	$players = Player::orderByDesc('lastlogin')->limit(10)->get(['name', 'level', 'lastlogin'])->toArray();
}

$twig->display('lastlogin.html.twig', array(
	'players' => $players,
));
