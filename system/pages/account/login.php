<?php
/**
 * Login
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2023 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\RateLimit;

defined('MYAAC') or die('Direct access not allowed!');

// new login with data from form
if($logged || !isset($_POST['account_login']) || !isset($_POST['password_login'])) {
	return;
}

$login_account = $_POST['account_login'];
$login_password = $_POST['password_login'];
$remember_me = isset($_POST['remember_me']);
$ip = get_browser_real_ip();
if(!empty($login_account) && !empty($login_password))
{

	$limiter = new RateLimit('failed_logins', setting('core.account_login_attempts_limit'), setting('core.account_login_ban_time'));
	$limiter->enabled = setting('core.account_login_ipban_protection');
	$limiter->load();

	$account_logged = new OTS_Account();
	if (config('account_login_by_email')) {
		$account_logged->findByEMail($login_account);
	}

	if (!config('account_login_by_email') || config('account_login_by_email_fallback')) {
		if(USE_ACCOUNT_NAME || USE_ACCOUNT_NUMBER) {
			$account_logged->find($login_account);
		} else {
			$account_logged->load($login_account, true);
		}
	}

	if($account_logged->isLoaded() && encrypt((USE_ACCOUNT_SALT ? $account_logged->getCustomField('salt') : '') . $login_password) == $account_logged->getPassword() && (!$limiter->enabled || !$limiter->exceeded($ip))
	)
	{
		if (setting('core.account_mail_verify') && (int)$account_logged->getCustomField('email_verified') !== 1) {
			$errors[] = 'Your account is not verified. Please verify your email address. If the message is not coming check the SPAM folder in your E-Mail client.';
		} else {
			session_regenerate_id();
			setSession('account', $account_logged->getId());
			setSession('password', encrypt((USE_ACCOUNT_SALT ? $account_logged->getCustomField('salt') : '') . $login_password));
			if($remember_me) {
				setSession('remember_me', true);
			}

			$logged = true;
			$logged_flags = $account_logged->getWebFlags();

			if(isset($_POST['admin']) && !admin()) {
				$errors[] = 'This account has no admin privileges.';
				unsetSession('account');
				unsetSession('password');
				unsetSession('remember_me');
				$logged = false;
			}
			else {
				$account_logged->setCustomField('web_lastlogin', time());
			}

			$hooks->trigger(HOOK_LOGIN, array('account' => $account_logged, 'password' => $login_password, 'remember_me' => $remember_me));
		}

		$limiter->reset($ip);
	}
	else
	{
		$hooks->trigger(HOOK_LOGIN_ATTEMPT, array('account' => $login_account, 'password' => $login_password, 'remember_me' => $remember_me));

		$errorMessage = getAccountLoginByLabel() . ' or password is not correct.';
		$limiter->increment($ip);
		if ($limiter->exceeded($ip)) {
			$errorMessage = 'A wrong password has been entered ' . $limiter->max_attempts . ' times in a row. You are unable to log into your account for the next ' . $limiter->ttl . ' minutes. Please wait.';
		}

		$errors[] = $errorMessage;

	}
}
else {
	$errors[] = 'Please enter your ' . getAccountLoginByLabel() . ' and password.';

	$hooks->trigger(HOOK_LOGIN_ATTEMPT, array('account' => $login_account, 'password' => $login_password, 'remember_me' => $remember_me));
}

$hooks->trigger(HOOK_ACCOUNT_LOGIN_POST);
