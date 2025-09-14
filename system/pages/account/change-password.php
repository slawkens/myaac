<?php
/**
 * Change password
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

$title = 'Change Password';
require __DIR__ . '/base.php';

if(!$logged) {
	return;
}

csrfProtect();

$new_password = $_POST['newpassword'] ?? NULL;
$new_password_confirm = $_POST['newpassword_confirm'] ?? NULL;
$old_password = $_POST['oldpassword'] ?? NULL;
if(empty($new_password) && empty($new_password_confirm) && empty($old_password)) {
	$twig->display('account.change-password.html.twig');
}
else
{
	if(empty($new_password) || empty($new_password_confirm) || empty($old_password)){
		$errors[] = 'Please fill in form.';
	}
	$password_strlen = strlen($new_password);
	if($new_password != $new_password_confirm) {
		$errors[] = 'The new passwords do not match!';
	}

	if(empty($errors)) {
		if(!Validator::password($new_password)) {
			$errors[] = Validator::getLastError();
		}

		/** @var OTS_Account $account_logged */
		$old_password = encrypt((USE_ACCOUNT_SALT ? $account_logged->getCustomField('salt') : '') . $old_password);
		if($old_password != $account_logged->getPassword()) {
			$errors[] = 'Current password is incorrect!';
		}

		$hooks->trigger(HOOK_ACCOUNT_CHANGE_PASSWORD_POST);
	}

	if(!empty($errors)){
		//show errors
		$twig->display('error_box.html.twig', array('errors' => $errors));

		//show form
		$twig->display('account.change-password.html.twig');
	}
	else {
		$org_pass = $new_password;

		if(USE_ACCOUNT_SALT) {
			$salt = generateRandomString(10, false, true, true);
			$new_password = $salt . $new_password;
			$account_logged->setCustomField('salt', $salt);
		}

		$new_password = encrypt($new_password);
		$account_logged->setPassword($new_password);
		$account_logged->save();
		$account_logged->logAction('Account password changed.');

		$message = '';
		if(setting('core.mail_enabled') && setting('core.mail_send_when_change_password')) {
			$mailBody = $twig->render('mail.password_changed.html.twig', array(
				'new_password' => $org_pass,
				'ip' => get_browser_real_ip(),
			));

			if(_mail($account_logged->getEMail(), $config['lua']['serverName']." - Changed password", $mailBody)) {
				$message = '<br/><small>Your new password were send on email address <b>' . $account_logged->getEMail() . '</b>.</small>';
			}
			else {
				$message = '<br/><p class="error">An error occurred while sending email. For Admin: More info can be found in system/logs/mailer-error.log</p>';
			}
		}

		$twig->display('success.html.twig', array(
			'title' => 'Password Changed',
			'description' => 'Your password has been changed.' . $message
		));
		setSession('password', $new_password);
	}
}
