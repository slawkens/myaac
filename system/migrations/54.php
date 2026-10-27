<?php
// Two-factor authentication (2FA)

/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	if ($db->hasColumn('accounts', '2fa_type')) {
		$db->changeColumn('accounts', '2fa_type', 'two_factor_type', "tinyint NOT NULL DEFAULT 0");
	}
	elseif (!$db->hasColumn('accounts', 'two_factor_type')) {
		$db->addColumn('accounts', 'two_factor_type', "tinyint NOT NULL DEFAULT 0 AFTER `web_flags`");
	}

	if ($db->hasColumn('accounts', '2fa_secret')) {
		$db->changeColumn('accounts', '2fa_secret', 'two_factor_secret', "varchar(16) NOT NULL DEFAULT ''");
	}
	elseif (!$db->hasColumn('accounts', 'two_factor_secret')) {
		$db->addColumn('accounts', 'two_factor_secret', "varchar(16) NOT NULL DEFAULT '' AFTER `two_factor_type`");
	}

	// add myaac_account_two_factor_email_codes table
	if (!$db->hasTable(TABLE_PREFIX . 'account_two_factor_email_codes')) {
		$db->exec(file_get_contents(__DIR__ . '/54-account_two_factor_email_codes.sql'));
	}

	if (!$db->hasTable(TABLE_PREFIX . 'account_two_factor_trusted_devices')) {
		$db->exec(file_get_contents(__DIR__ . '/54-account_two_factor_trusted_devices.sql'));
	}
};

$down = function () use ($db) {
	if ($db->hasColumn('accounts', 'two_factor_type')) {
		$db->dropColumn('accounts', 'two_factor_type');
	}

	if ($db->hasColumn('accounts', 'two_factor_secret')) {
		$db->dropColumn('accounts', 'two_factor_secret');
	}

	//if ($db->hasTable(TABLE_PREFIX . 'account_two_factor_email_codes')) {
	//	$db->dropTable(TABLE_PREFIX . 'account_two_factor_email_codes');
	//}

	//if ($db->hasTable(TABLE_PREFIX . 'account_two_factor_trusted_devices')) {
	//	$db->dropTable(TABLE_PREFIX . 'account_two_factor_trusted_devices');
	//}
};
