<?php
defined('MYAAC') or die('Direct access not allowed!');

csrfProtect();

$title = 'Lost Account';

require __DIR__ . '/../_base.php';

if (!$accountLost)  {
	$errors[] = "Account not found.";
}
else {
	$twig->display('account/lost/email.html.twig', [
		'character' => $character,
	]);
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
