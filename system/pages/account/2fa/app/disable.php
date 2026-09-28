<?php
defined('MYAAC') or die('Direct access not allowed!');

use MyAAC\TwoFactorAuth\TwoFactorAuth;

require __DIR__ . '/../base.php';

if (!setting('core.account_2fa_app')) {
	$errors[] = 'Two-factor authentication is not enabled on this server.';
}
elseif (!isRequestMethod('post')) {
	$errors[] = 'This page cannot be accessed directly.';
}
elseif (!$account_logged->isLoaded()) {
	$errors[] = 'Please login first.';
}
elseif (!$twoFactorAuth->isActive(TwoFactorAuth::TYPE_APP)) {
	$errors[] = 'Your account does not have Two Factor App Authentication enabled.';
}

if (!empty($errors)) {
	$twig->display('error_box.html.twig', ['errors' => $errors]);
	return;
}

$twoFactorAuth->disable();

$twig->display('success.html.twig', [
	'title' => 'Two Factor App Authentication Disabled',
	'description' => 'You have successfully <strong>disabled</strong> the <b>Two Factor App Authentication</b> for your account.'
]);
