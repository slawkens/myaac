<?php

if(PHP_SAPI !== 'cli') {
	echo 'This script can be run only in command line mode.';
	exit(1);
}

require_once __DIR__ . '/../../common.php';
require_once SYSTEM . 'functions.php';
require_once SYSTEM . 'init.php';

if($argc !== 3) {
	echo 'This command expects two parameters: account_name_or_id|player_name|email address, subject.' . PHP_EOL;
	exit(2);
}

$email_account_name = $argv[1];
$subject = $argv[2];
$message = file_get_contents('php://stdin');

if(strpos($email_account_name, '@') === false) {
	$account = new OTS_Account();
	if(USE_ACCOUNT_NAME) {
		$account->find($email_account_name);
	}
	else {
		$account->load($email_account_name);
	}

	if($account->isLoaded()) {
		$email_account_name = $account->getEMail();
	}
	else {
		$player = new OTS_Player();
		$player->find($email_account_name);
		if($player->isLoaded()) {
			$email_account_name = $player->getAccount()->getEMail();
		}
		else {
			echo 'Cannot find player or account with name: ' . $email_account_name . '.' . PHP_EOL;
			exit(3);
		}
	}
}

if(!Validator::email($email_account_name)) {
	echo 'Invalid E-Mail format.' . PHP_EOL;
	exit(4);
}

if(strlen($subject) > 255) {
	echo 'Subject max length is 255 characters.' . PHP_EOL;
	exit(5);
}

if(!_mail($email_account_name, $subject, $message)) {
	echo 'An error occurred while sending email. More info can be found in system/logs/mailer-error.log';
	exit(6);
}

echo 'Mail sent to ' . $email_account_name . '.' . PHP_EOL;
