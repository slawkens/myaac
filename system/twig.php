<?php

require_once LIBS . 'Twig/Autoloader.php';
Twig_Autoloader::register();

$twig_loader = new Twig_Loader_Filesystem(SYSTEM . 'templates');
$twig = new Twig_Environment($twig_loader, array(
	'cache' => CACHE . 'twig/',
	'auto_reload' => true,
	//'debug' => true
));

$function = new Twig_SimpleFunction('getStyle', function ($i) {
	return getStyle($i);
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('getLink', function ($s) {
	return getLink($s);
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('hook', function ($hook) {
	global $hooks;
	$hooks->trigger($hook);
});
$twig->addFunction($function);

$filter = new Twig_SimpleFilter('urlencode', function ($s) {
	return urlencode($s);
});
$twig->addFilter($filter);