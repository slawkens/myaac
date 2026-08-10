<?php
/**
 * Admin permissions management
 */

defined('MYAAC') or die('Direct access not allowed!');

use MyAAC\Models\AdminPermission;
use MyAAC\Models\Account;

$title = 'Permissions';

ensureAdminPermissionTables();

if (!hasAdminPermission('permissions', 'view')) {
	error('You do not have permission to manage admin permissions.');
	return;
}

$action = $_POST['action'] ?? '';
$selectedAccountEmail = ($_REQUEST['account_email'] ?? '');
$selectedAccountId = '';
$showMatrix = false;
$accountData = null;

if ($selectedAccountEmail !== '') {
	$accountData = Account::query()->select(['id', 'name', 'web_flags', 'email'])->where('email', $selectedAccountEmail)->first();
	if (!$accountData) {
		error('The selected account email does not exist.');
	}
	else {
		$selectedAccountId = $accountData->id;
		$showMatrix = true;
	}
}

$webFlags = isset($accountData->web_flags) ? $accountData->web_flags : 0;

if ($action === 'save') {
	$webFlags = (int) ($_POST['web_flags'] ?? 0);
	$selectedPages = $_POST['pages'] ?? [];
	$operations = array_keys(ADMIN_PERMISSIONS_OPERATION_MAP);

	if (empty($selectedAccountId)) {
		error('Please select an account before saving permissions.');
	}
	else {
		try {
			$db->beginTransaction();

			AdminPermission::query()->where('account_id', $selectedAccountId)->delete();

			foreach ($selectedPages as $pageKey => $pageOperations) {
				$pageName = str_replace(['..', '/', '\\'], '', (string) $pageKey);
				if ($pageName === '') {
					continue;
				}

				$selectedOps = [];
				foreach ($operations as $operation) {
					if (!empty($pageOperations[$operation])) {
						$selectedOps[] = $operation;
					}
				}

				$operationsString = buildOperationsString($selectedOps);
				if ($operationsString === '') {
					continue;
				}

				AdminPermission::query()->create([
					'account_id' => (int) $selectedAccountId,
					'page' => $pageName,
					'operations' => $operationsString,
				]);
			}

			Account::query()->where('id', $selectedAccountId)->update(['web_flags' => $webFlags]);

			$db->commit();
			success('Permissions saved at ' . date('H:i:s') . '.');
		}
		catch (\Exception $error) {
			$db->rollBack();
			error('Failed to save permissions: ' . $error->getMessage());
		}

		$showMatrix = true;
	}
}

$pages = [];
$selected = [];
$operations = array_keys(ADMIN_PERMISSIONS_OPERATION_MAP);
if ($showMatrix) {
	$pages = getAdminPermissionPages();
	$selected = getAdminPagePermissionSelections((int) $selectedAccountId);
}

$twig->display('admin.permissions.html.twig', [
	'showMatrix' => $showMatrix,

	'selectedAccountEmail' => $selectedAccountEmail,
	'selectedAccountId' => $selectedAccountId,
	'webFlags' => $webFlags,

	'pages' => $pages,

	'selected' => $selected,
	'operations' => $operations,
]);
