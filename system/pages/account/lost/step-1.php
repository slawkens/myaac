<?php
defined('MYAAC') or die('Direct access not allowed!');

$title = 'Lost Account';

$nick = stripslashes($_REQUEST['nick']);

$player = new OTS_Player();
$account = new OTS_Account();
$player->find($nick);
if($player->isLoaded()) {
	$account = $player->getAccount();
}

if (ACTION == 'email') {
	require __DIR__ . '/email/step-1.php';
}
else if (ACTION == 'recovery-key') {
	require __DIR__ . '/recovery-key/step-1.php';
}
else {
	$twig->display('account/lost/no-action.html.twig');
}

