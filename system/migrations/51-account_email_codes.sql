CREATE TABLE `myaac_account_email_codes`
(
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`account_id` int NOT NULL,
	`code` varchar(6) NOT NULL,
	`created_at` int NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;
