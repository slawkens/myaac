<?php
defined('MYAAC') or die('Direct access not allowed!');
if(isset($config['installed']) && $config['installed'] && !isset($_SESSION['saved'])) {
	echo '<div class="alert alert-warning"><span>' . $locale['already_installed'] . '</span></div>';
}
else {
	unset($_SESSION['saved']);

	$locales = array();
	foreach(get_locales() as $tmp_locale)
	{
		$lang_file_main = LOCALE . $tmp_locale . '/main.php';
		$lang_file_install = LOCALE . $tmp_locale . '/install.php';
		if(@file_exists($lang_file_main)
			&& @file_exists($lang_file_install))
		{
			require $lang_file_main;
			$locales[] = array('id' => $tmp_locale, 'name' => $locale['name']);
		}
	}

	$twig->display('install.welcome.html.twig', array(
		'locales' => $locales,
		'locale' => $locale,
		'cookie_locale' => @$_COOKIE['locale'],
		'detected_locale' => @$detected_locale,
		'buttons' => next_buttons(false, true)
	));
}
