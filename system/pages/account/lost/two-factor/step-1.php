<?php
defined('MYAAC') or die('Direct access not allowed!');

require __DIR__ . '/../_base.php';

if (!$accountLost) {
	$errors[] = "Player or account of player <b>" . escapeHtml($character) . "</b> doesn't exist.";
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

	return;
}

$twig->display('account/lost/auth.html.twig', [
	'newRecoveryKeyFormat' => $newRecoveryKeyFormat ?? null,
	'character' => $character,
]);
