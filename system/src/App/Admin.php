<?php

namespace MyAAC\App;

use MyAAC\Services\LoginService;
use MyAAC\Services\StatusService;

class Admin
{
	public function run(): void
	{
		App::preInstallCheck();

		$content = '';

		// validate page
		$page = $_GET['p'] ?? '';
		if(empty($page) || preg_match("/[^a-zA-Z0-9_\-\/.]/", $page)) {
			$page = 'dashboard';
		}

		$page = strtolower($page);
		define('PAGE', $page);

		require_once SYSTEM . 'init.php';
		require_once ADMIN . 'includes/debugbar.php';

		$loginService = new LoginService();
		$loginService->checkLogin();

		$statusService = new StatusService();
		$status = $statusService->checkStatus();

		require ADMIN . '/includes/functions.php';

		global $config;
		$twig = app()->get('twig');
		$twig->addGlobal('config', $config);
		$twig->addGlobal('status', $status);

		if (ACTION == 'logout') {
			require SYSTEM . 'logout.php';
		}

		// if we're not logged in - show login box
		if(!logged() || !admin()) {
			$page = 'login';
		}

		// include our page
		$file = ADMIN . '/pages/' . $page . '.php';
		if(!@file_exists($file)) {
			if (str_contains($page, 'plugins/')) {
				$file = BASE . $page;
			}
			else {
				$page = '404';
				$file = SYSTEM . 'pages/404.php';
			}
		}

		$hooks = app()->get('hooks');

		ob_start();
		if($hooks->trigger(HOOK_ADMIN_BEFORE_PAGE)) {
			require $file;
		}

		$content .= ob_get_contents();
		ob_end_clean();

		// template
		$template_path = 'template/';
		require ADMIN . '/' . $template_path . 'template.php';
	}
}
