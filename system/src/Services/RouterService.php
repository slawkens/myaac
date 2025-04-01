<?php

namespace MyAAC\Services;

use MyAAC\Models\Pages;
use MyAAC\Plugins;

class RouterService
{
	public function handleRouting(): array
	{
		global $content, $template_path, $template, $canEdit, $action;

		$db = app()->get('database');
		$twig = app()->get('twig');
		$logged = logged();
		$account_logged = accountLogged();

		if(!isset($content[0])) {
			$content = '';
		}

		// check if site has been closed
		$load_it = true;
		$site_closed = false;
		if(fetchDatabaseConfig('site_closed', $site_closed)) {
			$site_closed = ($site_closed == 1);
			if($site_closed) {
				if(!admin()) {
					$title = getDatabaseConfig('site_closed_title');
					$content .= '<p class="note">' . getDatabaseConfig('site_closed_message') . '</p><br/>';
					$load_it = false;
				}

				if(!logged()) {
					ob_start();
					require SYSTEM . 'pages/account/manage.php';
					$content .= ob_get_contents();
					ob_end_clean();
					$load_it = false;
				}
			}
		}
		define('SITE_CLOSED', $site_closed);

		$uri = $_SERVER['REQUEST_URI'];
		if(str_contains($uri, 'index.php')) {
			$uri = str_replace_first('/index.php', '', $uri);
		}

		if(str_starts_with($uri, '/')) {
			$uri = str_replace_first('/', '', $uri);
		}

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

			return [
				'title' => 'Maintenance mode',
				'content' => $content,
			];
		}

		/** @var string $content */
		if(SITE_CLOSED && admin())
			$content .= '<p class="note">Site is under maintenance (closed mode). Only privileged users can see it.</p>';

		$ignore = false;

		global $logged_access;
		$logged_access = 0;
		if($logged && $account_logged && $account_logged->isLoaded()) {
			$logged_access = $account_logged->getAccess();
		}

