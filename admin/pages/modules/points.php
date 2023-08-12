<?php

use MyAAC\Models\Account;

defined('MYAAC') or die('Direct access not allowed!');

$points = 0;

if ($db->hasColumn('accounts', 'premium_points')) {
	$coins = Account::orderByDesc('premium_points')->limit(10)->get(['premium_points', (USE_ACCOUNT_NAME ? 'name' : 'id')])->toArray();
}

$twig->display('points.html.twig', array(
	'points' => $points,
));
