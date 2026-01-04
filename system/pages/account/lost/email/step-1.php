<?php
defined('MYAAC') or die('Direct access not allowed!');

require __DIR__ . '/../base.php';

csrfProtect();

$title = 'Lost Account';

$nick = $_REQUEST['nick'] ?? '';

if($account->isLoaded()) {
	if($account->getCustomField('email_next') < time()) {
		$twig->display('account/lost/email.html.twig', [
			'nick' => $nick,
		]);
	}
	else {
		lostAccountWriteCooldown($nick, (int)$account->getCustomField('email_next'));
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
