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
	$accountModel = Account::where('email_hash', $hash)->where('email_verified', 0)->first();
	if ($accountModel) {
		$accountModel->email_verified = 1;
		$accountModel->save();

		success('You have now verified your e-mail, this will increase the security of your account. Thank you for doing this. You can now <a href=' . getLink('account/manage') . '>log in</a>.');

		$account = new OTS_Account();
		$account->load($accountModel->id);
		if ($account->isLoaded()) {
			$hooks->trigger(HOOK_EMAIL_CONFIRMED, ['account' => $account]);
		}
	}
	else {
		error('Link has expired.');
	}
}
