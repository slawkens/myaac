<?php
defined('MYAAC') or die('Direct access not allowed!');

require __DIR__ . '/../base.php';

$account = \MyAAC\Models\Account::find($account_logged->getId());
if (!$account) {
	error('Account not found!');
	return;
}

if ($db->hasColumn('accounts', 'secret')) {
	$account->secret = NULL;
}

$account->{'2fa_secret'} = '';
$account->save();

$twoFactorAuth->disable();

$twig->display('success.html.twig', [
	'title' => 'Disabled',
	'description' => 'Two Factor Authentication has been disabled.'
]);
