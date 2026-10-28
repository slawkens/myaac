<?php
defined('MYAAC') or die('Direct access not allowed!');

csrfProtect();

$title = 'Lost Account';

if (ACTION == 'email') {
	require __DIR__ . '/email/step-1.php';
}
else if (ACTION == 'recovery-key') {
	require __DIR__ . '/recovery-key/step-1.php';
}
else if (ACTION == 'two-factor') {
	require __DIR__ . '/two-factor/step-1.php';
}
else {
	$twig->display('account/lost/no-action.html.twig');
}

