<?php
defined('MYAAC') or die('Direct access not allowed!');

require __DIR__ . '/../base.php';

$from = $_POST['from'] ?? 'login';
if (!setting('core.mail_enabled') || !setting('core.account_2fa_email')) {
	$errors[] = 'Account Two-Factor E-Mail Authentication disabled.';
}
elseif (!$account_logged->isLoaded()) {
	$errors[] = 'Please login first.';
}
elseif ($twoFactorAuth->isActive()) {
	$errors[] = 'Two-factor authentication is already enabled on your account';
}
elseif ($twoFactorAuth->hasRecentEmailCode(30 * 60)) {
	$errors[] = 'Sorry, one email per 30 minutes';
}
elseif (!in_array($from, ['login', 'enable'])) {
	$errors[] = 'Invalid request!';
}

if (!empty($errors)) {
	$twig->display('error_box.html.twig',  ['errors' => $errors]);
	return;
}

$twoFactorAuth->resendEmailCode();
success('E-Mail code sent! Please check your inbox and SPAM folder.');

$login_account = $_POST['account_login'] ?? '';
$login_password = $_POST['password_login'] ?? '';
$remember_me = isset($_POST['remember_me']);

$twig->display("account/2fa/email/$from.html.twig", [
	'account_login' => $login_account,
	'password_login' => $login_password,
	'remember_me' => $remember_me,
]);
