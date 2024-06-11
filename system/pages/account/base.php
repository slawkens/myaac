<?php
/**
 * Account confirm mail
 * Keept for compability
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

if(!$logged)
{
	$title = 'Login';

	if(!empty($errors))
		$twig->display('error_box.html.twig', array('errors' => $errors));

	$twig->display('account.login.html.twig', array(
		'redirect' => $_REQUEST['redirect'] ?? null,
		'account' => USE_ACCOUNT_NAME ? 'Name' : 'Number',
		'account_login_by' => getAccountLoginByLabel(),
		'error' => $errors[0] ?? null,
		'errors' => $errors ?? [],
	));

	return;
}
else {
	$show_form = true;
}
