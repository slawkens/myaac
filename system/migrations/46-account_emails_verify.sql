CREATE TABLE `myaac_account_emails_verify`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`account_id` int NOT NULL,
	`hash` varchar(32) NOT NULL,
	`sent_at` int NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;
