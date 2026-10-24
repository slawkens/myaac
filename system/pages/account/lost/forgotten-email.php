<?php
defined('MYAAC') or die('Direct access not allowed!');

use MyAAC\Models\Account;

csrfProtect();

$title = 'Lost Account';

if (!isRequestMethod('post')) {
	$twig->display('account/lost/forgotten-email.html.twig');
	return;
}

$account = $_POST['account'] ?? '';
$password = $_POST['password'] ?? '';

$accountString = (USE_ACCOUNT_NAME ? 'name' : 'number');
if (empty($account) || empty($password)) {
	$errors[] = "Account $accountString and password are required.";
}
else {
	$column = (USE_ACCOUNT_NAME ? 'name' : (USE_ACCOUNT_NUMBER ? 'number' : 'id'));
	$account = Account::where($column, $account)->first();

	$msg = "Incorrect account $accountString or password.";

	if (!$account) {
		$errors[] = $msg;
	}
	else {
		$postPassword = encrypt((USE_ACCOUNT_SALT ? $account->salt : '') . $password);
		if($postPassword != $account->password) {
			$errors[] = $msg;
		}
		else {
			$twig->display('success.html.twig', [
				'title' => 'Account Name Found',
				'description' => "The email address of your account is: <strong>$account->email</strong>",
				'custom_buttons' => $twig->render('account/lost/forgotten-email.buttons.html.twig'),
			]);

			return;
		}
	}
}

if (!empty($errors)) {
	$twig->display('error_box.html.twig', ['errors' => $errors]);
}

$twig->display('account/lost/forgotten-email.html.twig');
