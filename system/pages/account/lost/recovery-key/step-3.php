<?php

use MyAAC\Models\Account as AccountModel;

defined('MYAAC') or die('Direct access not allowed!');

csrfProtect();

$title = 'Lost Account';

$key = $_POST['key'];
$newPassword = $_POST['password'] ?? '';
$passwordRepeat = $_POST['password_repeat'] ?? '';
$newEmail = $_POST['email'] ?? '';

require __DIR__ . '/../_base.php';

if(!$accountLost) {
	$errors[] = "Player or account of player <b>" . escapeHtml($character) . "</b> doesn't exist.";
}
else {
	$accountKey = $accountLost->key;

	if(!empty($accountKey)) {
		if($accountKey == $key) {
			if(Validator::password($newPassword)) {
				if ($newPassword == $passwordRepeat) {
					if (Validator::email($newEmail)) {
						$emailExists = AccountModel::where('email', $newEmail)->exists();
						if (!$emailExists) {

							$hooks->trigger(HOOK_ACCOUNT_LOST_RECOVERY_KEY_STEP_3_POST);

							if (empty($errors)) {
								$accountLost->email = $newEmail;

								$tmp_new_pass = $newPassword;
								if (USE_ACCOUNT_SALT) {
									$salt = generateRandomString(10, false, true, true);
									$tmp_new_pass = $salt . $newPassword;
								}

								$accountLost->password = encrypt($tmp_new_pass);

								if (USE_ACCOUNT_SALT) {
									$accountLost->salt = $salt;
								}

								$accountLost->save();

								$statusMsg = '';
								if ($accountLost->email_next < time()) {
									$mailBody = $twig->render('mail.account.lost.new-email.html.twig', [
										'account' => $accountLost,
										'newPassword' => $newPassword,
										'newEmail' => $newEmail,
									]);

									if (_mail($accountLost->email, configLua('serverName') . ' - New password to your account', $mailBody)) {
										$statusMsg = '<br /><small>Sent e-mail with your account name and password to new e-mail. You should receive this e-mail in 15 minutes. You can login now with new password!</small>';
									} else {
										$statusMsg = '<br /><p class="error">An error occurred while sending email! You will not receive e-mail with this informations. For Admin: More info can be found in system/logs/mailer-error.log</p>';
									}
								} else {
									$statusMsg = '<br /><small>You will not receive e-mail with this informations.</small>';
								}

								$twig->display('account/lost/finish.new-email.html.twig', [
									'statusMsg' => $statusMsg,
									'account' => $accountLost,
									'newPassword' => $newPassword,
									'newEmail' => $newEmail,
								]);

								return;
							}
						}
						else {
							$errors[] = 'This email is already registered!';
						}
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

if (!empty($errors)) {
	$twig->display('error_box.html.twig', [
		'errors' => $errors,
	]);
}

$twig->display('account.back_button.html.twig', [
	'new_line' => true,
	'center' => true,
	'action' => getLink('account/lost/recovery-key/step-2') . '?character=' . urlencode($character) . '&key=' . urlencode($key),
]);
