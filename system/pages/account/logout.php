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

require SYSTEM . 'logout.php';

$twig->display('account.logout.html.twig');
