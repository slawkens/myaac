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
	if(!empty($errors))
		$twig->display('error_box.html.twig', array('errors' => $errors));

	$twig->display('account.login.html.twig', array(
		'redirect' => isset($_REQUEST['redirect']) ? $_REQUEST['redirect'] : null,
		'account' => USE_ACCOUNT_NAME ? 'Name' : 'Number',
		'error' => isset($errors[0]) ? $errors[0] : null
	));

	return;
}
else {
	$show_form = true;
	$config_salt_enabled = $db->hasColumn('accounts', 'salt');
}
