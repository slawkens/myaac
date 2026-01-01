<?php
defined('MYAAC') or die('Direct access not allowed!');

csrfProtect();

$title = 'Lost Account';

$nick = $_REQUEST['nick'] ?? '';

if($account->isLoaded()) {
	$account_key = $account->getCustomField('key');

	if(!empty($account_key)) {
		$twig->display('account/lost/recovery-key.step-1.html.twig', [
			'nick' => $nick,
		]);
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
	'action' => getLink('account/lost'),
]);
