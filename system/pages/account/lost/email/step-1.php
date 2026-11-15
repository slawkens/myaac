<?php
defined('MYAAC') or die('Direct access not allowed!');

csrfProtect();

$title = 'Lost Account';

require __DIR__ . '/../_base.php';

$email = $_REQUEST['email'] ?? '';

$twig->display('account/lost/email.html.twig', [
	'character' => $character,
	'email' => str_contains($character, '@') ? $character : $email,
]);

if (!empty($errors)) {
	$twig->display('error_box.html.twig', [
		'errors' => $errors,
	]);
}
