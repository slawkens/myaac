<?php

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

				echo '<br />Details about steps required to recover your account has been sent to <b>' . $accountEMail . '</b>. You should receive this email within 15 minutes. Please check your inbox/spam directory.';
			}
			else {
				$account->setCustomField('email_next', (time() + 60));
				error('An error occurred while sending email! Try again later or contact with admin. For Admin: More info can be found in system/logs/mailer-error.log</p>');
			}
		}
		else {
			echo 'Invalid e-mail to account of character <b>' . htmlspecialchars($nick) . '</b>. Try again.';
		}
	}
	else {
		$insec = (int)$account->getCustomField('email_next') - time();
		$minutesleft = floor($insec / 60);
		$secondsleft = $insec - ($minutesleft * 60);
		$timeleft = $minutesleft.' minutes '.$secondsleft.' seconds';

		echo 'Account of selected character (<b>' . htmlspecialchars($nick) . '</b>) received e-mail in last '.ceil(setting('core.mail_lost_account_interval') / 60) . ' minutes. You must wait '.$timeleft.' before you can use Lost Account Interface again.';
	}
}
else {
	echo "Player or account of player <b>" . htmlspecialchars($nick) . "</b> doesn't exist.";
}

$twig->display('account.back_button.html.twig', [
	'new_line' => true,
	'center' => true,
	'action' => getLink('account/lost') . '?action=step1&action_type=email&nick=' . urlencode($nick),
]);
