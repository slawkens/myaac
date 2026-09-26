<?php
defined('MYAAC') or die('Direct access not allowed!');

require __DIR__ . '/../base.php';

if (!setting('core.mail_enabled') || !setting('core.account_2fa_email')) {
	$errors[] = 'Account Two-Factor E-Mail Authentication disabled.';
}
elseif (!isRequestMethod('post')) {
	$errors[] = 'This page cannot be accessed directly.';
}
elseif (!$account_logged->isLoaded()) {
	$errors[] = 'Please login first.';
}
elseif (!$twoFactorAuth->isActive($twoFactorAuth::TYPE_EMAIL)) {
	$errors[] = 'Your account does not have Two Factor E-Mail Authentication enabled.';
}

if (!empty($errors)) {
	$twig->display('error_box.html.twig',  ['errors' => $errors]);
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
