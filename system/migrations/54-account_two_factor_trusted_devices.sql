CREATE TABLE IF NOT EXISTS `myaac_account_two_factor_trusted_devices`
(
	`id` char(36) PRIMARY KEY,
	`account_id` int NOT NULL,
	`device_token_hash` varchar(64) NOT NULL,
	`system` varchar(255) NOT NULL,
	`expires_at` timestamp NOT NULL,
	`type` tinyint NOT NULL COMMENT '1 - website, 2 - client, 0 - unknown',
	`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	INDEX(account_id),
	INDEX(device_token_hash)
);
