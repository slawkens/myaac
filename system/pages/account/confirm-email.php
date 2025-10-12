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
use MyAAC\Models\AccountEmailVerify;

defined('MYAAC') or die('Direct access not allowed!');

$title = 'Confirm Email';

$hash = $_GET['hash'] ?? '';
if(empty($hash)) {
	warning('Please enter email hash code.<br/>If you copied the link, please try again with full link.');
	return;
}

// by default link is valid for 30 days
$accountEmailVerify = AccountEmailVerify::where('hash', $hash)->where('sent_at', '>', time() - 30 * 24 * 60 * 60)->first();
if(!$accountEmailVerify) {
	note("Wrong link or link has expired.");
}
else
{
	$accountModel = Account::where('id', $accountEmailVerify->account_id)->where('email_verified', 0)->first();
	if ($accountModel) {
		$accountModel->email_verified = 1;
		$accountModel->save();

		AccountEmailVerify::where('account_id', $accountModel->id)->delete();

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
