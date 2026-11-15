<?php
defined('MYAAC') or die('Direct access not allowed!');

csrfProtect();

$title = 'Lost Account';

require __DIR__ . '/../_base.php';

if (!$accountLost) {
	$errors[] = "Player or account of player <b>" . escapeHtml($character) . "</b> doesn't exist.";
}
else {
	if (isset($_REQUEST['key'])) {
		$key = $_REQUEST['key'];
	}
	elseif ($newRecoveryKeyFormat) {
		$key = $_POST['key1'] . '-' . $_POST['key2'] . '-' . $_POST['key3'] . '-' . $_POST['key4'];
	}
}


if (($newRecoveryKeyFormat && strlen($key) < 4) || empty($key)) {
	$errors[] = 'Please enter the recovery key!';
}
else {
	$accountKey = $accountLost->key;
	if(!empty($accountKey)) {
		if($accountKey == $key) {
			$twig->display('account/lost/recovery-key.step-2.html.twig', [
				'character' => $character,
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

if (!empty($errors)) {
	$twig->display('error_box.html.twig', [
		'errors' => $errors,
	]);
}

$twig->display('account.back_button.html.twig', [
	'new_line' => true,
	'center' => true,
	'action' => getLink('account/lost/step-1') . '?action=recovery-key&character=' . urlencode($character) . '&key=' . urlencode($key),
]);
