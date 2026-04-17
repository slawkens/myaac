<?php
/**
 * 2026-04-12
 * Add indexes to myaac_account_actions table
 */
$up = function () use ($db) {
	$db->query("CREATE INDEX `myaac_account_actions_account_id` ON `myaac_account_actions` (`account_id`);");
	$db->query("CREATE INDEX `myaac_account_actions_ip` ON `myaac_account_actions` (`ip`);");
};

$down = function () {
	// nothing to do, to not lose data
};
