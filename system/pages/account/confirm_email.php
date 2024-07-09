<?php
/**
 * Account confirm mail
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

$title = 'Confirm Email';

$hash = isset($_GET['v']) ? $_GET['v'] : '';
if(empty($hash)) {
	warning('Please enter email hash code.<br/>If you copied the link, please try again with full link.');
	return;
}

$res = $db->query('SELECT `email_hash` FROM `accounts` WHERE `email_hash` = ' . $db->quote($hash));
if(!$res->rowCount()) {
	note("Your email couldn't be verified. Please contact staff to do it manually.");
}
else
{
	$query = $db->query('SELECT id FROM accounts WHERE email_hash = ' . $db->quote($hash) . ' AND email_verified = 0');
	if ($query->rowCount() == 1) {
		$query = $query->fetch(PDO::FETCH_ASSOC);
		$account = new OTS_Account();
		$account->load($query['id']);
		if ($account->isLoaded()) {
			$db->update('accounts', ['email_verified' => '1'], ['email_hash' => $hash]);
			success('You have now verified your e-mail, this will increase the security of your account. Thank you for doing this. You can now <a href=' . getLink('account/manage') . '>log in</a>.');

			$hooks->trigger(HOOK_EMAIL_CONFIRMED, ['account' => $account]);
		}
	}
	else {
		error('Link has expired.');
	}
}
