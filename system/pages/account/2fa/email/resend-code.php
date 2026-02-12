<?php
defined('MYAAC') or die('Direct access not allowed!');

require __DIR__ . '/../base.php';

if ((!setting('core.mail_enabled'))) {
	$twig->display('error_box.html.twig',  ['errors' => ['Account Two-Factor E-Mail Authentication disabled.']]);
	return;
}

if (!$account_logged->isLoaded()) {
	error('Account not found!');
	return;
}

if ($twoFactorAuth->isActive($twoFactorAuth::TYPE_APP)) {
	error('You have to disable the app auth first!');
	return;
}

if ($twoFactorAuth->hasRecentEmailCode(30 * 60)) {
	$errors = ['Sorry, one email per 30 minutes'];
}
else {
	$twoFactorAuth->resendEmailCode();
}

if (!empty($errors)) {
	$twig->display('error_box.html.twig',  ['errors' => $errors]);
}

$twig->display('account/2fa/email/enable.html.twig');
