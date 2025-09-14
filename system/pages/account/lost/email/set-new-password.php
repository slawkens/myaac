<?php
defined('MYAAC') or die('Direct access not allowed!');

$title = 'Lost Account';

$newPassword = $_REQUEST['passor'];
$code = $_REQUEST['code'];
$character = stripslashes($_REQUEST['character']);

if(empty($code) || empty($character) || empty($newPassword)) {
	echo '<span style="color: red"><b>Error. Try again.</b></span><br/>Please enter code from e-mail and name of one character from account. Then press Submit.<br>';

	$twig->display('account.back_button.html.twig', [
		'new_line' => true,
		'center' => true,
		'action' => getLink('account/lost/check-code')
	]);
}
else
{
	$player = new OTS_Player();
	$account = new OTS_Account();
	$player->find($character);
	if($player->isLoaded()) {
		$account = $player->getAccount();
	}

	if($account->isLoaded()) {
		if($account->getCustomField('email_code') == $code) {
			if(Validator::password($newPassword)) {
				$tmp_new_pass = $newPassword;
				if(USE_ACCOUNT_SALT) {
					$salt = generateRandomString(10, false, true, true);
					$tmp_new_pass  = $salt . $newPassword;
					$account->setCustomField('salt', $salt);
				}

				$account->setPassword(encrypt($tmp_new_pass));
				$account->save();
				$account->setCustomField('email_code', '');

				$mailBody = $twig->render('mail.account.lost.new-password.html.twig', [
					'account' => $account,
					'newPassword' => $newPassword,
				]);

				$statusMsg = '';
				if(_mail($account->getCustomField('email'), configLua('serverName') . ' - Your new password', $mailBody)) {
					$statusMsg = '<br /><small>New password work! Sent e-mail with your password and account name. You should receive this e-mail in 15 minutes. You can login now with new password!';
				}
				else {
					$statusMsg = '<br /><p class="error">New password work! An error occurred while sending email! You will not receive e-mail with new password. For Admin: More info can be found in system/logs/mailer-error.log';
				}

				$twig->display('account/lost/finish.new-password.html.twig', [
					'statusMsg' => $statusMsg,
					'newPassword' => $newPassword,
				]);
			}
			else {
				$error = Validator::getLastError();
			}
		}
		else {
			$error = 'Wrong code to change password.';
		}
	}
	else {
		$error = "Account of this character or this character doesn't exist.";
	}
}

if(!empty($error)) {
	$twig->display('error_box.html.twig', [
		'errors' => [$error],
	]);

	echo '<br/>';

	$twig->display('account/lost/check-code.html.twig', [
		'code' => $code,
		'character' => $character,
	]);
}
