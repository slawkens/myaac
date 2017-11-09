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

if($action == 'logout')
	echo 'You have been logout.<br/>';

if(isset($errors)) {
	foreach($errors as $error) {
		error($error);
	}
}

echo $twig->render('admin.login.html.twig');
?>