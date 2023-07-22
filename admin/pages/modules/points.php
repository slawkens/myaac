<?php
defined('MYAAC') or die('Direct access not allowed!');

$points = ($db->hasColumn('accounts', 'premium_points') ? $db->query('SELECT `premium_points`, `' . (USE_ACCOUNT_NAME ? 'name' : 'id') . '` as `name` FROM `accounts` ORDER BY `premium_points` DESC LIMIT 10;') : 0);

$twig->display('points.html.twig', array(
	'points' => $points,
));
