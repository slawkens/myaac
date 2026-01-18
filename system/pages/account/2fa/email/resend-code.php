<?php
defined('MYAAC') or die('Direct access not allowed!');

require __DIR__ . '/../base.php';

if ($twoFactorAuth->hasRecentEmailCode(1 * 60)) {
	$errors = ['Sorry, one email per 15 minutes'];
}
else {
	$twoFactorAuth->resendEmailCode();
}

if (!empty($errors)) {
	$twig->display('error_box.html.twig',  ['errors' => $errors]);
}

$twig->display('account/2fa/email/login.html.twig');
