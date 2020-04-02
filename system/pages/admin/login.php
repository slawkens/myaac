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

$twig->display('admin.login.html.twig', array(
	'logout' => ($action == 'logout' ? 'You have  been logged out!'  : ''),
	'account' => USE_ACCOUNT_NAME ? 'Name' : 'Number',
	'errors' => isset($errors)? $errors : ''
));
