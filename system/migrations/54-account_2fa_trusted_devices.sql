CREATE TABLE `myaac_account_2fa_trusted_devices`
(
	`id` int AUTO_INCREMENT PRIMARY KEY,
	`account_id` int NOT NULL,
	`device_token_hash` varchar(64) NOT NULL,
	`user_agent` varchar(255) NOT NULL,
	`expires_at` timestamp NOT NULL,
	`type` tinyint NOT NULL,
	`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	INDEX(account_id),
	INDEX(device_token_hash)
);
