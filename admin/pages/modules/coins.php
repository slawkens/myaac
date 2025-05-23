<?php

use MyAAC\Models\Account;

defined('MYAAC') or die('Direct access not allowed!');

$coins = 0;

if ($db->hasColumn('accounts', 'coins')) {
	$whatToGet = ['id', 'coins'];
	if (USE_ACCOUNT_NAME) {
		$whatToGet[] = 'name';
	}

	$coins = Account::orderByDesc('coins')->limit(10)->get($whatToGet)->toArray();
}

$twig->display('coins.html.twig', array(
	'coins' => $coins
));
