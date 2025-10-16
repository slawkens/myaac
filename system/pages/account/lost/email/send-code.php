<?php
defined('MYAAC') or die('Direct access not allowed!');

csrfProtect();

require __DIR__ . '/../base.php';

$title = 'Lost Account';

$email = $_REQUEST['email'];
$nick = stripslashes($_REQUEST['nick']);

$player = new OTS_Player();
$account = new OTS_Account();
$player->find($nick);
if($player->isLoaded()) {
	$account = $player->getAccount();
}

if($account->isLoaded()) {
	if($account->getCustomField('email_next') < time()) {
		if($account->getEMail() == $email) {
			$newCode = generateRandomString(30, true, false, true);
			$mailBody = $twig->render('mail.account.lost.code.html.twig', [
				'newCode' => $newCode,
				'account' => $account,
				'nick' => $nick,
			]);

			$accountEMail = $account->getCustomField('email');
			if(_mail($accountEMail, configLua('serverName') . ' - Recover your account', $mailBody)) {
				$account->setCustomField('email_code', $newCode);
				$account->setCustomField('email_next', (time() + setting('core.mail_lost_account_interval')));

				$twig->display('success.html.twig', [
					'title' => 'Email has been sent',
					'description' => 'Details about steps required to recover your account has been sent to <b>' . $accountEMail . '</b>. You should receive this email within 15 minutes. Please check your inbox/spam directory.',
					'custom_buttons' => '',
				]);
			}
			else {
				$account->setCustomField('email_next', (time() + 60));
				error('An error occurred while sending email! Try again later or contact with admin. For Admin: More info can be found in system/logs/mailer-error.log</p>');
			}
		}
		else {
			$errors[] = 'Invalid e-mail to account of character <b>' . escapeHtml($nick) . '</b>. Try again.';
		}
	}
	else {
		lostAccountWriteCooldown($nick, (int)$account->getCustomField('email_next'));
	}
}
else {
	$errors[] =  "Player or account of player <b>" . escapeHtml($nick) . "</b> doesn't exist.";
}

if (!empty($errors)) {
	$twig->display('error_box.html.twig', [
		'errors' => $errors,
	]);
}

$twig->display('account.back_button.html.twig', [
	'new_line' => true,
	'center' => true,
	'action' => getLink('account/lost/step-1') . '?action=email&nick=' . urlencode($nick),
]);
