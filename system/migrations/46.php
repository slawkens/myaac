<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	if ($db->hasColumn('accounts', 'email_hash')) {
		$db->dropColumn('accounts', 'email_hash');
	}

	if (!$db->hasTable(TABLE_PREFIX . 'account_emails_verify')) {
		$db->query(file_get_contents(__DIR__ . '/46-account_emails_verify.sql'));
	}
};

$down = function () use ($db) {
	if (!$db->hasColumn('accounts', 'email_hash')) {
		$db->addColumn('accounts', 'email_hash', "varchar(32) NOT NULL DEFAULT ''");
	}

	if ($db->hasTable(TABLE_PREFIX . 'account_emails_verify')) {
		$db->dropTable(TABLE_PREFIX . 'account_emails_verify');
	}
};
