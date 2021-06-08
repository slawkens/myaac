<?php
/**
 * Logout Account
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2021 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

$title = 'Logout';

require __DIR__ . '/base.php';

if(!$logged) {
	return;
}

if(isset($account_logged) && $account_logged->isLoaded()) {
	if($hooks->trigger(HOOK_LOGOUT, array('account' => $account_logged, 'password' => getSession('password')))) {
		unsetSession('account');
		unsetSession('password');
		unsetSession('remember_me');

		$logged = false;
		unset($account_logged);

		if(isset($_REQUEST['redirect']))
		{
			header('Location: ' . urldecode($_REQUEST['redirect']));
			exit;
		}
	}
}

$twig->display('account.logout.html.twig');
