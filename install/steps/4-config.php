<?php
defined('MYAAC') or die('Direct access not allowed!');

$clients = array();
foreach($config['clients'] as $client) {
	$client_version = (string)($client / 100);
	if(strpos($client_version, '.') == false)
		$client_version .= '.0';

	$clients[$client] = $client_version;
}

if (empty($_SESSION['var_site_url'])) {
	//require SYSTEM . 'base.php';
	$serverUrl = 'http' . (isHttps() ? 's' : '') . '://' . $baseHost;
	$siteURL = $serverUrl . $baseDir;

	$_SESSION['var_site_url'] = $siteURL;
}

$twig->display('install.config.html.twig', array(
	'clients' => $clients,
	'timezones' => DateTimeZone::listIdentifiers(),
	'locale' => $locale,
	'session' => $_SESSION,
	'errors' => isset($errors) ? $errors : null,
	'buttons' => next_buttons()
));
