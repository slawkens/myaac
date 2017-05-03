<?php
/**
 * Login manager
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.0.2
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$logged = false;
$logged_flags = 0;

$action = isset($_REQUEST['action']) ? strtolower($_REQUEST['action']) : '';
if($action == 'logout' && !isset($_REQUEST['account_login']))
{
	unset($_SESSION['account']);
	unset($_SESSION['password']);
	unset($_SESSION['remember_me']);

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
				$_SESSION['account'] = $account_logged->getId();
				$_SESSION['password'] = encrypt(($config_salt_enabled ? $account_logged->getCustomField('salt') : '') . $login_password);
				if(isset($_POST['remember_me']))
					$_SESSION['remember_me'] = true;

				//if(isset($_POST['admin']))
				//	$_SESSION['admin'] = true;
			
				$logged = true;

				$logged_flags = $account_logged->getWebFlags();
				if(isset($_POST['admin']) && !admin()) {
					$errors[] = 'This account has no admin privileges.';
					unset($_SESSION['account']);
					unset($_SESSION['password']);
					unset($_SESSION['remember_me']);
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
	}
	
	// stay-logged with sessions
	if(isset($_SESSION['account']))
	{
		$account_logged = $ots->createObject('Account');
		$account_logged->load($_SESSION['account']);
		if($account_logged->isLoaded() && $account_logged->getPassword() == $_SESSION['password']
			//&& (!isset($_SESSION['admin']) || admin())
			&& (isset($_SESSION['remember_me']) || $_SESSION['last_visit'] > time() - 15 * 60))  // login for 15 minutes if "remember me" is not used
				$logged = true;
		else
		{
			unset($_SESSION['account']);
			unset($account_logged);
		}
	}

	if($logged)
		$logged_flags = $account_logged->getWebFlags();
}

$_SESSION['last_visit'] = time();
if(defined('PAGE'))
	$_SESSION['last_page'] = PAGE;
$_SESSION['last_uri'] = $_SERVER['REQUEST_URI'];
?>
