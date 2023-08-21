<?php

if(!$db->hasTable('myaac_menu')) {
	$db->query("
CREATE TABLE `myaac_menu`
(
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`template` VARCHAR(255) NOT NULL,
	`name` VARCHAR(255) NOT NULL,
	`link` VARCHAR(255) NOT NULL,
	`category` INT(11) NOT NULL DEFAULT 1,
	`ordering` INT(11) NOT NULL DEFAULT 0,
	`enabled` INT(1) NOT NULL DEFAULT 1,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;
");

	require_once LIBS . 'plugins.php';
	Plugins::installMenus('kathrine', require TEMPLATES . 'kathrine/menus.php');
	Plugins::installMenus('tibiacom', require TEMPLATES . 'tibiacom/menus.php');
}
