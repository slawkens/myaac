<?php

use MyAAC\Admin\Insights;

defined('MYAAC') or die('Direct access not allowed!');

$getYear = (int)($_GET['year'] ?? date('Y'));
$getMonth = $_GET['month'] ?? (int)date('M') + 1;

$insights = new Insights($db);

$twig->display('insights.html.twig', [
	'lastLoginPlayers' => $insights->getLastLoggedPlayers($getYear, $getMonth),
	'lastCreatedAccounts' => $insights->getLastCreatedAccounts($getYear, $getMonth),

	'firstYear' => $insights->getFirstYear(),

	'getYear' => $getYear,
	'getMonth' => $getMonth,

	'months' => $insights->getMonths(),
]);
