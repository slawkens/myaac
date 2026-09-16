<?php
defined('MYAAC') or die('Direct access not allowed!');
if (!isset($status)) {
	$status = [];
}

$twig->display('server_status.html.twig', ['status' => $status]);
