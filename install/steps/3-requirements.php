<?php
defined('MYAAC') or die('Direct access not allowed!');

// configuration
$dirs_required_writable = [
	'system/logs',
	'system/cache',
];

$dirs_required = [
	'tools/ext' => $locale['step_requirements_folder_not_exists_tools_ext'],
];

$dirs_optional = [
	GUILD_IMAGES_DIR => $locale['step_requirements_warning_images_guilds'],
	GALLERY_DIR => $locale['step_requirements_warning_images_gallery'],
];

$extensions_required = [
	'pdo', 'pdo_mysql', 'json', 'xml'
];
$extensions_optional = [
	'gd' => $locale['step_requirements_warning_player_signatures'],
	'zip' => $locale['step_requirements_warning_install_plugins'],
];

/*
 *
 * @param string $name
 * @param boolean $ok
 * @param mixed $info
 */
function version_check($name, $ok, $info = '', $warning = false)
{
	global $failed;
	echo '<div class="alert alert-' . ($ok ? 'success' : ($warning ? 'warning' : 'danger')) . '">' . $name;
	if(!empty($info))
		echo ': <b>' . $info . '</b>';

	echo '</div>';
	if(!$ok && !$warning)
		$failed = true;
}

$failed = false;

// start validating
version_check($locale['step_requirements_php_version'], (PHP_VERSION_ID >= 50500), PHP_VERSION);

foreach ($dirs_required_writable as $value)
{
	$is_writable = is_writable(BASE . $value) && (MYAAC_OS != 'WINDOWS' || win_is_writable(BASE . $value));
	version_check($locale['step_requirements_write_perms'] . ': ' . $value, $is_writable);
}

foreach ($dirs_optional as $dir => $errorMsg) {
	$is_writable = is_writable(BASE . $dir) && (MYAAC_OS != 'WINDOWS' || win_is_writable(BASE . $dir));
	version_check($locale['step_requirements_write_perms'] . ': ' . $dir, $is_writable, $is_writable ? '' : $errorMsg, true);
}

foreach ($dirs_required as $dir => $errorMsg)
{
	$exists = is_dir(BASE . $dir);
	version_check($locale['step_requirements_folder_exists'] . ': ' . $dir, $exists, $exists ? '' : $errorMsg);
}

$ini_register_globals = ini_get_bool('register_globals');
version_check('register_long_arrays', !$ini_register_globals, $ini_register_globals ? $locale['on'] : $locale['off']);

$ini_safe_mode = ini_get_bool('safe_mode');
version_check('safe_mode', !$ini_safe_mode, $ini_safe_mode ? $locale['on'] : $locale['off'], true);

foreach ($extensions_required as $ext) {
	$loaded = extension_loaded($ext);
	version_check(str_replace('$EXTENSION$', strtoupper($ext), $locale['step_requirements_extension']) , $loaded, $loaded ? $locale['loaded'] : $locale['not_loaded']);
}

foreach ($extensions_optional as $ext => $errorMsg) {
	$loaded = extension_loaded($ext);
	version_check(str_replace('$EXTENSION$', strtoupper($ext), $locale['step_requirements_extension']) , $loaded, $loaded ? $locale['loaded'] : $locale['not_loaded'] . '. ' . $errorMsg, true);
}

echo '<div class="text-center m-3">';

if($failed) {
	echo '<div class="alert alert-warning"><span>' . $locale['step_requirements_failed'] . '</span></div>';
	echo next_form(true, false);
}else {
	echo next_form(true, true);
}

echo '</div>';
