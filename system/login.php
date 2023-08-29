<?php
/**
 * Login manager
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$logged = false;
$logged_flags = 0;
$account_logged = new OTS_Account();

// stay-logged with sessions
$current_session = getSession('account');
if($current_session !== false)
{
	$account_logged->load($current_session);
	if($account_logged->isLoaded() && $account_logged->getPassword() == getSession('password')
		//&& (!isset($_SESSION['admin']) || admin())
		&& (getSession('remember_me') !== false || getSession('last_visit') > time() - 15 * 60)) {  // login for 15 minutes if "remember me" is not used
			$logged = true;
	}
	else {
		unsetSession('account');
		unset($account_logged);
	}
}

if($logged) {
	$logged_flags = $account_logged->getWebFlags();
	$twig->addGlobal('logged', true);
	$twig->addGlobal('account_logged', $account_logged);
}

setSession('last_visit', time());
if(defined('PAGE')) {
	setSession('last_page', PAGE);
}
setSession('last_uri', $_SERVER['REQUEST_URI']);
