<?php
defined('MYAAC') or die('Direct access not allowed!');

// configuration
$dirs_required = [
	'system/logs',
	'system/cache',
];
$dirs_optional = [
	'images/guilds' => 'Guild logo upload will not work',
	'images/gallery' => 'Gallery image upload will not work',
];

$extensions_required = [
	'pdo', 'pdo_mysql', 'xml', 'zip'
];
$extensions_optional = [
	'gd' => 'Player Signatures will not work'
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
	echo '<p class="' . ($ok ? 'success' : ($warning ? 'warning' : 'error')) . '">' . $name;
	if(!empty($info))
		echo ': <b>' . $info . '</b>';

	echo '</p>';
	if(!$ok && !$warning)
		$failed = true;
}

$failed = false;

// start validating
version_check($locale['step_requirements_php_version'], (PHP_VERSION_ID >= 50500), PHP_VERSION);

foreach ($dirs_required as $value)
{
	$is_writable = is_writable(BASE . $value) && (MYAAC_OS != 'WINDOWS' || win_is_writable(BASE . $value));
	version_check($locale['step_requirements_write_perms'] . ': ' . $value, $is_writable);
}

foreach ($dirs_optional as $dir => $errorMsg) {
	$is_writable = is_writable(BASE . $dir) && (MYAAC_OS != 'WINDOWS' || win_is_writable(BASE . $dir));
	version_check($locale['step_requirements_write_perms'] . ': ' . $dir, $is_writable, $is_writable ? '' : $errorMsg, true);
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

if($failed)
{
	echo '<br/><b>' . $locale['step_requirements_failed'];
	echo next_form(true, false);
}
else
	echo next_form(true, true);
?>