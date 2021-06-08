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

$twig->display('account.redirect.html.twig', array(
	'redirect' => $redirect
));
