<?php
defined('MYAAC') or die('Direct access not allowed!');

csrfProtect();

$title = 'Lost Account';

$key = $_REQUEST['key'] ?? '';
$nick = $_POST['nick'] ?? '';

$player = new OTS_Player();
$account = new OTS_Account();
$player->find($nick);
if($player->isLoaded()) {
	$account = $player->getAccount();
}

if($account->isLoaded()) {
	$accountKey = $account->getCustomField('key');
	if(!empty($accountKey)) {
		if($accountKey == $key) {
			$twig->display('account/lost/recovery-key.step-2.html.twig', [
				'nick' => $nick,
				'key' => $key,
			]);
		}
		else {
			$errors[] = 'Wrong recovery key!';
		}
	}
	else {
		$errors[] = 'Account of this character has no recovery key!';
	}
}
else {
	$errors[] = "Player or account of player <b>" . escapeHtml($nick) . "</b> doesn't exist.";
}

if (!empty($errors)) {
	$twig->display('error_box.html.twig', [
		'errors' => $errors,
	]);
}

$twig->display('account.back_button.html.twig', [
	'new_line' => true,
	'center' => true,
	'action' => getLink('account/lost/step-1') . '?action=recovery-key&nick=' . urlencode($nick),
]);
