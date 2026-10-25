<?php
/**
 * Disable Two-Factor Authentication using a recovery key.
 *
 * @var bool $newRecoveryKeyFormat Indicates if the new recovery key format is used.
 */
defined('MYAAC') or die('Direct access not allowed!');

use MyAAC\TwoFactorAuth\TwoFactorAuth;

$twoFactorAuth = TwoFactorAuth::getInstance($account->getKey());

if ($newRecoveryKeyFormat) {
	$key = $_POST['key1'] . '-' . $_POST['key2'] . '-' . $_POST['key3'] . '-' . $_POST['key4'];
}
else {
	$key = $_POST['key'];
}

if (($newRecoveryKeyFormat && strlen($key) < 4) || empty($key)) {
	$errors[] = 'Please enter the recovery key!';
}
elseif (!setting('core.mail_enabled') || (!setting('core.account_2fa_email') && !setting('core.account_2fa_app'))) {
	$errors[] = 'Account Two-Factor Authentication disabled.';
}
elseif (!$twoFactorAuth->isActive()) {
	$errors[] = 'Two-Factor Authentication is not active for your account.';
}

$accountKey = $account->key;
if (empty($errors) && !empty($key) && $key == $accountKey) {
	$isEmail2FA = $twoFactorAuth->isActive($twoFactorAuth::TYPE_EMAIL);

	$twoFactorAuth->disable();
	$twoFactorAuth->deleteOldCodes();

	$twig->display('success.html.twig',
		[
			'title' => 'Two-Factor Authentication Disabled',
			'description' => 'You have successfully <strong>disabled</strong> the Two-Factor <b>'
				. ($isEmail2FA ? 'Email Code Authentication' : 'App Authentication')
				 . '</b> for your account.'
		]
	);

	return;
}
elseif (empty($errors)) {
	$errors[] = 'You have entered an incorrect recovery key! Please enter a valid recovery key.';
}

$twig->display('error_box.html.twig',  ['errors' => $errors]);

$twig->display('account/lost/auth.html.twig', [
	'newRecoveryKeyFormat' => $newRecoveryKeyFormat,
	'key' => $key,
	'character' => $character,
	'errors' => $errors,
	'step' => $_POST['type'] ?? '',
]);
