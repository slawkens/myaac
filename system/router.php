<?php
/**
 * Router
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2021 MyAAC
 * @link      https://my-aac.org
 */

if(!$load_it) {
	// ignore warnings in some functions/plugins
	// page is not loaded anyways
	define('ACTION', '');
	define('PAGE', '');

	return;
}

if(SITE_CLOSED && admin())
	$content .= '<p class="note">Site is under maintenance (closed mode). Only privileged users can see it.</p>';

$ignore = false;

$logged_access = 1;
if($logged && $account_logged && $account_logged->isLoaded()) {
	$logged_access = $account_logged->getAccess();
}

$success = false;
$tmp_content = getCustomPage($uri, $success);
if($success) {
	$content .= $tmp_content;
	if(hasFlag(FLAG_CONTENT_PAGES) || superAdmin()) {
		$pageInfo = getCustomPageInfo($uri);
		$content = $twig->render('admin.pages.links.html.twig', array(
				'page' => array('id' => $pageInfo !== null ? $pageInfo['id'] : 0, 'hidden' => $pageInfo !== null ? $pageInfo['hidden'] : '0')
			)) . $content;
	}

	$page = $uri;
} else {
	// old support for pages like /?subtopic=accountmanagement
	$page = isset($_REQUEST['p']) ? $_REQUEST['p'] : (isset($_REQUEST['subtopic']) ? $_REQUEST['subtopic'] : '');
	if(!empty($page) && preg_match('/^[A-z0-9\-]+$/', $page)) {
		if(config('backward_support')) {
			require SYSTEM . 'compat_pages.php';
		}

		$file = SYSTEM . 'pages/' . $page . '.php';
		if (!is_file($file)) {
			$page = '404';
			$file = SYSTEM . 'pages/404.php';
		}
	}
	else {
		$dispatcher = FastRoute\cachedDispatcher(function (FastRoute\RouteCollector $r) {
			$routes = require SYSTEM . 'routes.php';

			$duplicates = [];
			Plugins::clearWarnings();
			foreach (Plugins::getRoutes() as $route) {
				$duplicates[$route[1]] = true;
				$r->addRoute($route[0], '/' . $route[1], $route[2]);
			}

			foreach ($routes as $route) {
				if(!isset($duplicates[$route[1]])) {
					$r->addRoute($route[0], '/' . $route[1], 'system/pages/' . $route[2]);
				}
			}

			if (config('env') === 'dev') {
				foreach(Plugins::getWarnings() as $warning) {
					log_append('router.log', $warning);
				}
			}
		},
			[
				'cacheFile' => CACHE . 'route.cache',
				'cacheDisabled' => config('env') === 'dev',
			]
		);

		// Fetch method and URI from somewhere
		$httpMethod = $_SERVER['REQUEST_METHOD'];
		$uri = $_SERVER['REQUEST_URI'];

		// Strip query string (?foo=bar) and decode URI
		if (false !== $pos = strpos($uri, '?')) {
			if ($pos !== 1) {
				$uri = substr($uri, 0, $pos);
			}
			else {
				$uri = str_replace_first('?', '', $uri);
			}
		}
		$uri = rawurldecode($uri);

		$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
		switch ($routeInfo[0]) {
			case FastRoute\Dispatcher::NOT_FOUND:
				// ... 404 Not Found
				$tmp = URI;
				$found = true;

				$page = $tmp;
				if (preg_match('/^[A-z0-9\/\-]+$/', $tmp)) {
					global $template_path;
					$file = $template_path . '/pages/' . $tmp . '.php';
					if (!is_file($file)) {
						$file = SYSTEM . 'pages/' . $tmp . '.php';
						if (!is_file($file)) {
							$found = false;
						}
					}
				}
				else {
					$tmp_ = BASE_DIR;
					$uri = $_SERVER['REQUEST_URI'];
					if (!empty($tmp)) {
						$uri = str_replace(BASE_DIR . '/', '', $uri);
					}

					if (false !== $pos = strpos($uri, '?')) {
						$tmp = substr($uri, 0, $pos);
					}

					if (empty($tmp)) {
						$page = 'news';
						$file = SYSTEM . 'pages/news.php';
					}
					else {
						$found = false;
					}
				}

				if (!$found) {
					$page = '404';
					$file = SYSTEM . 'pages/404.php';
				}

				break;

			case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
				// ... 405 Method Not Allowed
				$page = '405';
				$allowedMethods = $routeInfo[1];
				$file = SYSTEM . 'pages/405.php';
				break;

			case FastRoute\Dispatcher::FOUND:
				$path = $routeInfo[1];
				$vars = $routeInfo[2];

				$_REQUEST = array_merge($_REQUEST, $vars);
				$_GET = array_merge($_GET, $vars);

				// parse for define PAGE
				$tmp = BASE_DIR;
				$uri = $_SERVER['REQUEST_URI'];
				if (!empty($tmp)) {
					$uri = str_replace(BASE_DIR . '/', '', $uri);
				}

				if (false !== $pos = strpos($uri, '?')) {
					$uri = substr($uri, 0, $pos);
				}
				if (0 === strpos($uri, '/')) {
					$uri = str_replace_first('/', '', $uri);
				}

				$page = $uri;
				$file = BASE . $path;

				unset($tmp, $uri);
				break;
		}
	}
}

define('PAGE', $page);
if(config('backward_support')) {
	$subtopic = $page;
}

$action = isset($_REQUEST['action']) ? strtolower($_REQUEST['action']) : '';
define('ACTION', $action);

ob_start();
if($hooks->trigger(HOOK_BEFORE_PAGE)) {
	if(!$ignore)
		require $file;
}

unset($file);

if(config('backward_support') && isset($main_content[0]))
	$content .= $main_content;

$content .= ob_get_contents();
ob_end_clean();
$hooks->trigger(HOOK_AFTER_PAGE);

if(config('backward_support')) {
	$main_content = $content;
	if(!isset($title)) {
		$title = ucfirst($page);
	}

	$topic = $title;
}

unset($page);
