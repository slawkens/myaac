<?php
defined('MYAAC') or die('Direct access not allowed!');

use MyAAC\Models\Account;
use MyAAC\Models\Player;

$character = $_REQUEST['character'] ?? '';

$accountLost = null;
if (str_contains($character, '@')) {
	$accountLost = Account::where('email', $character)->first();
}
else {
	$playerLost = Player::where('name', $character)->notDeleted()->first();
	if ($playerLost) {
		$accountLost = $playerLost->account()->first();
	}
}

if ($accountLost) {
	$explodeRecoveryKey = explode('-', $accountLost->key);
	$newRecoveryKeyFormat = (count($explodeRecoveryKey) == 4);
}
