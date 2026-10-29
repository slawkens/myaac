<?php
defined('MYAAC') or die('Direct access not allowed!');

use MyAAC\Models\Player;

csrfProtect();

$title = 'Lost Account';

$newPassword = $_POST['password'] ?? '';
$passwordRepeat = $_POST['password_repeat'] ?? '';
$code = $_POST['code'] ?? '';
$character = $_POST['character'] ?? '';

if(empty($code) || empty($character)) {
	$errors[] = 'Please enter code from e-mail and name of one character from account.';

	$twig->display('error_box.html.twig', [
		'errors' => $errors,
	]);

	$twig->display('account/lost/check-code.html.twig', [
		'code' => $code,
		'character' => $character,
	]);

	$twig->display('account.back_button.html.twig', [
		'new_line' => true,
		'center' => true,
		'action' => getLink('account/lost/check-code')
	]);

	return;
}

if (empty($newPassword) || empty($passwordRepeat)) {
	$errors[] = 'Please enter both passwords.';

	$twig->display('error_box.html.twig', [
		'errors' => $errors,
	]);

	$twig->display('account/lost/check-code.finish.html.twig', [
		'character' => $character,
		'code' => $code,
	]);

	return;
}

$player = Player::where('name', $character)->first();
$account = null;
if($player) {
	$account = $player->account()->first();
}

$passwordFailed = false;

if($account) {
	if($account->email_code == $code) {
		if ($newPassword == $passwordRepeat) {
			if (Validator::password($newPassword)) {

				$hooks->trigger(HOOK_ACCOUNT_LOST_EMAIL_SET_NEW_PASSWORD_POST);

				if (empty($errors)) {
					$tmp_new_pass = $newPassword;
					if (USE_ACCOUNT_SALT) {
						$salt = generateRandomString(10, false, true, true);
						$tmp_new_pass = $salt . $newPassword;
						$account->salt = $salt;
					}

					$account->password = encrypt($tmp_new_pass);
					$account->email_code = '';
					$account->save();

					$mailBody = $twig->render('mail.account.lost.new-password.html.twig', [
						'account' => $account,
						'newPassword' => $newPassword,
					]);

					$statusMsg = '';
					if (_mail($account->email, configLua('serverName') . ' - Your new password', $mailBody)) {
						$statusMsg = '<br /><small>New password work! Sent e-mail with your password and account name. You should receive this e-mail in 15 minutes. You can login now with new password!';
					} else {
						$statusMsg = '<br /><p class="error">New password work! An error occurred while sending email! You will not receive e-mail with new password. For Admin: More info can be found in system/logs/mailer-error.log';
					}

					$twig->display('account/lost/finish.new-password.html.twig', [
						'statusMsg' => $statusMsg,
						'newPassword' => $newPassword,
					]);
				}
			} else {
				$passwordFailed = true;
				$errors[] = Validator::getLastError();
			}
		}
		else {
			$passwordFailed = true;
			$errors[] = 'Passwords are not the same!';
		}
	}
	else {
		$errors[] = 'Wrong code to change password.';
	}
}
else {
	$errors[] = "Account of this character or this character doesn't exist.";
}

if(!empty($errors)) {
	$twig->display('error_box.html.twig', [
		'errors' => $errors,
	]);

	echo '<br/>';

	$template = 'account/lost/check-code.html.twig';
	if($passwordFailed) {
		$template = 'account/lost/check-code.finish.html.twig';
	}

	$twig->display($template, [
		'code' => $code,
		'character' => $character,
	]);
}
