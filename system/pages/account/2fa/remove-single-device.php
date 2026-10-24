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
elseif (!TrustedDevice::get($account_logged->getId(), $_GET['device_id'] ?? 0)) {
	$errors[] = 'The selected trusted device does not exist.';
}

if (!empty($errors)) {
	$twig->display('error_box.html.twig', ['errors' => $errors]);
	return;
}

TrustedDevice::remove($account_logged->getId(), $_GET['device_id'] ?? 0);

$twig->display('success.html.twig', [
	'title' => 'Device Removed',
	'description' => 'The selected trusted device has been removed from your account.'
]);
