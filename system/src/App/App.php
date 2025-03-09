<?php

namespace MyAAC\App;

use MyAAC\Hooks;
use MyAAC\Services\AnonymousStatisticsService;
use MyAAC\Services\DatabaseService;
use MyAAC\Services\LoginService;
use MyAAC\Services\RouterService;
use MyAAC\Services\StatusService;
use MyAAC\Settings;
use MyAAC\Visitors;
use MyAAC\Twig\EnvironmentBridge as MyAAC_Twig_EnvironmentBridge;
use Twig\Loader\FilesystemLoader;

class App
{
	private bool $isLoggedIn = false;
	private ?\OTS_Account $accountLogged = null;
	private array $instances = [];

	public function run(): void
	{
		$configInstalled = config('installed');
		if((!isset($configInstalled) || !$configInstalled) && file_exists(BASE . 'install')) {
			header('Location: ' . BASE_URL . 'install/');
			exit();
		}

		$template_place_holders = [];

		require_once SYSTEM . 'init.php';
		require_once SYSTEM . 'template.php';

		$loginService = new LoginService();
		$checkLogin = $loginService->checkLogin();
		$logged = $checkLogin['logged'];
		$account_logged = $checkLogin['account'];

		$statusService = new StatusService();
		$status = $statusService->checkStatus();

		global $config;

		$twig = app()->get('twig');
		$twig->addGlobal('config', $config);
		$twig->addGlobal('status', $status);

		$hooks = app()->get('hooks');
		$hooks->trigger(HOOK_STARTUP);

		$routerService = new RouterService();
		$handleRouting = $routerService->handleRouting();

		$title = $handleRouting['title'];
		$content = $handleRouting['content'];

		$anonymousStatisticsService = new AnonymousStatisticsService();
		$anonymousStatisticsService->checkReport();

		if(setting('core.views_counter')) {
			require_once SYSTEM . 'counter.php';
		}

		if(setting('core.visitors_counter')) {
			global $visitors;
			$visitors = new Visitors(setting('core.visitors_counter_ttl'));
		}

		global $content;
		/**
		 * @var \OTS_Account $account_logged
		 */
		if ($this->isLoggedIn && admin()) {
			$content .= $twig->render('admin-bar.html.twig', [
				'username' => USE_ACCOUNT_NAME ? $this->accountLogged->getName() : $this->accountLogged->getId()
			]);
		}

		global $template_path, $template_index;
		$title_full =  (isset($title) ? $title . ' - ' : '') . $config['lua']['serverName'];

		$logged = $this->isLoggedIn;
		$accountLogged = $this->accountLogged;

		require $template_path . '/' . $template_index;

		echo base64_decode('PCEtLSBQb3dlcmVkIGJ5IE15QUFDIDo6IGh0dHBzOi8vd3d3Lm15LWFhYy5vcmcvIC0tPg==') . PHP_EOL;
		if(superAdmin()) {
			echo '<!-- Generated in: ' . round(microtime(true) - START_TIME, 4) . 'ms -->';
			echo PHP_EOL . '<!-- Queries done: ' . $db->queries() . ' -->';
			if(function_exists('memory_get_peak_usage')) {
				echo PHP_EOL . '<!-- Peak memory usage: ' . convert_bytes(memory_get_peak_usage(true)) . ' -->';
			}
		}

		$hooks->trigger(HOOK_FINISH);
	}

	public function setAccountLogged(\OTS_Account $accountLogged): void {
		$this->accountLogged = $accountLogged;
	}

	public function getAccountLogged(): ?\OTS_Account {
		return $this->accountLogged;
	}

	public function setLoggedIn($loggedIn): void {
		$this->isLoggedIn = $loggedIn;
	}

	public function isLoggedIn(): bool {
		return $this->isLoggedIn;
	}

	public function get($what)
	{
		if ($what == 'db') {
			$what = 'database';
		}

		if (!isset($this->instances[$what])) {
			switch ($what) {
				case 'cache':
					$this->instances[$what] = \MyAAC\Cache\Cache::getInstance();
					break;

				case 'database':
					$databaseService = new DatabaseService();
					$this->instances[$what] =  $databaseService->getConnectionHandle();
					break;

				case 'groups':
					$this->instances[$what] = new \OTS_Groups_List();
					break;

				case 'hooks':
					$this->instances[$what] = new Hooks();
					break;

				case 'settings':
					$this->instances[$what] = Settings::getInstance();
					break;

				case 'twig':
					$dev_mode = (config('env') === 'dev');
					$this->instances[$what] = new MyAAC_Twig_EnvironmentBridge($this->get('twig-loader'), array(
						'cache' => CACHE . 'twig/',
						'auto_reload' => $dev_mode,
						'debug' => $dev_mode
					));
					break;

				case 'twig-loader':
					$this->instances[$what] = new FilesystemLoader(SYSTEM . 'templates');
					break;
			}
		}

		return $this->instances[$what];
	}
}
