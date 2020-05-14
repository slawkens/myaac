<?php

$db->exec("CREATE TABLE `myaac_options_bool`
(
	`name` VARCHAR(255) NOT NULL,
	`key` VARCHAR(255) NOT NULL,
	`value` INT(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (`key`),
	UNIQUE (`key`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE `myaac_options_double`
(
	`name` VARCHAR(255) NOT NULL,
	`key` VARCHAR(255) NOT NULL,
	`value` DOUBLE NOT NULL DEFAULT 0,
	PRIMARY KEY (`key`),
	UNIQUE (`key`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE `myaac_options_int`
(
	`name` VARCHAR(255) NOT NULL,
	`key` VARCHAR(255) NOT NULL,
	`value` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY (`key`),
	UNIQUE (`key`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE `myaac_options_text`
(
	`name` VARCHAR(255) NOT NULL,
	`key` VARCHAR(255) NOT NULL,
	`value` TEXT NOT NULL,
	PRIMARY KEY (`key`),
	UNIQUE (`key`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE `myaac_options_varchar`
(
	`name` VARCHAR(255) NOT NULL,
	`key` VARCHAR(255) NOT NULL,
	`value` VARCHAR(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`key`),
	UNIQUE (`key`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;");