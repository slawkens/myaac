<?php
defined('MYAAC') or die('Direct access not allowed!');

require __DIR__ . '/../base.php';

if (!isRequestMethod('post')) {
	error('This page cannot be accessed directly.');
	return;
}

if (!$account_logged->isLoaded()) {
	error('Account not found!');
	return;
}

if (!$twoFactorAuth->isActive($twoFactorAuth::TYPE_APP)) {
	error("Your account does not have Two Factor App Authentication enabled.");
	return;
}

$twoFactorAuth->disable();

$twig->display('success.html.twig', [
	'title' => 'Disabled',
	'description' => 'Two Factor App Authentication has been disabled.'
]);