		/**
		 * Routes loading
		 */
		$dispatcher = \FastRoute\cachedDispatcher(function (\FastRoute\RouteCollector $r) {
			$routes = require SYSTEM . 'routes.php';

			$routesFinal = [];
			foreach($this->getDatabasePages() as $page) {
				$routesFinal[] = ['*', $page, '__database__/' . $page, 100];
			}

			Plugins::clearWarnings();
			foreach (Plugins::getRoutes() as $route) {
				$routesFinal[] = [$route[0], $route[1], $route[2], $route[3] ?? 1000];
				/*
						echo '<pre>';
						var_dump($route[1], $route[3], $route[2]);
						echo '/<pre>';
				*/
			}

			foreach ($routes as $route) {
				if (!str_contains($route[2], '__redirect__') && !str_contains($route[2], '__database__')) {
					$routesFinal[] = [$route[0], $route[1], 'system/pages/' . $route[2], $route[3] ?? 10000];
				}
				else {
					$routesFinal[] = [$route[0], $route[1], $route[2], $route[3] ?? 10000];
				}
			}

			// sort required for the next step (filter)
			usort($routesFinal, function ($a, $b)
			{
				// key 3 is priority
				if ($a[3] == $b[3]) {
					return 0;
				}

				return ($a[3] < $b[3]) ? -1 : 1;
			});

			// remove duplicates
			// if same route pattern, but different priority
			$routesFinal = array_filter($routesFinal, function ($a) {
				$aliases = [
					[':int', ':string', ':alphanum'],
					[':\d+', ':[A-Za-z0-9-_%+\' ]+', ':[A-Za-z0-9]+'],
				];

				// apply aliases
				$a[1] = str_replace($aliases[0], $aliases[1], $a[1]);

				static $duplicates = [];
				if (isset($duplicates[$a[1]])) {
					return false;
				}

				$duplicates[$a[1]] = true;
				return true;
			});
			/*
				echo '<pre>';
				var_dump($routesFinal);
				echo '</pre>';
				die;
			*/
			foreach ($routesFinal as $route) {
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

			$file = $this->loadPageFromFileSystem($page, $found);
			if(!$found) {
				$file = false;
			}
		}
		else {
			$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
			switch ($routeInfo[0]) {
				case \FastRoute\Dispatcher::NOT_FOUND:
					// ... 404 Not Found
					/**
					 * Fallback to load page from templates/ or system/pages/ directory
					 */
					$page = $uri;
					if (preg_match('/^[A-z0-9\/\-]+$/', $page)) {
						$file = $this->loadPageFromFileSystem($page, $found);
					} else {
						$found = false;
					}

					break;

				case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
					// ... 405 Method Not Allowed
					$page = '405';
					$allowedMethods = $routeInfo[1];
					$file = SYSTEM . 'pages/405.php';
					break;

				case \FastRoute\Dispatcher::FOUND:
					$path = $routeInfo[1];
					$vars = $routeInfo[2];

					$_REQUEST = array_merge($_REQUEST, $vars);
					$_GET = array_merge($_GET, $vars);
					extract($vars);

					if (str_contains($path, '__database__/')) {
						$pageName = str_replace('__database__/', '', $path);

						$success = false;
						$tmp_content = getCustomPage($pageName, $success);
						if ($success) {
							$content .= $tmp_content;
							if (hasFlag(FLAG_CONTENT_PAGES) || superAdmin()) {
								$pageInfo = getCustomPageInfo($pageName);
								$content = $twig->render('admin.links.html.twig', ['page' => 'pages', 'id' => $pageInfo !== null ? $pageInfo['id'] : 0, 'hide' => $pageInfo !== null ? $pageInfo['hide'] : '0']
									) . $content;
							}

							$page = $pageName;
							$file = false;
						}
					} else if (str_contains($path, '__redirect__/')) {
						$path = str_replace('__redirect__/', '', $path);
						header('Location: ' . BASE_URL . $path);
						exit;
					} else {
						// parse for define PAGE
						$tmp = BASE_DIR;
						$uri = $_SERVER['REQUEST_URI'];
						if (strlen($tmp) > 0) {
							$uri = str_replace(BASE_DIR . '/', '', $uri);
						}

						if (false !== $pos = strpos($uri, '?')) {
							$uri = substr($uri, 0, $pos);
						}
						if (str_starts_with($uri, '/')) {
							$uri = str_replace_first('/', '', $uri);
						}

						$page = str_replace('index.php/', '', $uri);
						if (empty($page)) {
							$page = 'news';
						}

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

		global $config;

		$cache = app()->get('cache');
		$hooks = app()->get('hooks');

		if($hooks->trigger(HOOK_BEFORE_PAGE)) {
			if(!$ignore && $file !== false) {
				require $file;
			}
		}

		$content .= ob_get_contents();
		ob_end_clean();
		$hooks->trigger(HOOK_AFTER_PAGE);

		if(!isset($title)) {
			$title = str_replace('index.php/', '', $page);
			$title = ucfirst($title);
		}

		return [
			'title' => $title,
			'content' => $content,
		];
	}

	public function getDatabasePages($withHidden = false): array
	{
		global $logged_access;
		$pages = Pages::where('access', '<=', $logged_access)->when(!$withHidden, function ($q) {
			$q->isPublic();
		})->get('name');

		$ret = [];
		foreach($pages as $page) {
			$ret[] = $page->name;
		}

		return $ret;
	}

	private function loadPageFromFileSystem($page, &$found): string
	{
		$file = SYSTEM . 'pages/' . $page . '.php';
		if (!is_file($file)) {
			// feature: load pages from templates/ dir
			global $template_path;
			$file = $template_path . '/pages/' . $page . '.php';
			if (!is_file($file)) {
				$found = false;
			}
		}

		return $file;
	}
}
