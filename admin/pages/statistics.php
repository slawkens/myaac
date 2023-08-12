<?php
/**
 * Statistics
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\Account;
use MyAAC\Models\Guild;
use MyAAC\Models\House;
use MyAAC\Models\Player;

defined('MYAAC') or die('Direct access not allowed!');
$title = 'Statistics';

$total_accounts = Account::count();
$total_players = Player::count();
$total_guilds = Guild::count();
$total_houses = House::count();

$points = Account::select(['premium_points', (USE_ACCOUNT_NAME ? 'name' : 'id')])
	->orderByDesc('premium_points')
	->limit(10)
	->get()
	->toArray();

$twig->display('admin.statistics.html.twig', array(
	'total_accounts' => $total_accounts,
	'total_players' => $total_players,
	'total_guilds' => $total_guilds,
	'total_houses' => $total_houses,
	'account_type' => (USE_ACCOUNT_NAME ? 'name' : 'number'),
	'points' => $points
));
