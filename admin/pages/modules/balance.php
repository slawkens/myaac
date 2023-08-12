<?php

use MyAAC\Models\Player;

defined('MYAAC') or die('Direct access not allowed!');

$balance = 0;

if ($db->hasColumn('players', 'balance')) {
	$balance = Player::orderByDesc('balance')->limit(10)->get(['balance', 'id','name', 'level'])->toArray();
}

$twig->display('balance.html.twig', array(
	'balance' => $balance
));
