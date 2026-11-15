<?php
defined('MYAAC') or die('Direct access not allowed!');

csrfProtect();

use MyAAC\Models\Account;
use MyAAC\Models\Player;

$title = 'Lost Account';

$character = $_REQUEST['character'] ?? '';
$email = $_REQUEST['email'] ?? '';

$accountLost = Account::where('email', $email)->first();
$playerLost = Player::where('name', $character)->notDeleted()->first();

if (!$accountLost || !$playerLost || $playerLost->account_id !== $accountLost->id) {
	$errors[] = "Player or account of player <b>" . escapeHtml($character) . "</b> doesn't match the provided email.";
}
elseif (!$accountLost) {
	$errors[] =  "Invalid player name or E-Mail.";
}
elseif ($accountLost->email_next >= time()) {
	$inSec = (int)$accountLost->email_next - time();
	$minutesLeft = floor($inSec / 60);
	$secondsLeft = $inSec - ($minutesLeft * 60);
	$timeLeft = "$minutesLeft minutes $secondsLeft seconds";

	$timeRounded = ceil(setting('core.mail_lost_account_interval') / 60);

	$twig->display('error_box.html.twig', [
		'errors' => ["Account of selected character (<b>" . escapeHtml($character) . "</b>) received e-mail in last $timeRounded minutes. You must wait $timeLeft before you can use Lost Account Interface again."]
	]);
}
elseif ($accountLost->email != $email) {
	$errors[] = 'Invalid e-mail to account of character <b>' . escapeHtml($character) . '</b>. Try again.';
}
else {
	$newCode = generateRandomString(30, true, false, true);
	$mailBody = $twig->render('mail.account.lost.code.html.twig', [
		'newCode' => $newCode,
		'account' => $accountLost,
		'character' => $character,
	]);

	$accountEMail = $accountLost->email;
	if(_mail($accountEMail, configLua('serverName') . ' - Recover your account', $mailBody)) {
		$accountLost->email_code = $newCode;
		$accountLost->email_next = (time() + setting('core.mail_lost_account_interval'));
		$accountLost->save();

		$twig->display('success.html.twig', [
			'title' => 'Email has been sent',
			'description' => 'Details about steps required to recover your account has been sent to <b>' . $accountEMail . '</b>. You should receive this email within 15 minutes. Please check your inbox/spam directory.',
			'custom_buttons' => '',
		]);

		$twig->display('account.back_button.html.twig', [
			'new_line' => true,
			'center' => true,
			'action' => getLink('news'),
		]);

		return;
	}

	$account->email_next =  (time() + 60);
	$account->save();
	error('An error occurred while sending email! Try again later or contact with admin. For Admin: More info can be found in system/logs/mailer-error.log</p>');
}

if (!empty($errors)) {
	$twig->display('error_box.html.twig', [
		'errors' => $errors,
	]);
}

$queryString = '';
if (!empty($character)) {
	$queryString .= '&character=' . urlencode($character);
}

if (!empty($email)) {
	$queryString .= '&email=' . urlencode($email);
}

$twig->display('account.back_button.html.twig', [
	'new_line' => true,
	'center' => true,
	'action' => getLink('account/lost/step-1') . '?action=email' . $queryString,
]);
