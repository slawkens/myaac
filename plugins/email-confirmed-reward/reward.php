<?php
defined('MYAAC') or die('Direct access not allowed!');

$reward = setting('core.account_mail_confirmed_reward');

$hasCoinsColumn = $db->hasColumn('accounts', 'coins');
$rewardCoins = setting('core.account_mail_confirmed_reward_coins');
if ($rewardCoins > 0 && !$hasCoinsColumn) {
	log_append('error.log', 'email_confirm: accounts.coins column does not exist.');
}

if (!isset($account) || !$account->isLoaded()) {
	return;
}

$rewardMessage = 'You received %d %s for confirming your E-Mail address.';

$rewardPremiumPoints = setting('core.account_mail_confirmed_reward_premium_points');
if ($rewardPremiumPoints > 0) {
	$account->setCustomField('premium_points', (int)$account->getCustomField('premium_points') + $rewardPremiumPoints);

	success(sprintf($rewardMessage, $rewardPremiumPoints, 'premium points'));
}

if ($rewardCoins > 0 && $hasCoinsColumn) {
	$account->setCustomField('coins', (int)$account->getCustomField('coins') + $rewardCoins);

	success(sprintf($rewardMessage, $rewardCoins, 'coins'));
}

$rewardPremiumDays = setting('core.account_mail_confirmed_reward_premium_days');
if ($rewardPremiumDays > 0) {
	$account->setPremDays($account->getPremDays() + $rewardPremiumDays);
	$account->save();

	success(sprintf($rewardMessage, $rewardPremiumDays, 'premium days'));
}
