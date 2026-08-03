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
$selectedAccountId = $_REQUEST['account_id'] ?? '';
$showMatrix = false;

if (!empty($selectedAccountId)) {
	$accountData = Account::query()->select(['id', 'name'])->where('id', $selectedAccountId)->first();
    if (!$accountData) {
        error('The selected account does not exist.');
    }
    else {
        $showMatrix = true;
    }
}

if ($action === 'save') {
	$selectedPages = $_POST['pages'] ?? [];
    $operations = array_keys(ADMIN_PERMISSIONS_OPERATION_MAP);

	if ($selectedAccountId === '' || !ctype_digit($selectedAccountId)) {
		error('Please select an account before saving permissions.');
	}
	else {
		try {
			$db->beginTransaction();

			AdminPermission::query()->where('account_id', (int) $selectedAccountId)->delete();

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

			$db->commit();
			success('Permissions saved at ' . date('H:i:s') . '.');
			$showMatrix = true;
		}
		catch (\Exception $error) {
			$db->rollBack();
			error('Failed to save permissions: ' . $error->getMessage());
			$showMatrix = true;
		}
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
	'selectedAccountId' => $selectedAccountId,
	'pages' => $pages,
	'selected' => $selected,
	'operations' => $operations,
]);
