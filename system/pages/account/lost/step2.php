<?php

$recKey = trim($_REQUEST['key']);
$nick = stripslashes($_REQUEST['nick']);

$player = new OTS_Player();
$account = new OTS_Account();
$player->find($nick);
if($player->isLoaded()) {
	$account = $player->getAccount();
}

if($account->isLoaded()) {
	$accountKey = $account->getCustomField('key');
	if(!empty($accountKey)) {
		if($accountKey == $recKey) {
			$twig->display('account.lost.step2.html.twig', [
				'nick' => $nick,
				'recKey' => $recKey,
			]);
		}
		else {
			echo 'Wrong recovery key!';
		}
	}
	else {
		echo 'Account of this character has no recovery key!';
	}
}
else
	echo "Player or account of player <b>" . htmlspecialchars($nick) . "</b> doesn't exist.";

$twig->display('account.back_button.html.twig', [
	'new_line' => true,
	'center' => true,
	'action' => getLink('account/lost') . '?action=step1&action_type=reckey&nick=' . urlencode($nick),
]);
