<?php
/**
 * Twig Loader
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2021 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

use MyAAC\Twig\EnvironmentBridge as MyAAC_Twig_EnvironmentBridge;
use Twig\Extension\DebugExtension as Twig_DebugExtension;
use Twig\Loader\FilesystemLoader as Twig_FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

global $twig, $twig_loader;

$dev_mode = (config('env') === 'dev');
$twig_loader = new Twig_FilesystemLoader(SYSTEM . 'templates');
$twig = new MyAAC_Twig_EnvironmentBridge($twig_loader, array(
	'cache' => CACHE . 'twig/',
	'auto_reload' => $dev_mode,
	'debug' => $dev_mode
));

$twig_loader->addPath(PLUGINS);

if($dev_mode) {
	$twig->addExtension(new Twig_DebugExtension());
}
unset($dev_mode);

$twig->addExtension(new MyAAC\Twig\Extension\TypeCastingExtension());

$filter = new TwigFilter('timeago', function ($datetime) {

	$time = time() - strtotime($datetime);

	$units = array (
		31536000 => 'year',
		2592000 => 'month',
		604800 => 'week',
		86400 => 'day',
		3600 => 'hour',
		60 => 'minute',
		1 => 'second'
	);

	foreach ($units as $unit => $val) {
		if ($time < $unit) continue;
		$numberOfUnits = floor($time / $unit);
		return ($val == 'second')? 'a few seconds ago' :
			(($numberOfUnits>1) ? $numberOfUnits : 'a')
			.' '.$val.(($numberOfUnits>1) ? 's' : '').' ago';
	}

});
$twig->addFilter($filter);

$function = new TwigFunction('getStyle', function ($i) {
	return getStyle($i);
});
$twig->addFunction($function);

$function = new TwigFunction('getLink', function ($s) {
	return getLink($s);
});
$twig->addFunction($function);

$function = new TwigFunction('generateLink', function ($s, $n, $b = false) {
	return generateLink($s, $n, $b);
});
$twig->addFunction($function);

$function = new TwigFunction('getPlayerLink', function ($s, $p = true, $colored = false) {
	return getPlayerLink($s, $p, $colored);
});
$twig->addFunction($function);

$function = new TwigFunction('getMonsterLink', function ($s, $p = true) {
	return getMonsterLink($s, $p);
});
$twig->addFunction($function);

$function = new TwigFunction('getGuildLink', function ($s, $p = true) {
    return getGuildLink($s, $p);
});
$twig->addFunction($function);

$function = new TwigFunction('truncate', function ($s, $n) {
	return truncate($s, $n);
});
$twig->addFunction($function);

$function = new TwigFunction('hook', function ($context, $hook, array $params = []) {
	global $hooks;

	if(is_string($hook)) {
		if (defined($hook)) {
			$hook = constant($hook);
		}
		else {
			// plugin/template has a hook that this version of myaac does not support
			// just silently return
			return;
		}
	}

	$params['context'] = $context;
	$hooks->trigger($hook, $params);
}, ['needs_context' => true]);
$twig->addFunction($function);

$function = new TwigFunction('config', function ($key) {
	return config($key);
});
$twig->addFunction($function);

$function = new TwigFunction('setting', function ($key) {
	return setting($key);
});
$twig->addFunction($function);

$function = new TwigFunction('getCustomPage', function ($name) {
	$success = false;
	return getCustomPage($name, $success);
});
$twig->addFunction($function);

$function = new TwigFunction('csrf', function ($return = false) {
	return csrf($return);
});
$twig->addFunction($function);

$function = new TwigFunction('csrfToken', function () {
	return csrfToken();
});
$twig->addFunction($function);

$filter = new TwigFilter('urlencode', function ($s) {
	return urlencode($s);
});

$twig->addFilter($filter);
unset($function, $filter);

$hooks->trigger(HOOK_TWIG, ['twig' => $twig, 'twig_loader' => $twig_loader]);
