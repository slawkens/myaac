<?php

$nick = stripslashes($_REQUEST['nick']);

$player = new OTS_Player();
$account = new OTS_Account();
$player->find($nick);
if($player->isLoaded()) {
	$account = $player->getAccount();
}

if($account->isLoaded()) {
	if($account->getCustomField('email_next') < time()) {
		$twig->display('account.lost.step1-email.html.twig', [
			'nick' => $nick,
		]);
	}
	else
	{
		$insec = (int)$account->getCustomField('email_next') - time();
		$minutesleft = floor($insec / 60);
		$secondsleft = $insec - ($minutesleft * 60);
		$timeleft = $minutesleft.' minutes '.$secondsleft.' seconds';

		echo 'Account of selected character (<b>'.$nick.'</b>) received e-mail in last '.ceil(setting('core.mail_lost_account_interval') / 60).' minutes. You must wait '.$timeleft.' before you can use Lost Account Interface again.';
	}
}
else {
	echo "Player or account of player <b>" . htmlspecialchars($nick) . "</b> doesn't exist.";
}

$twig->display('account.back_button.html.twig', [
	'new_line' => true,
	'center' => true,
	'action' => getLink('account/lost'),
]);
