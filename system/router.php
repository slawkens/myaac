<?php
/**
 * Router
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2023 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\Pages;

defined('MYAAC') or die('Direct access not allowed!');

if(!isset($content[0]))
	$content = '';

// check if site has been closed
$load_it = true;
$site_closed = false;
if(fetchDatabaseConfig('site_closed', $site_closed)) {
	$site_closed = ($site_closed == 1);
	if($site_closed) {
		if(!admin())
		{
			$title = getDatabaseConfig('site_closed_title');
			$content .= '<p class="note">' . getDatabaseConfig('site_closed_message') . '</p><br/>';
			$load_it = false;
		}

		if(!$logged)
		{
			ob_start();
			require SYSTEM . 'pages/account/manage.php';
			$content .= ob_get_contents();
			ob_end_clean();
			$load_it = false;
		}
	}
}
define('SITE_CLOSED', $site_closed);

// Strip query string (?foo=bar) and decode URI
/** @var string $uri */
if (false !== $pos = strpos($uri, '?')) {
	if ($pos !== 1) {
		$uri = substr($uri, 0, $pos);
	}
	else {
		$uri = str_replace_first('?', '', $uri);
	}
}

$uri = rawurldecode($uri);
if (BASE_DIR !== '') {
	$tmp = str_replace_first('/', '', BASE_DIR);
	$uri = str_replace_first($tmp, '', $uri);
}

if(0 === strpos($uri, '/')) {
	$uri = str_replace_first('/', '', $uri);
}

define('URI', $uri);

if(!$load_it) {
	// ignore warnings in some functions/plugins
	// page is not loaded anyway
	define('PAGE', '');

	return;
}

/** @var string $content */
if(SITE_CLOSED && admin())
	$content .= '<p class="note">Site is under maintenance (closed mode). Only privileged users can see it.</p>';

$ignore = false;

/** @var boolean $logged */
/** @var OTS_Account $account_logged */
$logged_access = 1;
if($logged && $account_logged && $account_logged->isLoaded()) {
	$logged_access = $account_logged->getAccess();
}

/**
 * Routes loading
 */
$dispatcher = FastRoute\cachedDispatcher(function (FastRoute\RouteCollector $r) {
	$routes = require SYSTEM . 'routes.php';

	$isAlreadyDefined = [];

	$routesTmp = [];
	foreach(getDatabasePages() as $page) {
		$isAlreadyDefined[$page] = true;
		$routesTmp[] = ['*', $page, '__database__/' . $page, true];
	}

	Plugins::clearWarnings();
	foreach (Plugins::getRoutes() as $route) {
		if(!isset($isAlreadyDefined[$route[1]])) {
			$isAlreadyDefined[$route[1]] = true;
			$routesTmp[] = [$route[0], $route[1], $route[2]];
		}
	}

	foreach ($routes as $route) {
		if(!isset($isAlreadyDefined[$route[1]])) {
			if (strpos($route[2], '__redirect__') === false && strpos($route[2], '__database__') === false) {
				$routesTmp[] = [$route[0], $route[1], 'system/pages/' . $route[2]];
			}
			else {
				$routesTmp[] = [$route[0], $route[1], $route[2]];
			}
		}
	}

	//var_dump($routesTmp);
	foreach ($routesTmp as $route) {
		if ($route[0] === '*') {
			$route[0] = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD'];
		}
		else {
			if (is_string($route[0])) {
				$route[0] = explode(',', $route[0]);
			}

			$toUpperCase = function(string $value): string {
				return trim(strtoupper($value));
			};

			// convert to upper case, fast-route accepts only upper case
			$route[0] = array_map($toUpperCase, $route[0]);
		}

		$aliases = [
			[':int', ':string', ':alphanum'],
			[':\d+', ':[A-Za-z0-9-_%+\' ]+', ':[A-Za-z0-9]+'],
		];

		// apply aliases
		$route[1] = str_replace($aliases[0], $aliases[1], $route[1]);

		$r->addRoute($route[0], $route[1], $route[2]);
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

// Fetch method and URI
$httpMethod = $_SERVER['REQUEST_METHOD'];

$found = true;

// old support for pages like /?subtopic=accountmanagement
$page = $_REQUEST['p'] ?? ($_REQUEST['subtopic'] ?? '');
if(!empty($page) && preg_match('/^[A-z0-9\-]+$/', $page)) {
	if (isset($_REQUEST['p'])) { // some plugins may require this
		$_REQUEST['subtopic'] = $_REQUEST['p'];
	}

	if (setting('core.backward_support')) {
		require SYSTEM . 'compat/pages.php';
	}

	$file = loadPageFromFileSystem($page, $found);
	if(!$found) {
		$file = false;
	}
}
else {
	$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
	switch ($routeInfo[0]) {
		case FastRoute\Dispatcher::NOT_FOUND:
			// ... 404 Not Found
			/**
			 * Fallback to load page from templates/ or system/pages/ directory
			 */
			$page = $uri;
			if (preg_match('/^[A-z0-9\/\-]+$/', $page)) {
				$file = loadPageFromFileSystem($page, $found);
			} else {
				$found = false;
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
			extract($vars);

			if (strpos($path, '__database__/') !== false) {
				$pageName = str_replace('__database__/', '', $path);

				$success = false;
				$tmp_content = getCustomPage($pageName, $success);
				if ($success) {
					$content .= $tmp_content;
					if (hasFlag(FLAG_CONTENT_PAGES) || superAdmin()) {
						$pageInfo = getCustomPageInfo($pageName);
						$content = $twig->render('admin.links.html.twig', ['page' => 'pages', 'id' => $pageInfo !== null ? $pageInfo['id'] : 0, 'hidden' => $pageInfo !== null ? $pageInfo['hidden'] : '0']
							) . $content;
					}

					$page = $pageName;
					$file = false;
				}
			} else if (strpos($path, '__redirect__/') !== false) {
				$path = str_replace('__redirect__/', '', $path);
				header('Location: ' . BASE_URL . $path);
				exit;
			} else {
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
			}

			unset($tmp, $uri);
			break;
	}
}

if (!$found) {
	$page = '404';
	$file = SYSTEM . 'pages/404.php';
}

define('PAGE', $page);

ob_start();
if($hooks->trigger(HOOK_BEFORE_PAGE)) {
	if(!$ignore && $file !== false)
		require $file;
}

unset($file);

if(setting('core.backward_support') && isset($main_content[0]))
	$content .= $main_content;

$content .= ob_get_contents();
ob_end_clean();
$hooks->trigger(HOOK_AFTER_PAGE);

if(!isset($title)) {
	$title = ucfirst($page);
}

if(setting('core.backward_support')) {
	$main_content = $content;
	$topic = $title;
}

unset($page);

function getDatabasePages($withHidden = false): array
{
	global $logged_access;
	$pages = Pages::where('access', '<=', $logged_access)->when(!$withHidden, function ($q) {
		$q->isPublic();
	})->get('name');

	foreach($pages as $page) {
		$ret[] = $page->name;
	}

	return $ret;
}

function loadPageFromFileSystem($page, &$found): string
{
	$file = SYSTEM . 'pages/' . $page . '.php';
	if (!is_file($file)) {
		// feature: convert camelCase to snake_case
		// so instead of forum/move_thread
		// we can write: forum/moveThread
		$file = SYSTEM . 'pages/' . camelCaseToUnderscore($page) . '.php';
		if (!is_file($file)) {
			// feature: load pages from templates/ dir
			global $template_path;
			$file = $template_path . '/pages/' . $page . '.php';
			if (!is_file($file)) {
				$found = false;
			}
		}
	}

	return $file;
}
