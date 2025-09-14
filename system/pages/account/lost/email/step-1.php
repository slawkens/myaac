<?php

$title = 'Lost Account';

if($account->isLoaded()) {
	if($account->getCustomField('email_next') < time()) {
		$twig->display('account/lost/email.html.twig', [
			'nick' => $nick,
		]);
	}
	else {
		$inSec = (int)$account->getCustomField('email_next') - time();
		$minutesLeft = floor($inSec / 60);
		$secondsLeft = $inSec - ($minutesLeft * 60);
		$timeLeft = $minutesLeft.' minutes '.$secondsLeft.' seconds';

		$timeRounded = ceil(setting('core.mail_lost_account_interval') / 60);

		echo "Account of selected character (<b>" . escapeHtml($nick) . "</b>) received e-mail in last $timeRounded minutes. You must wait $timeLeft before you can use Lost Account Interface again.";
	}
}
else {
	echo "Player or account of player <b>" . escapeHtml($nick) . "</b> doesn't exist.";
}

$twig->display('account.back_button.html.twig', [
	'new_line' => true,
	'center' => true,
	'action' => getLink('account/lost'),
]);
