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

$count->views = getDatabaseConfig('views_counter');

$visitors = new \MyAAC\Visitors(setting('core.visitors_counter_ttl'));
$count->visitors = $visitors->getAmountVisitors();

$args = [];
$args['elements'] = [
	'visitors' => [
		'order' => 10,
		'name' => 'Visitors',
		'title' => 'Amount of users currently browsing the site',
		'class' => 'bg-success',
		'icon' => 'fas fa-users',
		'link' => '?p=visitors',
		'value' => $count->visitors,
	],
	'views' => [
		'order' => 20,
		'name' => 'Views',
		'title' => 'Total number of page views',
		'class' => 'bg-success',
		'icon' => 'fas fa-chart-bar',
		'value' => $count->views,
	],
	'accounts' => [
		'order' => 30,
		'name' => 'Accounts',
		'title' => 'Total number of accounts',
		'class' => 'bg-info',
		'icon' => 'fas fa-user-plus',
		'link' => '?p=accounts',
		'value' => $count->total_accounts,
	],
	'players' => [
		'order' => 40,
		'name' => 'Players',
		'title' => 'Total number of players',
		'class' => 'bg-red',
		'icon' => 'fas fa-user-plus',
		'link' => '?p=players',
		'value' => $count->total_players,
	],
	'guilds' => [
		'order' => 50,
		'name' => 'Guilds',
		'title' => 'Total number of guilds',
		'class' => 'bg-green',
		'icon' => 'fas fa-chart-pie',
		'value' => $count->total_guilds,
	],
	'houses' => [
		'order' => 60,
		'name' => 'Houses',
		'title' => 'Total number of houses',
		'class' => 'bg-yellow',
		'icon' => 'fas fa-home',
		'value' => $count->total_houses,
	],
];

$hooks->triggerFilter(HOOK_FILTER_ADMIN_DASHBOARD_STATISTICS, $args);

uasort($args['elements'], function ($a, $b) {
	return $a['order'] <=> $b['order'];
});

$twig->display('statistics.html.twig', array(
	'elements' => $args['elements'],
	'edit' => $edit,
));
