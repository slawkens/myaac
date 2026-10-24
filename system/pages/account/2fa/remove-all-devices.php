<?php
/**
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2026 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

require __DIR__ . '/base.php';
use MyAAC\TwoFactorAuth\TrustedDevice;

if (!setting('core.mail_enabled') || (!setting('core.account_2fa_app') && !setting('core.account_2fa_email'))) {
	$errors[] = 'Two-factor authentication is not enabled on this server.';
}
elseif (!$account_logged->isLoaded()) {
	$errors[] = 'Please login first.';
}
elseif (!$twoFactorAuth->isActive()) {
	$errors[] = 'Two-factor authentication is not enabled on your account.';
}
elseif (!isRequestMethod('post')) {
	$errors[] = 'This page cannot be accessed directly.';
}

if (!empty($errors)) {
	$twig->display('error_box.html.twig', ['errors' => $errors]);
	return;
}

TrustedDevice::removeAll($account_logged->getId());

$twig->display('success.html.twig', [
	'title' => 'All Known Devices Removed',
	'description' => 'All trusted devices have been removed from your account.'
]);
