<?php

use MyAAC\TwoFactorAuth\TwoFactorAuth;

defined('MYAAC') or die('Direct access not allowed!');

require __DIR__ . '/../base.php';

if (!$twoFactorAuth->hasRecentEmailCode(15 * 60)) {
	$twoFactorAuth->resendEmailCode();
}

if (isset($_POST['save'])) {
	if (!empty($code)) {
		$twoFactorAuth->setAuthGateway(TwoFactorAuth::TYPE_EMAIL);
		if ($twoFactorAuth->getAuthGateway()->verifyCode($code)) {
			$serverName = configLua('serverName');

			$twoFactorAuth->enable(TwoFactorAuth::TYPE_EMAIL);
			$twoFactorAuth->deleteOldCodes();

			$twig->display('success.html.twig', [
				'title' => 'Email Code Authentication Activated',
				'description' => sprintf('You have successfully activated <b>email code authentication</b> for your account. This means an <b>email code</b> will be sent to the email address assigned to your account whenever you try to log in to the %s client or the %s website. In order to log in, you will need to enter the <b>most recent email code</b> you have received.', $serverName, $serverName)
			]);

			return;
		}
		else {
			$errors[] = 'Invalid email code!';
		}
	}
}

if (!empty($errors)) {
	$twig->display('error_box.html.twig', ['errors' => $errors]);
}

$twig->display('account/2fa/email/enable.html.twig', ['wrongCode' => count($errors) > 0]);
