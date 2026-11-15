<?php
defined('MYAAC') or die('Direct access not allowed!');

use MyAAC\Models\Player;

csrfProtect();

$title = 'Lost Account';

$code = $_REQUEST['code'] ?? '';
$character = $_REQUEST['character'] ?? '';

if(empty($code) || empty($character)) {
	$twig->display('account/lost/check-code.html.twig', [
		'code' => $code,
		'characters' => $character,
	]);
}
else {
	$player = Player::where('name', $character)->first();
	$account = null;
	if($player) {
		$account = $player->account()->first();
	}

	if($account) {
		if($account->email_code == $code) {
			$twig->display('account/lost/check-code.finish.html.twig', [
				'character' => $character,
				'code' => $code,
			]);
		}
		else {
			$error = 'Wrong code to change password.';
		}
	}
	else {
		$error = "Account of this character or this character doesn't exist.";
	}
}

if(!empty($error)) {
	$twig->display('error_box.html.twig', [
		'errors' => [$error],
	]);

	echo '<br/>';

	$twig->display('account/lost/check-code.html.twig', [

	]);
}
