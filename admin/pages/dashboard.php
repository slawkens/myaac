<?php
/**
 * Dashboard
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Dashboard';

csrfProtect();

if (isset($_POST['clear_cache'])) {
	if (clearCache()) {
		success('Cache cleared.');
	} else {
		error('Error while clearing cache.');
	}
}

if (isset($_POST['maintenance'])) {
	$message = (!empty($_POST['message']) ? $_POST['message'] : null);
	$_status = (isset($_POST['status']) && $_POST['status'] == 'true');
	$_status = ($_status ? '0' : '1');

	if (empty($message)) {
		error('Message cannot be empty.');
	} else if (strlen($message) > 255) {
		error('Message is too long. Maximum length allowed is 255 chars.');
	} else {
		$tmp = '';
		if (fetchDatabaseConfig('site_closed', $tmp))
			updateDatabaseConfig('site_closed', $_status);
		else
			registerDatabaseConfig('site_closed', $_status);

		if (fetchDatabaseConfig('site_closed_message', $tmp))
			updateDatabaseConfig('site_closed_message', $message);
		else
			registerDatabaseConfig('site_closed_message', $message);
	}
}
$is_closed = getDatabaseConfig('site_closed') == '1';

$closed_message = 'Server is under maintenance, please visit later.';
$tmp = '';
if (fetchDatabaseConfig('site_closed_message', $tmp))
	$closed_message = $tmp;

$settingAdminPanelModules = setting('core.admin_panel_modules');
if (count($settingAdminPanelModules) > 0) {
	echo '<div class="row">';
	$twig_loader->prependPath(__DIR__ . '/modules/templates');
	foreach ($settingAdminPanelModules as $box) {
		$file = __DIR__ . '/modules/' . $box . '.php';
		if (file_exists($file)) {
			include($file);
		}
	}
echo '</div>';
}
