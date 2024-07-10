<?php
$recKey = trim($_REQUEST['key']);
$nick = stripslashes($_REQUEST['nick']);
$newPassword = trim($_REQUEST['passor']);
$newEmail = trim($_REQUEST['email']);

$player = new OTS_Player();
$account = new OTS_Account();
$player->find($nick);
if($player->isLoaded()) {
	$account = $player->getAccount();
}

if($account->isLoaded())
{
	$accountKey = $account->getCustomField('key');
	if(!empty($accountKey)) {
		if($accountKey == $recKey) {
			if(Validator::password($newPassword)) {
				if(Validator::email($newEmail)) {
					$account->setEMail($newEmail);

					$tmp_new_pass = $newPassword;
					if(USE_ACCOUNT_SALT)
					{
						$salt = generateRandomString(10, false, true, true);
						$tmp_new_pass = $salt . $newPassword;
					}

					$account->setPassword(encrypt($tmp_new_pass));
					$account->save();

					if(USE_ACCOUNT_SALT) {
						$account->setCustomField('salt', $salt);
					}

					$statusMsg = '';
					if($account->getCustomField('email_next') < time()) {
						$mailBody = $twig->render('mail.account.lost.new-email.html.twig', [
							'account' => $account,
							'newPassword' => $newPassword,
							'newEmail' => $newEmail,
						]);

						if(_mail($account->getCustomField('email'), $config['lua']['serverName']." - New password to your account", $mailBody)) {
							$statusMsg = '<br /><small>Sent e-mail with your account name and password to new e-mail. You should receive this e-mail in 15 minutes. You can login now with new password!</small>';
						}
						else {
							$statusMsg = '<br /><p class="error">An error occurred while sending email! You will not receive e-mail with this informations. For Admin: More info can be found in system/logs/mailer-error.log</p>';
						}
					}
					else {
						$statusMsg = '<br /><small>You will not receive e-mail with this informations.</small>';
					}

					$twig->display('account.lost.finish.new-email.html.twig', [
						'statusMsg' => $statusMsg,
						'account' => $account,
						'newPassword' => $newPassword,
						'newEmail' => $newEmail,
					]);
				}
				else {
					echo Validator::getLastError();
				}
			}
			else {
				echo Validator::getLastError();
			}
		}
		else {
			echo 'Wrong recovery key!';
		}
	}
	else {
		echo 'Account of this character has no recovery key!';
	}
}
else {
	echo "Player or account of player <b>" . htmlspecialchars($nick) . "</b> doesn't exist.";
}

$twig->display('account.back_button.html.twig', [
	'new_line' => true,
	'center' => true,
	'action' => getLink('account/lost') . '?action=step1&action_type=reckey&nick=' . urlencode($nick),
]);
