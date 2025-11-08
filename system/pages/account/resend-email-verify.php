<?php

use MyAAC\Models\AccountEmailVerify;

defined('MYAAC') or die('Direct access not allowed!');

$title = 'Resend Email';

$errorWithBackButton = function ($msg) use ($twig) {
	$errors = [$msg];

	$twig->display('error_box.html.twig', ['errors' => $errors]);
	$twig->display('account.back_button.html.twig', [
		'action' => getLink('account/resend-email-verify'),
	]);
};

if (!setting('core.mail_enabled') || !setting('core.account_mail_verify')) {
	$errorWithBackButton('Resending email is not possible on this server.');
	return;
}

$showForm = true;

if (isset($_POST['submit']) && $_POST['submit'] == '1') {
	$email = $_REQUEST['email'];

	if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$errorWithBackButton('Please enter valid Email.');
		return;
	}

	$account = new OTS_Account();
	$account->findByEMail($email);
	if ($account->isLoaded()) {
		if ($account->getCustomField('email_verified') == '1') {
			$errorWithBackButton('This account is already verified! You can <a href=' . getLink('account/manage') . '>log in</a> on the website.');
			return;
		}

		$accountEmailVerify = AccountEmailVerify::where('account_id', $account->getId())->orderBy('sent_at', 'DESC')->first();
		if ($accountEmailVerify && time() - $accountEmailVerify->sent_at < 60) {
			$errorWithBackButton('Only one Email per minute is allowed. Please try again later.');
			return;
		}

		$tmp_account = $email;
		if (!config('account_login_by_email')) {
			$tmp_account = (USE_ACCOUNT_NAME ? $account->getName() : $account->getId());
		}

		$hash = md5(generateRandomString(16, true, true) . $email);

		AccountEmailVerify::create([
			'account_id' => $account->getId(),
			'hash' => $hash,
			'sent_at' => time(),
		]);

		$verify_url = getLink('account/confirm-email/' . $hash);
		$body_html = $twig->render('mail.account.resend-email-verify.html.twig', array(
			'account' => $tmp_account,
			'verify_url' => generateLink($verify_url, $verify_url, true)
		));

		if (_mail($account->getEMail(), configLua('serverName') . ' - Verify Account', $body_html)) {
			$message = "If account with this email exists - you will become an email with verification link.";
			$showForm = false;
		} else {
			$message = "<p class='error'>An error occurred while sending email (<b>{$email}</b> )! Try again later. For Admin: More info can be found in system/logs/mailer-error.log</p>";
		}
	}
	else {
		$message = "<br />If account with this email exists - you will become an email with verification link.";
		$showForm = false;
	}

	$twig->display('success.html.twig', array(
		'title' => 'Verify Email Sent',
		'description' => $message,
	));
}

//show errors if not empty
if (!empty($errors)) {
	$twig->display('error_box.html.twig', ['errors' => $errors]);
	$twig->display('account.back_button.html.twig', [
		'action' => getLink('account/resend-email-verify'),
	]);
}

if ($showForm) {
	$twig->display('account.resend-email-verify.html.twig');
}
