<?php
/**
 * Change comment
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

$redirect = urldecode($_REQUEST['redirect']);

// should never happen, unless hacker modify the URL
if (!str_contains($redirect, BASE_URL)) {
	error('Fatal error: Cannot redirect outside the website.');
	return;
}

$twig->display('account.redirect.html.twig', array(
	'redirect' => $redirect
));
