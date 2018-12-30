<?php
/**
 * Login
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Login';
$logout = '';
if ($action == 'logout') {
	$logout = "You have  been logged out!";
}

if (isset($errors)) {
	foreach ($errors as $error) {
		error($error);
	}
}

$twig->display('admin.login.html.twig', array(
	'logout' => $logout,
	'account' => USE_ACCOUNT_NAME ? 'Name' : 'Number',
));