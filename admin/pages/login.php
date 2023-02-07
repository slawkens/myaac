<?php
/**
 * Login
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Login';

require PAGES . 'account/login.php';
if ($logged) {
	header('Location: ' . ADMIN_URL);
	return;
}

$twig->display('admin.login.html.twig', [
	'logout' => (ACTION == 'logout' ? 'You have  been logged out!'  : ''),
	'account' => USE_ACCOUNT_NAME ? 'Name' : 'Number',
	'account_login_by' => getAccountLoginByLabel(),
	'errors' => $errors ?? ''
]);
