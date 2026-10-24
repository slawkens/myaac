<?php
// 2fa
// add the myaac_account_2fa_email_codes

/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	if (!$db->hasColumn('accounts', '2fa_type')) {
		$db->addColumn('accounts', '2fa_type', "tinyint NOT NULL DEFAULT 0 AFTER `web_flags`");
	}

	if (!$db->hasColumn('accounts', '2fa_secret')) {
		$db->addColumn('accounts', '2fa_secret', "varchar(16) NOT NULL DEFAULT '' AFTER `2fa_type`");
	}

	// add myaac_account_2fa_email_codes table
	if (!$db->hasTable(TABLE_PREFIX . 'account_2fa_email_codes')) {
		$db->exec(file_get_contents(__DIR__ . '/54-account_2fa_email_codes.sql'));
	}

	if (!$db->hasTable(TABLE_PREFIX . 'account_2fa_trusted_devices')) {
		$db->exec(file_get_contents(__DIR__ . '/54-account_2fa_trusted_devices.sql'));
	}
};

$down = function () use ($db) {
	if ($db->hasColumn('accounts', '2fa_type')) {
		$db->dropColumn('accounts', '2fa_type');
	}

	if ($db->hasColumn('accounts', '2fa_secret')) {
		$db->dropColumn('accounts', '2fa_secret');
	}

	//if ($db->hasTable(TABLE_PREFIX . 'account_2fa_email_codes')) {
	//	$db->dropTable(TABLE_PREFIX . 'account_2fa_email_codes');
	//}

	//if ($db->hasTable(TABLE_PREFIX . 'account_2fa_trusted_devices')) {
	//	$db->dropTable(TABLE_PREFIX . 'account_2fa_trusted_devices');
	//}
};
