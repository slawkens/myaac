<?php
	if(!fieldExist('ordering', TABLE_PREFIX . 'hooks'))
		$db->query("ALTER TABLE `" . TABLE_PREFIX . "hooks` ADD `ordering` INT(11) NOT NULL DEFAULT 0 AFTER `file`;");
	
	if(!tableExist(TABLE_PREFIX . 'admin_menu'))
		$db->query("
CREATE TABLE `myaac_admin_menu`
(
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL DEFAULT '',
	`page` VARCHAR(255) NOT NULL DEFAULT '',
	`ordering` INT(11) NOT NULL DEFAULT 0,
	`flags` INT(11) NOT NULL DEFAULT 0,
	`enabled` INT(1) NOT NULL DEFAULT 1,
	PRIMARY KEY (`id`)
) ENGINE = MyISAM;");
?>