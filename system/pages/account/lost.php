<?php
/**
 * Lost account
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

use MyAAC\Models\Account;
use MyAAC\Models\Player;

$title = 'Lost Account';

/**
 * @var \Twig\Environment $twig
 * @var array $errors
 */
csrfProtect();

if(!setting('core.mail_enabled')) {
	echo "<b>Account maker is not configured to send e-mails, you can't use Lost Account Interface. Contact with admin to get help.</b>";
	return;
}

$emailOrCharacter = $_POST['emailOrCharacter'] ?? '';

$step = $_POST['step'] ?? null;
if (empty($step)) {
	$twig->display('account/lost/welcome.html.twig');
	return;
}

if (empty($emailOrCharacter)) {
	$errors[] = 'Please enter the name of a character on the lost account. If your account does not contain any characters, please create a new account.';
}
elseif (str_contains($emailOrCharacter, '@')) {
	$account = Account::where('email', $emailOrCharacter)->first();
	if (!$account) {
		$errors[] = "Email address <strong>$emailOrCharacter</strong> does not exist. Please make sure to enter the email address correctly. Note that email addresses may be deleted automatically if they have not been used for a long time.";
	}
}
else {
	$player = Player::where('name', $emailOrCharacter)->first();
	if (!$player) {
		$errors[] = "Character with name <strong>$emailOrCharacter</strong> does not exist. Please make sure to enter the character name correctly. Note that characters may be deleted automatically if they have not been used for a long time.";
	}
	else {
		$account = $player->account()->first();
		setSession('account_2fa_id', $account->getKey());
	}
}

if (!empty($errors)) {
	$twig->display('error_box.html.twig', ['errors' => $errors]);

	$twig->display('account.back_button.html.twig', [
		'new_line' => true,
		'center' => true,
		'action' => getLink('account/lost')
	]);

	return;
}

$explodeRecoveryKey = explode('-', $account->key);
$newRecoveryKeyFormat = (count($explodeRecoveryKey) == 4);

switch ($step) {
	case 'problem':
		if (empty($errors)) {
			$twig->display('account/lost/problem.html.twig', [
				'email' => str_contains($emailOrCharacter, '@') ? $emailOrCharacter : '',
				'character' => !str_contains($emailOrCharacter, '@') ? $emailOrCharacter : '',
				'emailOrCharacter' => $emailOrCharacter,
				//'errors' => $errors,
			]);
		}

		break;

	case 'auth':
		$twig->display('account/lost/auth.html.twig', [
			'newRecoveryKeyFormat' => $newRecoveryKeyFormat,
			'emailOrCharacter' => $emailOrCharacter,
		]);

		break;

	case 'auth-remove-by-key':
		if ($newRecoveryKeyFormat) {
			$key = $_POST['key1'] . '-' . $_POST['key2'] . '-' . $_POST['key3'] . '-' . $_POST['key4'];
		}
		else {
			$key = $_POST['key'];
		}

		require __DIR__ . '/2fa/base.php';

		if (strlen($key) < 4) {
			$errors[] = 'Please enter the recovery key!';
		}
		elseif (!setting('core.mail_enabled') || !setting('core.account_2fa_email')) {
			$errors[] = 'Account Two-Factor E-Mail Authentication disabled.';
		}
		elseif (!isRequestMethod('post')) {
			$errors[] = 'This page cannot be accessed directly.';
		}
		elseif (!$twoFactorAuth->isActive($twoFactorAuth::TYPE_APP)) {
			$errors[] = 'There is no two-factor authenticator app activated for your account.';
		}

		$accountKey = $account->key;
		if (!empty($key) && $key == $accountKey) {
			$twoFactorAuth->disable();
			$twoFactorAuth->deleteOldCodes();

			$twig->display('success.html.twig',
				[
					'title' => 'Authenticator App Disabled',
					'description' => 'You have successfully <strong>disabled</strong> the <b>Authenticator App</b> for your account.'
				]
			);

			return;
		}
		elseif (empty($errors)) {
			$errors[] = 'You have entered an incorrect recovery key! Please enter a valid recovery key.';
		}

		if (!empty($errors)) {
			$twig->display('error_box.html.twig',  ['errors' => $errors]);
		}

		$twig->display('account/lost/auth.html.twig', [
			'newRecoveryKeyFormat' => $newRecoveryKeyFormat,
			'key' => $key,
			'emailOrCharacter' => $emailOrCharacter,
			'errors' => $errors,
		]);

		break;

	case 'reset':
		$twig->display('account/lost/reset.html.twig');
		break;
	default:
		$twig->display('account/lost/welcome.html.twig');
		break;
}
