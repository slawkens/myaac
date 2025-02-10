<?php

use MyAAC\Models\Account;

defined('MYAAC') or die('Direct access not allowed!');

$accounts = 0;

if ($db->hasColumn('accounts', 'created')) {
	$accounts = Account::orderByDesc('created')->limit(10)->get(['id', 'created'])->toArray();
}

$twig->display('created.html.twig', array(
	'accounts' => $accounts,
));
