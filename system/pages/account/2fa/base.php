<?php
defined('MYAAC') or die('Direct access not allowed!');

use MyAAC\TwoFactorAuth\TwoFactorAuth;

csrfProtect();

$title = 'Two Factor Authentication';

/**
 * @var OTS_Account $account_logged
 */
$code = $_REQUEST['auth-code'] ?? '';

if ((!setting('core.mail_enabled')) && ACTION == 'email-code') {
	$twig->display('error_box.html.twig',  ['errors' => ['Account two-factor e-mail authentication disabled.']]);
	return;
}

if (!isset($account_logged) || !$account_logged->isLoaded()) {
	$current_session = getSession('account');
	if($current_session) {
		$account_logged = new OTS_Account();
		$account_logged->load($current_session);
	}
}

$twoFactorAuth = TwoFactorAuth::getInstance($account_logged);
$twig->addGlobal('account_logged', $account_logged);
