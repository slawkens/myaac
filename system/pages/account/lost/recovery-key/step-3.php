<?php
defined('MYAAC') or die('Direct access not allowed!');

csrfProtect();

$title = 'Lost Account';

$key = trim($_REQUEST['key']);
$nick = stripslashes($_REQUEST['nick']);
$newPassword = trim($_REQUEST['password']);
$passwordRepeat = trim($_REQUEST['password_repeat']);
$newEmail = trim($_REQUEST['email']);

$player = new OTS_Player();
$account = new OTS_Account();
$player->find($nick);
if($player->isLoaded()) {
	$account = $player->getAccount();
}

if($account->isLoaded()) {
	$accountKey = $account->getCustomField('key');

	if(!empty($accountKey)) {
		if($accountKey == $key) {
			if(Validator::password($newPassword)) {
				if ($newPassword == $passwordRepeat) {
					if (Validator::email($newEmail)) {
						$account->setEMail($newEmail);

						$tmp_new_pass = $newPassword;
						if (USE_ACCOUNT_SALT) {
							$salt = generateRandomString(10, false, true, true);
							$tmp_new_pass = $salt . $newPassword;
						}

						$account->setPassword(encrypt($tmp_new_pass));
						$account->save();

						if (USE_ACCOUNT_SALT) {
							$account->setCustomField('salt', $salt);
						}

						$statusMsg = '';
						if ($account->getCustomField('email_next') < time()) {
							$mailBody = $twig->render('mail.account.lost.new-email.html.twig', [
								'account' => $account,
								'newPassword' => $newPassword,
								'newEmail' => $newEmail,
							]);

							if (_mail($account->getCustomField('email'), configLua('serverName') . ' - New password to your account', $mailBody)) {
								$statusMsg = '<br /><small>Sent e-mail with your account name and password to new e-mail. You should receive this e-mail in 15 minutes. You can login now with new password!</small>';
							} else {
								$statusMsg = '<br /><p class="error">An error occurred while sending email! You will not receive e-mail with this informations. For Admin: More info can be found in system/logs/mailer-error.log</p>';
							}
						} else {
							$statusMsg = '<br /><small>You will not receive e-mail with this informations.</small>';
						}

						$twig->display('account/lost/finish.new-email.html.twig', [
							'statusMsg' => $statusMsg,
							'account' => $account,
							'newPassword' => $newPassword,
							'newEmail' => $newEmail,
						]);
					} else {
						$errors[] = Validator::getLastError();
					}
				}
				else {
					$errors[] = 'Passwords are not the same!';
				}
			}
			else {
				$errors[] = Validator::getLastError();
			}
		}
		else {
			$errors[] = 'Wrong recovery key!';
		}
	}
	else {
		$errors[] = 'Account of this character has no recovery key!';
	}
}
else {
	$errors[] = "Player or account of player <b>" . escapeHtml($nick) . "</b> doesn't exist.";
}

if (!empty($errors)) {
	$twig->display('error_box.html.twig', [
		'errors' => $errors,
	]);
}

$twig->display('account.back_button.html.twig', [
	'new_line' => true,
	'center' => true,
	'action' => getLink('account/lost/step-1') . '?action=recovery-key&nick=' . urlencode($nick),
]);
