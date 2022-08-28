<?php
defined('MYAAC') or die('Direct access not allowed!');

$twig->display('web_status.twig', array(
	'is_closed' => $is_closed,
	'closed_message' => $closed_message,
	'status' => $status,
	'account_type' => USE_ACCOUNT_NAME ? 'name' : 'number'
));
?>
