<?php
/**
 * Lost account
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Lost Account Interface';

if(!setting('core.mail_enabled')) {
	echo "<b>Account maker is not configured to send e-mails, you can't use Lost Account Interface. Contact with admin to get help.</b>";
	return;
}

$twig->display('account/lost/form.html.twig');
