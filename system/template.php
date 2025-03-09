<?php
/**
 * Template parsing engine.
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Cache\Cache;
use MyAAC\Models\Menu;
use MyAAC\Plugins;

defined('MYAAC') or die('Direct access not allowed!');

// template
$template_name = setting('core.template');
if(setting('core.template_allow_change'))
{
	if(isset($_GET['template']))
	{
		if(!preg_match("/[^A-z0-9_\-]/", $template_name)) { // validate template
			//setcookie('template', $template_name, 0, BASE_DIR . '/', $_SERVER["SERVER_NAME"]);
			$template_name = $_GET['template'];

			$cache = app()->get('cache');
			if($cache->enabled()) {
				$cache->delete('template_menus');
			}

			setSession('template', $template_name);

			$newLocation = $lastUri = getSession('last_uri');
			if($lastUri === $_SERVER['REQUEST_URI']) { // avoid ERR_TOO_MANY_REDIRECTS error in browsers
				$newLocation = SERVER_URL;
			}

			header('Location:' . $newLocation);
		}
	}
	else {
		$template_session = getSession('template');
		if ($template_session) {
			if (!preg_match("/[^A-z0-9_\-]/", $template_session)) {
				$template_name = $template_session;
			}
		}
	}
}

global $template_path, $template_index;

$themes = Plugins::getThemes();
$template_path = $themes[$template_name] ?? 'templates/' . $template_name;

if(file_exists(BASE . $template_path . '/index.php')) {
	$template_index = 'index.php';
}
elseif(file_exists(BASE . $template_path . '/template.php')) {
	$template_index = 'template.php';
}
else {
	$template_name = 'kathrine';
	$template_path = 'templates/' . $template_name;
	$template_index = 'template.php';
	if(!file_exists(BASE . $template_path . '/' . $template_index)) {
		throw new RuntimeException('Cannot load any template. Please ensure your templates directory is not empty, and you set correct name for template in configuration.');
	}
}

if(file_exists(BASE . $template_path . '/config.php')) {
	require BASE . $template_path . '/config.php';
}

$template_ini = Cache::remember('template_ini_' . $template_name, 10 * 60, function() use ($template_path) {
	$file = BASE . $template_path . '/config.ini';
	if (!file_exists($file)) {
		return [];
	}

	return parse_ini_file($file);
});

foreach ($template_ini as $key => $value) {
	$config[$key] = $value;
}

$twig->addGlobal('template_name', $template_name);
$twig->addGlobal('template_path', $template_path);
if($twig_loader) {
	$twig_loader->prependPath(BASE . $template_path);
}

function get_template_menus(): array
{
	global $template_name;

	$result = Cache::remember('template_menus', 10 * 60, function () use ($template_name) {
		$result = Menu::select(['name', 'link', 'blank', 'color', 'category'])
			->where('template', $template_name)
			->orderBy('category')
			->orderBy('ordering')
			->get();

		return $result->toArray();
	});

	$configMenuCategories = config('menu_categories');
	$configMenuDefaultColor = config('menu_default_links_color') ?? config('menu_default_color');

	$menus = [];
	foreach($result as $menu) {
		if (empty($menu['link'])) {
			$menu['link'] = 'news';
		}

		$link_full = (str_starts_with(trim($menu['link']), 'http') ? $menu['link'] : getLink($menu['link']));
		$target_blank = ($menu['blank'] == 1 ? ' target="blank"' : '');

		$color = (empty($menu['color']) ? ($configMenuCategories[$menu['category']]['default_links_color'] ?? ($configMenuDefaultColor ?? '')) : $menu['color']);

		$color = str_replace('#', '', $color);

		if (in_array('#' . $color, [$configMenuCategories[$menu['category']]['default_links_color'] ?? '', $configMenuDefaultColor])) {
			$color = '';
		}

		$style_color = (empty($color) ? '' : 'style="color: #' . $color . ' !important"');

		$menus[$menu['category']][] = [
			'name' => $menu['name'],
			'link' => $menu['link'], 'link_full' => $link_full,
			'blank' => $menu['blank'] == 1, 'target_blank' => $target_blank,
			'color' => $color, 'style_color' => $style_color,
		];
	}

	$new_menus = [];
	/**
	 * @var array $configMenuCategories
	 */
	if($configMenuCategories === null) {
		return [];
	}

	foreach($configMenuCategories as $id => $options) {
		if(isset($menus[$id])) {
			$new_menus[$id] = $menus[$id];
		}
	}

	return $new_menus;
}
