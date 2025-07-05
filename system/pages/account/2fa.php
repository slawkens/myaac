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
$step = $_REQUEST['step'] ?? '';
$code = $_REQUEST['email-code'] ?? '';

if ((!setting('core.mail_enabled')) && ACTION == 'email-code') {
	$twig->display('error_box.html.twig',  ['errors' => ['Account two-factor e-mail authentication disabled.']]);
	return;
}

if (!isset($account_logged) || !$account_logged->isLoaded()) {
	$current_session = getSession('account');
	if($current_session) {
		$account_logged->load($current_session);
	}
}

$twoFactorAuth = TwoFactorAuth::getInstance($account_logged);

if (ACTION == 'email-code') {
	if ($step == 'resend') {
		if ($twoFactorAuth->hasRecentEmailCode(15 * 60)) {
			$errors = ['Sorry, one email per 15 minutes'];
		}
		else {
			$twoFactorAuth->resendEmailCode();
		}

		if (!empty($errors)) {
			$twig->display('error_box.html.twig',  ['errors' => $errors]);
		}

		$twig->display('account.2fa.email.login.html.twig');
	}
	else if ($step == 'activate') {
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

		$twig->display('account.2fa.email_code.html.twig', ['wrongCode' => count($errors) > 0]);
	}
	else if ($step == 'deactivate') {
		if (!$twoFactorAuth->hasRecentEmailCode(15 * 60)) {
			$twoFactorAuth->resendEmailCode();
		}

		if (isset($_POST['save'])) {
			if (!empty($code)) {
				if ($twoFactorAuth->getAuthGateway()->verifyCode($code)) {

					$twoFactorAuth->disable();
					$twoFactorAuth->deleteOldCodes();

					$twig->display('success.html.twig',
						[
							'title' => 'Email Code Authentication Deactivated',
							'description' => 'You have successfully <b>deactivated</b> the <b>Email Code Authentication</b> for your account.'
						]
					);

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

		$twig->display('account.2fa.email.deactivate.html.twig', ['wrongCode' => count($errors) > 0]);
	}
}
