<?php
/**
 * Account confirm mail
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\Account;

defined('MYAAC') or die('Direct access not allowed!');

$title = 'Confirm Email';

$hash = $_GET['hash'] ?? '';
if(empty($hash)) {
	warning('Please enter email hash code.<br/>If you copied the link, please try again with full link.');
	return;
}

if(!Account::where('email_hash', $hash)->exists()) {
	note("Your email couldn't be verified. Please contact staff to do it manually.");
}
else
{
	if (Account::where('email_hash', $hash)->where('email_verified', 0)->exists()) {
		$query = $query->fetch(PDO::FETCH_ASSOC);
		$account = new OTS_Account();
		$account->load($query['id']);
		if ($account->isLoaded()) {
			$hooks->trigger(HOOK_EMAIL_CONFIRMED, ['account' => $account]);
		}
	}

	Account::where('email_hash', $hash)->update('email_verified', 1);
	success('You have now verified your e-mail, this will increase the security of your account. Thank you for doing this.');
}
?>
