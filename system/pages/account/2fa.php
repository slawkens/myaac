<?php
/**
 * 2-factor authentication
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\TwoFactorAuth\TwoFactorAuth;

defined('MYAAC') or die('Direct access not allowed!');

$title = 'Two Factor Authentication';
require __DIR__ . '/base.php';

csrfProtect();

/**
 * @var OTS_Account $account_logged
 */
$step = isset($_REQUEST['step']) ?? '';

if ((!setting('core.mail_enabled')) && ACTION == 'email-code') {
	$twig->display('error_box.html.twig',  ['errors' => ['Account two-factor e-mail authentication disabled.']]);
	return;
}

$twoFactorAuth = new TwoFactorAuth($account_logged);

if (ACTION == 'email-code') {
	if ($step === 'verify') {
		$code = $_POST['email-code'] ?? '';
		if ($twoFactorAuth->getAuthGateway()->verifyCode($code)) {
			$twoFactorAuth->getAuthGateway()->deleteOldCodes();

			//session(['2fa_skip' => true]);
			header('Location: account/manage');
			exit;
		}
	}
	else if ($step == 'resend') {
		$twoFactorAuth->getAuthGateway()->resendEmailCode();

		$twig->display('account.2fa.email_code.html.twig');
	}
	else if ($step == 'confirm-activate') {
		$account2faCode = $account_logged->getCustomField('2fa_email_code');
		$account2faCodeTimeout = $account_logged->getCustomField('2fa_email_code_timeout');

		if (!empty($account2faCodeTimeout) && time() - (int)$account2faCodeTimeout < (24 * 60 * 60)) {
			$postCode = $_POST['email-code'] ?? '';
			if (!empty($account2faCode)) {
				if (!empty($postCode)) {
					if ($postCode == $account2faCode) {
						$twig->display('account.2fa.email-code.success.html.twig');
					}
				}
				else {

				}
			}
			else {
				$errors[] = 'Your account dont have 2fa E-Mail code sent.';

			}
		}
		else {
			$errors[] = 'E-Mail Code expired.';
		}
	}
}

if (!empty($errors)) {
	$twig->display('error_box.html.twig',  ['errors' => $errors]);
	$twig->display('account.back_button.html.twig', [
		'new_line' => true
	]);
}
