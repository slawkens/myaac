<?php
defined('MYAAC') or die('Direct access not allowed!');

/**
 * @var \MyAAC\TwoFactorAuth\TwoFactorAuth $twoFactorAuth
 */
require __DIR__ . '/_base.php';

if (!isRequestMethod('post')) {
	$errors[] = 'This page cannot be accessed directly.';
}
elseif (!$account_logged->isLoaded()) {
	$errors[] = 'Please login first.';
}
elseif (!$twoFactorAuth->isActive()) {
	$errors[] = 'Your account does not have Two Factor Authentication enabled.';
}
else {
	$isEmailAuth = $twoFactorAuth->isActive($twoFactorAuth::TYPE_EMAIL);

	if ($isEmailAuth && (!setting('core.mail_enabled') || !setting('core.account_2fa_email'))
		|| (!$isEmailAuth && !setting('core.account_2fa_app'))) {
		$errors[] = 'Account Two-Factor Authentication is disabled.';
	}
}

if (!empty($errors)) {
	$twig->display('error_box.html.twig',  ['errors' => $errors]);
	return;
}

$twoFactorAuth->disable();
$twoFactorAuth->deleteOldCodes();

$twig->display('success.html.twig',
	[
		'title' => 'Two-Factor Authentication Disabled',
		'description' => 'You have successfully <strong>disabled</strong> the Two-Factor <b>'
				. ($isEmailAuth ? 'Email Code Authentication' : 'App Authentication')
				 . '</b> for your account.'
	]
);
