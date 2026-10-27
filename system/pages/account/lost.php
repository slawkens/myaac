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

$character = $_POST['character'] ?? '';

$step = $_POST['step'] ?? null;
if (empty($step)) {
	$twig->display('account/lost/welcome.html.twig');
	return;
}

if (!isRequestMethod('post')) {
	$errors[] = 'This page cannot be accessed directly.';
}
elseif (empty($character)) {
	$errors[] = 'Please enter the name of a character on the lost account. If your account does not contain any characters, please create a new account.';
}
elseif (str_contains($character, '@')) {
	$accountLost = Account::where('email', $character)->first();
	if (!$accountLost) {
		$errors[] = "Email address <strong>$character</strong> does not exist. Please make sure to enter the email address correctly. Note that email addresses may be deleted automatically if they have not been used for a long time.";
	}
}
else {
	$player = Player::where('name', $character)->first();
	if (!$player) {
		$errors[] = "Character with name <strong>$character</strong> does not exist. Please make sure to enter the character name correctly. Note that characters may be deleted automatically if they have not been used for a long time.";
	}
	else {
		$accountLost = $player->account()->first();
		setSession('account_two_factor_id', $accountLost->getKey());
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

$explodeRecoveryKey = explode('-', $accountLost->key);
$newRecoveryKeyFormat = (count($explodeRecoveryKey) == 4);

switch ($step) {
	case 'problem':
		if (isset($_POST['submitted']) && $_POST['submitted'] == '1') {
			$errors[] = 'Please select a problem.';
			$twig->display('error_box.html.twig', ['errors' => $errors]);
		}

		$twig->display('account/lost/problem.html.twig', [
			'character' => $character,
		]);

		break;

	case 'auth-app-remove':
	case 'auth-email-remove':
		$twig->display('account/lost/auth.html.twig', [
			'newRecoveryKeyFormat' => $newRecoveryKeyFormat,
			'character' => $character,
			'step' => $step,
		]);

		break;

	case 'auth-remove-by-key':
		require __DIR__ . '/lost/_auth-remove-by-key.php';

		break;

	case 'email':
		$twig->display('account/lost/email.html.twig', [
			'character' => $character,
		]);

		break;

	case 'email2':
		$twig->display('account/lost/email2.html.twig', [
			'character' => $character,
			'email' => $_POST['email'] ?? '',
		]);

		break;

	case 'email3':
		$twig->display('account/lost/email3.html.twig', [
			'character' => $character,
			'email' => $_POST['email'] ?? '',
		]);

		break;

	default:
		$twig->display('account/lost/welcome.html.twig');
		break;
}
