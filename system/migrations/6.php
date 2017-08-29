<?php
	if(!fieldExist('enabled', TABLE_PREFIX . 'hooks'))
		$db->query("ALTER TABLE `" . TABLE_PREFIX . "hooks` ADD `enabled` INT(1) NOT NULL DEFAULT 1;");
?>