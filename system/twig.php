<?php

require_once LIBS . 'Twig/Autoloader.php';
Twig_Autoloader::register();

$dev_mode = (config('env') === 'dev');
$twig_loader = new Twig_Loader_Filesystem(SYSTEM . 'templates');
$twig = new Twig_Environment($twig_loader, array(
	'cache' => CACHE . 'twig/',
	'auto_reload' => $dev_mode,
	'debug' => $dev_mode
));

if($dev_mode) {
	$twig->addExtension(new Twig_Extension_Debug());
}
unset($dev_mode);

$function = new Twig_SimpleFunction('getStyle', function ($i) {
	return getStyle($i);
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('getLink', function ($s) {
	return getLink($s);
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('getPlayerLink', function ($s, $p) {
	return getPlayerLink($s, $p);
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('getGuildLink', function ($s, $p) {
    return getGuildLink($s, $p);
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('hook', function ($hook) {
	global $hooks;
	$hooks->trigger($hook);
});
$twig->addFunction($function);

$function = new Twig_SimpleFunction('config', function ($key) {
	return config($key);
});
$twig->addFunction($function);

$filter = new Twig_SimpleFilter('urlencode', function ($s) {
	return urlencode($s);
});
$twig->addFilter($filter);
unset($function, $filter);