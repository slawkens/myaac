<?php

use MyAAC\Models\Account;

defined('MYAAC') or die('Direct access not allowed!');

$coins = 0;

if ($db->hasColumn('accounts', 'coins')) {
	$coins = Account::orderByDesc('coins')->limit(10)->get(['coins', (USE_ACCOUNT_NAME ? 'name' : 'id')])->toArray();
}

$twig->display('coins.html.twig', array(
	'coins' => $coins
));
