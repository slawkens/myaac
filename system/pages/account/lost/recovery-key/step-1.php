<?php
defined('MYAAC') or die('Direct access not allowed!');

csrfProtect();

$title = 'Lost Account';

require __DIR__ . '/../_base.php';

$key = $_REQUEST['key'] ?? '';

if(!$accountLost) {
	$errors[] = "Player or account of player <b>" . escapeHtml($character) . "</b> doesn't exist.";
}
else {
	$account_key = $accountLost->key;

	if(!empty($account_key)) {
		$twig->display('account/lost/recovery-key.step-1.html.twig', [
			'character' => $character,
			'key' => $key,
			'newRecoveryKeyFormat' => $newRecoveryKeyFormat,
		]);
	}
	else {
		$errors[] = 'Account of this character has no recovery key!';
	}
}

if (!empty($errors)) {
	$twig->display('error_box.html.twig', [
		'errors' => $errors,
	]);

	$twig->display('account.back_button.html.twig', [
		'new_line' => true,
		'center' => true,
		'action' => getLink('account/lost'),
	]);
}
