<?php
defined('MYAAC') or die('Direct access not allowed!');

require __DIR__ . '/../base.php';

if (!$account_logged->isLoaded()) {
	error('Account not found!');
	return;
}

$twoFactorAuth->disable();

$twig->display('success.html.twig', [
	'title' => 'Disabled',
	'description' => 'Two Factor Authentication has been disabled.'
]);
