<?php

defined('MYAAC') or die('Direct access not allowed!');

use MyAAC\Models\AdminPermission;
use MyAAC\Plugins;

const ADMIN_PERMISSIONS_OPERATION_MAP = [
	'view' => 'v',
	'add' => 'a',
	'edit' => 'e',
	'remove' => 'r',
];

const ADMIN_PERMISSIONS_PAGES = [
	'accounts' => [
		'enabled' => true,
		'label' => 'Accounts Editor',
		'operations' => ['view' => true, 'add' => false, 'edit' => true, 'remove' => true],
	],
	'changelog' => [
		'enabled' => true,
		'label' => 'Change Log',
		'operations' => ['view' => true, 'add' => true, 'edit' => true, 'remove' => true],
	],
	'dashboard' => [
		'enabled' => true,
		'label' => 'Dashboard',
		'operations' => ['view' => true, 'add' => false, 'edit' => false, 'remove' => false],
	],
	'data' => [
		'enabled' => true,
		'label' => 'Server Data',
		'operations' => ['view' => true, 'add' => false, 'edit' => true, 'remove' => false],
		'descriptions' => [
			'edit' => 'Reload',
		],
	],
	'logs' => [
		'enabled' => true,
		'label' => 'Logs',
		'operations' => ['view' => true, 'add' => false, 'edit' => false, 'remove' => false],
	],
	'mailer' => [
		'enabled' => true,
		'label' => 'Mailer',
		'operations' => ['view' => false, 'add' => true, 'edit' => false, 'remove' => false],
		'descriptions' => [
			'add' => 'Send mass email to players',
		],
	],
	'mass_account' => [
		'enabled' => true,
		'label' => 'Mass Account Actions',
		'operations' => ['view' => true, 'add' => true, 'edit' => true, 'remove' => true],
	],
	'mass_teleport' => [
		'enabled' => true,
		'label' => 'Mass Teleport Actions',
		'operations' => ['view' => true, 'add' => true, 'edit' => true, 'remove' => true],
	],
	'menus' => [
		'enabled' => true,
		'label' => 'Menus',
		'operations' => ['view' => false, 'add' => false, 'edit' => true, 'remove' => false],
	],
	'news' => [
		'enabled' => true,
		'label' => 'News',
		'operations' => ['view' => true, 'add' => true, 'edit' => true, 'remove' => true],
	],
	'notepad' => [
		'enabled' => true,
		'label' => 'Notepad',
		'operations' => ['view' => true, 'add' => false, 'edit' => true, 'remove' => false],
	],
	'pages' => [
		'enabled' => true,
		'label' => 'Pages',
		'operations' => ['view' => true, 'add' => true, 'edit' => true, 'remove' => true],
	],
	'permissions' => [
		'enabled' => true,
		'label' => 'Permissions',
		'operations' => ['view' => true, 'add' => false, 'edit' => true, 'remove' => false],
	],
	'phpinfo' => [
		'enabled' => true,
		'label' => 'PHP Information',
		'operations' => ['view' => true, 'add' => false, 'edit' => false, 'remove' => false],
	],
	'players' => [
		'enabled' => true,
		'label' => 'Players Editor',
		'operations' => ['view' => true, 'add' => false, 'edit' => true, 'remove' => true],
	],
	'plugins' => [
		'enabled' => true,
		'label' => 'Plugins',
		'operations' => ['view' => true, 'add' => true, 'edit' => true, 'remove' => true],
		'descriptions' => [
			'view' => 'View list and details',
			'add' => 'Upload',
			'edit' => 'Enable and disable',
			'remove' => 'Uninstall',
		],
	],
	'reports' => [
		'enabled' => true,
		'label' => 'Reports',
		'operations' => ['view' => true, 'add' => false, 'edit' => false, 'remove' => false],
	],
	'settings' => [
		'enabled' => true,
		'label' => 'Settings',
		'operations' => ['view' => true, 'add' => false, 'edit' => true, 'remove' => false],
	],
	'visitors' => [
		'enabled' => true,
		'label' => 'Visitors',
		'operations' => ['view' => true, 'add' => false, 'edit' => false, 'remove' => false],
	],
];

function ensureAdminPermissionTables(): void
{
	global $db;

	if (!$db->hasTable(TABLE_PREFIX . 'admin_permissions')) {
		$db->query('CREATE TABLE `' . TABLE_PREFIX . 'admin_permissions` (
			`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`account_id` INT UNSIGNED NOT NULL,
			`page` VARCHAR(150) NOT NULL,
			`operations` VARCHAR(20) NOT NULL,
			`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`),
			UNIQUE KEY `account_page` (`account_id`, `page`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
	}
}

function buildOperationsString(array $selectedOperations): string
{
	$letters = [];

	foreach ($selectedOperations as $operation) {
		if (isset(ADMIN_PERMISSIONS_OPERATION_MAP[$operation])) {
			$letters[] = ADMIN_PERMISSIONS_OPERATION_MAP[$operation];
		}
	}

	return implode('', array_unique($letters));
}

function getAdminPermissionPages(): array
{
	global $hooks;

	$corePages = [];
	$hiddenPages = ['index', 'login', 'clmd', 'version', 'open_source', 'tools'];

	$corePagesFiles = glob(__DIR__ . '/../pages/*.php');
	if ($corePagesFiles) {
		foreach ($corePagesFiles as $file) {
			$name = pathinfo($file, PATHINFO_FILENAME);
			if (in_array($name, $hiddenPages, true)) {
				continue;
			}

			$corePages[$name] = [
				'source' => 'core',
				'label' => ADMIN_PERMISSIONS_PAGES[$name]['label'] ?? $name,
				'operations' => ADMIN_PERMISSIONS_PAGES[$name]['operations'] ?? [],
				'descriptions' => ADMIN_PERMISSIONS_PAGES[$name]['descriptions'] ?? [],
			];
		}
	}

	ksort($corePages);

	$pluginPages = [];
	$args = ['pages' => ['core' => $corePages, 'plugins' => $pluginPages]];
	$hooks->triggerFilter(HOOK_FILTER_ADMIN_PERMISSIONS, $args);

	return $args['pages'];
}

function getAdminPagePermissionSelections(int $accountId): array {
	$rows = AdminPermission::query()
		->where('account_id', $accountId)
		->orderBy('page')
		->get(['page', 'operations']);

	$map = array_flip(ADMIN_PERMISSIONS_OPERATION_MAP);

	$selected = [];
	foreach ($rows as $row) {
		$operations = str_split($row->operations);
		foreach ($operations as $operationLetter) {
			$operationName = null;
			if (isset($map[$operationLetter])) {
				$operationName = $map[$operationLetter];
			}
			if ($operationName !== null) {
				$selected[$row->page][$operationName] = true;
			}
		}
	}

	return $selected;
}

function hasAdminPermission(string $page, ?string $operation = null): bool {
	global $logged, $account_logged;

	if (!$logged) {
		return false;
	}

	if (superAdmin()) {
		return true;
	}

	$permission = AdminPermission::query()
		->where('account_id', $account_logged->getId())
		->where('page', $page)
		->first();

	if (!$permission) {
		return false;
	}

	if ($operation === null) {
		return true;
	}

	$operationName = strtolower($operation);
	$operationLetter = ADMIN_PERMISSIONS_OPERATION_MAP[$operationName] ?? null;
	if ($operationLetter === null) {
		return false;
	}

	return str_contains($permission->operations, $operationLetter);
}
