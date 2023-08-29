<?php

use MyAAC\Models\Account;
use MyAAC\Models\Guild;
use MyAAC\Models\House;
use MyAAC\Models\Monster;
use MyAAC\Models\Player;

defined('MYAAC') or die('Direct access not allowed!');
$count = $eloquentConnection->query()
	->select([
		'total_accounts' => Account::selectRaw('COUNT(id)'),
		'total_players' => Player::selectRaw('COUNT(id)'),
		'total_guilds' => Guild::selectRaw('COUNT(id)'),
		'total_monsters' => Monster::selectRaw('COUNT(id)'),
		'total_houses' => House::selectRaw('COUNT(id)'),
	])->first();

$twig->display('statistics.html.twig', array(
	'count' => $count,
));
