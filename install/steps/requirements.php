<?php
/*
 * 
 * @param string $name
 * @param boolean $ok
 * @param mixed $version
 */
function version_check($name, $ok, $version = '', $warning = false)
{
	global $failed;
	echo '<p class="' . ($ok ? 'success' : ($warning ? 'warning' : 'error')) . '">' . $name;
	if(!empty($version))
		echo ': <b>' . $version . '</b>';

	echo '</p>';
	if(!$ok && !$warning)
		$failed = true;
}

$failed = false;

// start validating
version_check($locale['step_requirements_php_version'], (PHP_VERSION_ID >= 50000), PHP_VERSION);
foreach(array('config.local.php', 'images/guilds', 'images/houses', 'images/screenshots') as $value)
{
	$perms = (int) substr(decoct(fileperms(BASE . $value)), 2);
	version_check($locale['step_requirements_write_perms'] . ': ' . $value, $perms >= 660);
}

$ini_register_globals = ini_get_bool('register_globals');
version_check('register_long_arrays', !$ini_register_globals, $ini_register_globals ? $locale['on'] : $locale['off']);

$ini_safe_mode = ini_get_bool('safe_mode');
version_check('safe_mode', !$ini_safe_mode, $ini_safe_mode ? $locale['on'] : $locale['off'], true);

version_check('PDO extension loaded', extension_loaded('pdo'), '', false);
version_check('zip extension loaded', extension_loaded('zip'), '', false);

if($failed)
{
	echo '<br/><b>' . $locale['step_requirements_failed'];
	echo next_form(true, false);
}
else
	echo next_form(true, true);
?>