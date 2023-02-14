<?php

// new login with data from form
if(!$logged && isset($_POST['account_login'], $_POST['password_login']))
{
	$login_account = $_POST['account_login'];
	$login_password = $_POST['password_login'];
	$remember_me = isset($_POST['remember_me']);
	if(!empty($login_account) && !empty($login_password))
	{
		if($cache->enabled())
		{
			$tmp = '';
			if($cache->fetch('failed_logins', $tmp))
			{
				$tmp = unserialize($tmp);
				$to_remove = array();
				foreach($tmp as $ip => $t)
				{
					if(time() - $t['last'] >= 5 * 60)
						$to_remove[] = $ip;
				}

				foreach($to_remove as $ip)
					unset($tmp[$ip]);
			}
			else
				$tmp = array();

			$ip = $_SERVER['REMOTE_ADDR'];
			$t = $tmp[$ip] ?? null;
		}

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

		if($account_logged->isLoaded() && encrypt((USE_ACCOUNT_SALT ? $account_logged->getCustomField('salt') : '') . $login_password) == $account_logged->getPassword()
			&& (!isset($t) || $t['attempts'] < 5)
		)
		{
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
		else
		{
			$hooks->trigger(HOOK_LOGIN_ATTEMPT, array('account' => $login_account, 'password' => $login_password, 'remember_me' => $remember_me));

			$errorMessage = getAccountLoginByLabel() . ' or password is not correct.';

			// temporary solution for blocking failed login attempts
			if($cache->enabled())
			{
				if(isset($t))
				{
					$t['attempts']++;
					$t['last'] = time();

					if($t['attempts'] >= 5)
						$errors[] = 'A wrong password has been entered 5 times in a row. You are unable to log into your account for the next 5 minutes. Please wait.';
					else
						$errors[] = $errorMessage;
				}
				else
				{
					$t = array('attempts' => 1, 'last' => time());
					$errors[] = $errorMessage;
				}

				$tmp[$ip] = $t;
				$cache->set('failed_logins', serialize($tmp), 60 * 60); // save for 1 hour
			}
			else {
				$errors[] = $errorMessage;
			}
		}
	}
	else {
		$errors[] = 'Please enter your ' . getAccountLoginByLabel() . ' and password.';

		$hooks->trigger(HOOK_LOGIN_ATTEMPT, array('account' => $login_account, 'password' => $login_password, 'remember_me' => $remember_me));
	}

	$hooks->trigger(HOOK_ACCOUNT_LOGIN_POST);
}
