CREATE TABLE IF NOT EXISTS `myaac_gallery`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`comment` varchar(255) NOT NULL DEFAULT '',
	`image` varchar(255) NOT NULL,
	`thumb` varchar(255) NOT NULL,
	`author` varchar(50) NOT NULL DEFAULT '',
	`ordering` int NOT NULL DEFAULT 0,
	`hide` tinyint NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;
