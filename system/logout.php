<?php
/**
 * Logout from account
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\CsrfToken;

defined('MYAAC') or die('Direct access not allowed!');

$account_logged = accountLogged();
$hooks = app()->get('hooks');

if($account_logged !== null && $account_logged->isLoaded()) {
	if($hooks->trigger(HOOK_LOGOUT, ['account_id' => $account_logged->getId()])) {
		unsetSession('account');
		unsetSession('password');
		unsetSession('remember_me');

		CsrfToken::generate();

		global $logged, $account_logged;
		$logged = false;
		$account_logged = new OTS_Account();

		app()->setLoggedIn($logged);
		app()->setAccountLogged($account_logged);
	}
}
