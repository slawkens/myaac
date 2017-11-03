<?php
/**
 * Login manager
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.6
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$logged = false;
$logged_flags = 0;

$action = isset($_REQUEST['action']) ? strtolower($_REQUEST['action']) : '';
define('ACTION', $action);

if(ACTION == 'logout' && !isset($_REQUEST['account_login']))
{
	unsetSession('account');
	unsetSession('password');
	unsetSession('remember_me');

	if(isset($_REQUEST['redirect']))
	{
		header('Location: ' . urldecode($_REQUEST['redirect']));
		exit;
	}
}
else
{
	// new login with data from form
	if(!$logged && isset($_POST['account_login']) && isset($_POST['password_login']))
	{
		$login_account = strtoupper($_POST['account_login']);
		$login_password = $_POST['password_login'];
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
				$t = isset($tmp[$ip]) ? $tmp[$ip] : NULL;
			}

			$account_logged = $ots->createObject('Account');
			if(USE_ACCOUNT_NAME)
				$account_logged->find($login_account);
			else
				$account_logged->load($login_account);
	
			$config_salt_enabled = fieldExist('salt', 'accounts');
			if($account_logged->isLoaded() && encrypt(($config_salt_enabled ? $account_logged->getCustomField('salt') : '') . $login_password) == $account_logged->getPassword()
				&& (!isset($t) || $t['attempts'] < 5)
				)
			{
				setSession('account', $account_logged->getId());
				setSession('password', encrypt(($config_salt_enabled ? $account_logged->getCustomField('salt') : '') . $login_password));
				if(isset($_POST['remember_me']))
					setSession('remember_me', true);

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
			}
			else
			{
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
							$errors[] = 'Account name or password is not correct.';
					}
					else
					{
						$t = array('attempts' => 1, 'last' => time());
						$errors[] = 'Account name or password is not correct.';
					}

					$tmp[$ip] = $t;
					$cache->set('failed_logins', serialize($tmp), 60 * 60); // save for 1 hour
				}
			}
		}
		else {
			$errors[] = 'Please enter your account ' . (USE_ACCOUNT_NAME ? 'name' : 'password') . ' and password.';
		}
	}

	// stay-logged with sessions
	$current_session = getSession('account');
	if($current_session !== false)
	{
		$account_logged = new OTS_Account();
		$account_logged->load($current_session);
		if($account_logged->isLoaded() && $account_logged->getPassword() == getSession('password')
			//&& (!isset($_SESSION['admin']) || admin())
			&& (getSession('remember_me') !== false || getSession('last_visit') > time() - 15 * 60)) {  // login for 15 minutes if "remember me" is not used
				$logged = true;
		}
		else
		{
			$logged = false;
			unsetSession('account');
			unset($account_logged);
		}
	}
	
	if($logged) {
		$logged_flags = $account_logged->getWebFlags();
		$twig->addGlobal('logged', true);
		$twig->addGlobal('account_logged', $account_logged);
	}
}

setSession('last_visit', time());
if(defined('PAGE')) {
	setSession('last_page', PAGE);
}
setSession('last_uri', $_SERVER['REQUEST_URI']);
?>
