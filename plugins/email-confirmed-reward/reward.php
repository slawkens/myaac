<?php
defined('MYAAC') or die('Direct access not allowed!');

$reward = config('account_mail_confirmed_reward');

$hasCoinsColumn = $db->hasColumn('accounts', 'coins');
if ($reward['coins'] > 0 && !$hasCoinsColumn) {
	log_append('email_confirm_error.log', 'accounts.coins column does not exist.');
}

if (!isset($account) || !$account->isLoaded()) {
	//log_append('email_confirm_error.log', 'Account not loaded.');
	return;
}

if ($reward['premium_points'] > 0) {
	$account->setCustomField('premium_points', (int)$account->getCustomField('premium_points') + $reward['premium_points']);

	success(sprintf($reward['message'], $reward['premium_points'], 'premium points'));
}

if ($reward['coins'] > 0 && $hasCoinsColumn) {
	$account->setCustomField('coins', (int)$account->getCustomField('coins') + $reward['coins']);

	success(sprintf($reward['message'], $reward['coins'], 'coins'));
}

if ($reward['premium_days'] > 0) {
	$account->setPremDays($account->getPremDays() + $reward['premium_days']);
	$account->save();

	success(sprintf($reward['message'], $reward['premium_days'], 'premium days'));
}
