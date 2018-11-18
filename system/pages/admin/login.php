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
if($action == 'logout') {
    $logout = "You have  been logged out!";
}

$search_errors[] = 'Character <b></b> does not exist or has been deleted.';


if(isset($errors)) {
	foreach($errors as $error) {
		error($error);
        $twig->display('admin.error.html.twig', array('errors' => $error));
	}
}

$twig->display('admin.login.html.twig', array(
    'errors' => $search_errors,
    'logout' => $logout
    ));