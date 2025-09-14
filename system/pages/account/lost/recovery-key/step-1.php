<?php
defined('MYAAC') or die('Direct access not allowed!');

csrfProtect();

$title = 'Lost Account';

if($account->isLoaded()) {
	$account_key = $account->getCustomField('key');

	if(!empty($account_key)) {
		$twig->display('account/lost/recovery-key.html.twig', [
			'nick' => $nick,
		]);
	}
	else {
		echo 'Account of this character has no recovery key!';
	}
}
else {
	echo "Player or account of player <b>" . escapeHtml($nick) . "</b> doesn't exist.";
}

$twig->display('account.back_button.html.twig', [
	'new_line' => true,
	'center' => true,
	'action' => getLink('account/lost'),
]);
