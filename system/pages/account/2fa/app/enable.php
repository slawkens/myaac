<?php
defined('MYAAC') or die('Direct access not allowed!');

use MyAAC\TwoFactorAuth\TwoFactorAuth;
use OTPHP\TOTP;
use Symfony\Component\Clock\NativeClock;

require __DIR__ . '/../base.php';

if (!empty($account_logged->getCustomField('2fa_secret'))) {

	$twig->display('account/2fa/app/enable.already_connected.html.twig');

	return;
}

if (ACTION == 'request') {
	$clock = new NativeClock();

	$secret = generateRandom2faSecret();

	$otp = TOTP::createFromSecret($secret);

	setSession('2fa_secret', $secret);

	$otp->setLabel($account_logged->getEmail());
	$otp->setIssuer(configLua('serverName'));

	$grCodeUri = $otp->getQrCodeUri(
		'https://api.qrserver.com/v1/create-qr-code/?data=[DATA]&size=200x200&ecc=M',
		'[DATA]'
	);

	$twig->display('account/2fa/app/enable.html.twig', [
		'grCodeUri' => $grCodeUri,
		'secret' => $secret,
	]);

	return;
}

if (ACTION == 'link') {
	$secret = getSession('2fa_secret');

	if ($secret === null) {
		$twig->display('error_box.html.twig', ['errors' => ['Secret not set']]);
	}

	$totp = $_POST['totp'] ?? '';
	if (!empty($totp)) {
		$otp = TOTP::createFromSecret($secret);

		$otp->setLabel($account_logged->getEmail());
		$otp->setIssuer(configLua('serverName'));

		if (!$otp->verify($totp)) {
			$grCodeUri = $otp->getQrCodeUri(
				'https://api.qrserver.com/v1/create-qr-code/?data=[DATA]&size=200x200&ecc=M',
				'[DATA]'
			);

			$twig->display('error_box.html.twig', ['errors' => ['Token is invalid!']]);

			$twig->display('account/2fa/app/enable.html.twig', [
				'grCodeUri' => $grCodeUri,
				'secret' => $secret,
			]);

			return;
		}

		if ($db->hasColumn('accounts', 'secret')) {
			$account_logged->setCustomField('secret', $secret);
		}

		$account_logged->setCustomField('2fa_secret', $secret);
		$twoFactorAuth->enable(TwoFactorAuth::TYPE_APP);

		$twig->display('success.html.twig',
			[
				'title' => 'Authenticator App Connected',
				'description' => 'You successfully connected your Tibia account to an authenticator app.'
			]
		);

		return;
	}
}

if (!empty($errors)) {
	$twig->display('error_box.html.twig', ['errors' => $errors]);
}

$twig->display('account/2fa/app/enable.warning.html.twig', ['wrongCode' => count($errors) > 0]);
