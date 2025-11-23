<?php
/**
 * @var OTS_DB_MySQL $db
 */

// 2025-02-27
// remove ipv6, change to ip (for both ipv4 + ipv6) as VARCHAR(45)
$up = function () use ($db) {
	$accountActionsInfo = $db->getColumnInfo(TABLE_PREFIX . 'account_actions', 'account_id');
	if ($accountActionsInfo && is_array($accountActionsInfo) && $accountActionsInfo['key'] == 'pri') {
		$db->query("ALTER TABLE `myaac_account_actions` DROP KEY `account_id`;");
	}

	if (!$db->hasColumn(TABLE_PREFIX . 'account_actions', 'id')) {
		$db->addColumn(TABLE_PREFIX . 'account_actions', 'id', 'INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`)');
	}

	$db->modifyColumn(TABLE_PREFIX . 'account_actions', 'ip', "VARCHAR(45) NOT NULL DEFAULT ''");
	$db->query("UPDATE `" . TABLE_PREFIX . "account_actions` SET `ip` = INET_NTOA(`ip`) WHERE `ip` != '0';");
	$db->query("UPDATE `" . TABLE_PREFIX . "account_actions` SET `ip` = INET6_NTOA(`ipv6`) WHERE `ip` = '0';");

	if ($db->hasColumn(TABLE_PREFIX . 'account_actions', 'ipv6')) {
		$db->dropColumn(TABLE_PREFIX . 'account_actions', 'ipv6');
	}
};

$down = function () use ($db) {
	if ($db->hasColumn(TABLE_PREFIX . 'account_actions', 'id')) {
		$db->query("ALTER TABLE `" . TABLE_PREFIX . "account_actions` DROP `id`;");
	}

	$db->query("ALTER TABLE  `" . TABLE_PREFIX . "account_actions` ADD KEY (`account_id`);");

	if (!$db->hasColumn(TABLE_PREFIX . 'account_actions', 'ipv6')) {
		$db->addColumn(TABLE_PREFIX . 'account_actions', 'ipv6', "BINARY(16) NOT NULL DEFAULT 0x00000000000000000000000000000000 AFTER ip");
	}

	$db->query("UPDATE `" . TABLE_PREFIX . "account_actions` SET `ipv6` = INET6_ATON(ip) WHERE NOT IS_IPV4(`ip`);");
	$db->query("UPDATE `" . TABLE_PREFIX . "account_actions` SET `ip` = INET_ATON(`ip`) WHERE IS_IPV4(`ip`);");
	$db->query("UPDATE `" . TABLE_PREFIX . "account_actions` SET `ip` = 0 WHERE `ipv6` != 0x00000000000000000000000000000000;");
	$db->modifyColumn(TABLE_PREFIX . 'account_actions', 'ip', "INT(11) UNSIGNED NOT NULL DEFAULT 0;");
};
