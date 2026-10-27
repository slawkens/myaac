<?php
defined('MYAAC') or die('Direct access not allowed!');

use MyAAC\TwoFactorAuth\TwoFactorAuth;

csrfProtect();

$title = 'Two Factor Authentication';

/**
 * @var OTS_Account $account_logged
 */
$code = $_REQUEST['auth-code'] ?? '';

if (!$account_logged->isLoaded()) {
	$current_session = getSession('account_two_factor_id');
	if($current_session) {
		$account_logged = new OTS_Account();
		$account_logged->load($current_session);
	}
}

$twoFactorAuth = TwoFactorAuth::getInstance($account_logged->getId());
$twig->addGlobal('account_logged', $account_logged);

/**
 * Took from ZnoteAAC
 * @author Znote
 */
function generateRandomTwoFactorSecret($length = 16): string
{
	$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
	$charactersLength = strlen($characters);
	$randomString = '';

	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}

	return $randomString;
}
