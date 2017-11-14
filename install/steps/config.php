<?php
defined('MYAAC') or die('Direct access not allowed!');

$clients_list = array(
	710,
	740,
	750,
	760,
	770,
	780,
	7920,
	800,
	810,
	821,
	822,
	831,
	840,
	841,
	842,
	850,
	852,
	853,
	854,
	855,
	857,
	860,
	870,

	900,
	910,
	920,
	930,
	940,
	942,
	944,
	946,
	950,
	952,
	953,
	954,
	960,
	970,
	980,

	1000,
	1010,
	1021,
	1031,
	1034,
	1041,
	1050,
	1053,
	1054,
	1058,
	1075,
	1077,
	1079,
	1080,
	1090,
	1093,
	1094,
	1095,
	1096,
	1097,
	1098,
	1100,
);

$clients = array();
foreach($clients_list as $client) {
	$client_version = (string)($client / 100);
	if(strpos($client_version, '.') == false)
		$client_version .= '.0';
	
	$clients[$client] = $client_version;
}

echo $twig->render('install.config.html.twig', array(
	'clients' => $clients,
	'locale' => $locale,
	'session' => $_SESSION,
	'errors' => isset($errors) ? $errors : null,
	'buttons' => next_buttons()
));
?>