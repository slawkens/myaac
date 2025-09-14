<?php
defined('MYAAC') or die('Direct access not allowed!');

function lostAccountCooldown(string $nick, int $time): string
{
	$inSec = $time - time();
	$minutesLeft = floor($inSec / 60);
	$secondsLeft = $inSec - ($minutesLeft * 60);
	$timeLeft = $minutesLeft.' minutes '.$secondsLeft.' seconds';

	$timeRounded = ceil(setting('core.mail_lost_account_interval') / 60);

	return "Account of selected character (<b>" . escapeHtml($nick) . "</b>) received e-mail in last $timeRounded minutes. You must wait $timeLeft before you can use Lost Account Interface again.";
}
