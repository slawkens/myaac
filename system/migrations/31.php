<?php

$db->exec("CREATE TABLE `myaac_settings`
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`plugin_name` VARCHAR(255) NOT NULL DEFAULT '',
	`key` VARCHAR(255) NOT NULL DEFAULT '',
	`value` TEXT NOT NULL,
	PRIMARY KEY (`id`),
	KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;");