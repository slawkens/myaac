<?php
/**
 * Mailer
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\Account;

defined('MYAAC') or die('Direct access not allowed!');
$title = 'Mailer';

csrfProtect();

if (!hasFlag(FLAG_CONTENT_MAILER) && !superAdmin()) {
	echo 'Access denied.';
	return;
}

if (!setting('core.mail_enabled')) {
	echo 'Mail support disabled in config.';
	return;
}

$mail_to = isset($_REQUEST['mail_to']) ? stripslashes(trim($_REQUEST['mail_to'])) : null;
$mail_subject = isset($_POST['mail_subject']) ? stripslashes($_POST['mail_subject']) : null;
$mail_content = isset($_POST['mail_content']) ? stripslashes($_POST['mail_content']) : null;
$mail_verified_only = $_POST['mail_verified_only'] ?? false;

if (isset($_POST['submit'])) {
	if (empty($mail_subject)) {
		warning('Please enter subject of the message.');
	}

	if (empty($mail_content)) {
		warning('Please enter content of the message.');
	}
}
if (!empty($mail_to)) {
	if(!Validator::email($mail_to)) {
		warning('E-Mail is invalid.');
	}
	else {
		if (!empty($mail_content) && !empty($mail_subject)) {
			if (_mail($mail_to, $mail_subject, $mail_content)) {
				success("Successfully mailed <strong>$mail_to</strong>");
			}
			else {
				error("Error while sending mail to <strong>$mail_to</strong>. More info can be found in system/logs/mailer-error.log");
			}
		}
	}
}

if (!empty($mail_content) && !empty($mail_subject) && empty($mail_to)) {
	$success = 0;
	$failed = 0;

	$query = Account::where('email', '!=', '');

	if ($mail_verified_only) {
		info('Note: Sending only to users with verified E-Mail.');
		$query->where('email_verified', 1);
	}

	foreach ($query->get(['email']) as $email) {
		if (_mail($email->email, $mail_subject, $mail_content)) {
			$success++;
		}
		else {
			$failed++;
			echo '<br />';
			error('An error occorred while sending email to <b>' . $email->email . '</b>. For Admin: More info can be found in system/logs/mailer-error.log');
		}
	}

	success('Mailing finished.');
	success("$success emails delivered.");
	warning("$failed emails failed.");
}

$twig->display('admin.mailer.html.twig', [
	'mail_to' => $mail_to,
	'mail_subject' => $mail_subject,
	'mail_content' => $mail_content,
	'mail_verified_only' => $mail_verified_only,
]);
