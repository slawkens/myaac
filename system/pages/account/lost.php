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

if(!setting('core.mail_enabled'))
{
	echo '<b>Account maker is not configured to send e-mails, you can\'t use Lost Account Interface. Contact with admin to get help.</b>';
	return;
}

$action_type = $_REQUEST['action_type'] ?? '';
if($action == '') {
	$twig->display('account.lost.form.html.twig');
}
else if($action == 'step1' && $action_type == '') {
	$twig->display('account.lost.noaction.html.twig');
}
elseif($action == 'step1' && $action_type == 'email') {
	require PAGES . 'account/lost/step1-email.php';
}
elseif($action == 'send-code') {
	require PAGES . 'account/lost/send-code.php';
}
elseif($action == 'step1' && $action_type == 'reckey') {
	require PAGES . 'account/lost/step1-reckey.php';
}
elseif($action == 'step2') {
	require PAGES . 'account/lost/step2.php';
}
elseif($action == 'step3') {
	require PAGES . 'account/lost/step3.php';
}
elseif($action == 'check-code') {
	require PAGES . 'account/lost/check-code.php';
}
elseif($action == 'set-new-password') {
	require PAGES . 'account/lost/set-new-password.php';
}
