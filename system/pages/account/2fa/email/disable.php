<?php
defined('MYAAC') or die('Direct access not allowed!');

require __DIR__ . '/../base.php';

//if (!$twoFactorAuth->hasRecentEmailCode(15 * 60)) {
//	$twoFactorAuth->resendEmailCode();
//}

/*if (isset($_POST['save'])) {
	if (!empty($code)) {
		if ($twoFactorAuth->getAuthGateway()->verifyCode($code)) {
*/
$twoFactorAuth->disable();
$twoFactorAuth->deleteOldCodes();

$twig->display('success.html.twig',
	[
		'title' => 'Email Code Authentication Deactivated',
		'description' => 'You have successfully <b>deactivated</b> the <b>Email Code Authentication</b> for your account.'
	]
);
/*
}
else {
$errors[] = 'Invalid email code!';
}
}
}*/

/*
if (!empty($errors)) {
	$twig->display('error_box.html.twig', ['errors' => $errors]);
}

$twig->display('account/2fa/email/deactivate.html.twig', ['wrongCode' => count($errors) > 0]);
*/
