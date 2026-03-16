<?php
defined('MYAAC') or die('Direct access not allowed!');

require __DIR__ . '/../base.php';

if ((!setting('core.mail_enabled'))) {
	$twig->display('error_box.html.twig',  ['errors' => ['Account Two-Factor E-Mail Authentication disabled.']]);
	return;
}

if (!isRequestMethod('post')) {
	error('This page cannot be accessed directly.');
	return;
}

if (!$account_logged->isLoaded()) {
	error('Account not found!');
	return;
}

if (!$twoFactorAuth->isActive($twoFactorAuth::TYPE_EMAIL)) {
	error("Your account does not have Two Factor E-Mail Authentication enabled.");
	return;
}

$twoFactorAuth->disable();
$twoFactorAuth->deleteOldCodes();

$twig->display('success.html.twig',
	[
		'title' => 'Email Code Authentication Disabled',
		'description' => 'You have successfully <strong>disabled</strong> the <b>Email Code Authentication</b> for your account.'
	]
);
