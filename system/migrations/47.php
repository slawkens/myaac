<?php
// add the myaac_account_email_codes

/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	if (!$db->hasColumn('accounts', '2fa_type')) {
		$db->addColumn('accounts', '2fa_type', "tinyint NOT NULL DEFAULT 0 AFTER `web_flags`");
	}

	// add myaac_account_email_codes table
	if (!$db->hasTable(TABLE_PREFIX . 'account_email_codes')) {
		$db->exec(file_get_contents(__DIR__ . '/46-account_email_codes.sql'));
	}
};

$down = function () use ($db) {
	if ($db->hasColumn('accounts', '2fa_type')) {
		$db->dropColumn('accounts', '2fa_type');
	}

	//if ($db->hasTable(TABLE_PREFIX . 'account_email_codes')) {
	//	$db->dropTable(TABLE_PREFIX . 'account_email_codes');
	//}
};
