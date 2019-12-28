<?php

if(PHP_SAPI !== 'cli') {
	die('This script can be run only in command line mode.');
}

require_once __DIR__ . '/../../common.php';
require_once SYSTEM . 'functions.php';
require_once SYSTEM . 'init.php';

if($argc !== 3) {
	exit('This command expects two parameters: account_name_or_id|player_name|email address, subject.' . PHP_EOL);
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
			exit('Cannot find player or account with name: ' . $email_account_name . '.' . PHP_EOL);
		}
	}
}

if(!Validator::email($email_account_name)) {
	exit('Invalid E-Mail format.' . PHP_EOL);
}

if(strlen($subject) > 255) {
	exit('Subject max length is 255 characters.' . PHP_EOL);
}

if(!_mail($email_account_name, $subject, $message)) {
	exit('Error while sending mail: ' . $mailer->ErrorInfo . PHP_EOL);
}

echo 'Mail sent to ' . $email_account_name . '.' . PHP_EOL;