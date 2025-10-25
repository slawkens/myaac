CREATE TABLE IF NOT EXISTS `myaac_account_actions`
(
	`account_id` int NOT NULL,
	`ip` int unsigned NOT NULL DEFAULT 0,
	`ipv6` binary(16) NOT NULL DEFAULT 0,
	`date` int NOT NULL DEFAULT 0,
	`action` varchar(255) NOT NULL DEFAULT '',
	KEY (`account_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `myaac_account_emails_verify`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`account_id` int NOT NULL,
	`hash` varchar(32) NOT NULL,
	`sent_at` int NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `myaac_admin_menu`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL DEFAULT '',
	`page` varchar(255) NOT NULL DEFAULT '',
	`ordering` int NOT NULL DEFAULT 0,
	`flags` int NOT NULL DEFAULT 0,
	`enabled` int NOT NULL DEFAULT 1,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `myaac_changelog`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`body` varchar(500) NOT NULL DEFAULT '',
	`type` tinyint NOT NULL DEFAULT 0 COMMENT '1 - added, 2 - removed, 3 - changed, 4 - fixed',
	`where` tinyint NOT NULL DEFAULT 0 COMMENT '1 - server, 2 - site',
	`date` int NOT NULL DEFAULT 0,
	`player_id` int NOT NULL DEFAULT 0,
	`hide` tinyint NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `myaac_config`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`name` varchar(30) NOT NULL,
	`value` varchar(1000) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE (`name`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `myaac_faq`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`question` varchar(255) NOT NULL DEFAULT '',
	`answer` varchar(1020) NOT NULL DEFAULT '',
	`ordering` int NOT NULL DEFAULT 0,
	`hide` tinyint NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `myaac_forum_boards`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`name` varchar(32) NOT NULL,
	`description` varchar(255) NOT NULL DEFAULT '',
	`ordering` int NOT NULL DEFAULT 0,
	`guild` int NOT NULL DEFAULT 0,
	`access` int NOT NULL DEFAULT 0,
	`closed` tinyint NOT NULL DEFAULT 0,
	`hide` tinyint NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `myaac_forum`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`first_post` int NOT NULL DEFAULT 0,
	`last_post` int NOT NULL DEFAULT 0,
	`section` int NOT NULL DEFAULT 0,
	`replies` int NOT NULL DEFAULT 0,
	`views` int NOT NULL DEFAULT 0,
	`author_aid` int NOT NULL DEFAULT 0,
	`author_guid` int NOT NULL DEFAULT 0,
	`post_text` text NOT NULL,
	`post_topic` varchar(255) NOT NULL DEFAULT '',
	`post_smile` tinyint NOT NULL DEFAULT 0,
	`post_html` tinyint NOT NULL DEFAULT 0,
	`post_date` int NOT NULL DEFAULT 0,
	`last_edit_aid` int NOT NULL DEFAULT 0,
	`edit_date` int NOT NULL DEFAULT 0,
	`post_ip` varchar(45) NOT NULL DEFAULT '0.0.0.0',
	`sticked` tinyint NOT NULL DEFAULT 0,
	`closed` tinyint NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `section` (`section`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `myaac_menu`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`template` varchar(255) NOT NULL,
	`name` varchar(255) NOT NULL,
	`link` varchar(255) NOT NULL,
	`blank` tinyint NOT NULL DEFAULT 0,
	`color` varchar(6) NOT NULL DEFAULT '',
	`category` int NOT NULL DEFAULT 1,
	`ordering` int NOT NULL DEFAULT 0,
	`enabled` int NOT NULL DEFAULT 1,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `myaac_monsters` (
	`id` int NOT NULL AUTO_INCREMENT,
	`hide` tinyint NOT NULL DEFAULT 0,
	`name` varchar(255) NOT NULL,
	`mana` int NOT NULL DEFAULT 0,
	`exp` int NOT NULL,
	`health` int NOT NULL,
	`look` varchar(255) NOT NULL DEFAULT '',
	`speed_lvl` int NOT NULL DEFAULT 1,
	`use_haste` tinyint NOT NULL,
	`voices` text NOT NULL,
	`immunities` varchar(255) NOT NULL,
	`elements` text NOT NULL,
	`summonable` tinyint NOT NULL,
	`convinceable` tinyint NOT NULL,
	`pushable` tinyint NOT NULL DEFAULT 0,
	`canpushitems` tinyint NOT NULL DEFAULT 0,
	`canwalkonenergy` tinyint NOT NULL DEFAULT 0,
	`canwalkonpoison` tinyint NOT NULL DEFAULT 0,
	`canwalkonfire` tinyint NOT NULL DEFAULT 0,
	`runonhealth` tinyint NOT NULL DEFAULT 0,
	`hostile` tinyint NOT NULL DEFAULT 0,
	`attackable` tinyint NOT NULL DEFAULT 0,
	`rewardboss` tinyint NOT NULL DEFAULT 0,
	`defense` int NOT NULL DEFAULT 0,
	`armor` int NOT NULL DEFAULT 0,
	`canpushcreatures` tinyint NOT NULL DEFAULT 0,
	`race` varchar(255) NOT NULL,
	`loot` text NOT NULL,
	`summons` text NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `myaac_news`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`title` varchar(100) NOT NULL,
	`body` text NOT NULL,
	`type` tinyint NOT NULL DEFAULT 0 COMMENT '1 - news, 2 - ticker, 3 - article',
	`date` int NOT NULL DEFAULT 0,
	`category` tinyint NOT NULL DEFAULT 0,
	`player_id` int NOT NULL DEFAULT 0,
	`last_modified_by` int NOT NULL DEFAULT 0,
	`last_modified_date` int NOT NULL DEFAULT 0,
	`comments` varchar(50) NOT NULL DEFAULT '',
	`article_text` varchar(300) NOT NULL DEFAULT '',
	`article_image` varchar(100) NOT NULL DEFAULT '',
	`hide` tinyint NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `myaac_news_categories`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`name` varchar(50) NOT NULL DEFAULT "",
	`description` varchar(50) NOT NULL DEFAULT "",
	`icon_id` int NOT NULL DEFAULT 0,
	`hide` tinyint NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `myaac_notepad`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`account_id` int NOT NULL,
	/*`name` varchar(30) NOT NULL,*/
	`content` text NOT NULL,
	/*`public` tinyint NOT NULL DEFAULT 0*/
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `myaac_pages`
(
	`id` INT NOT NULL AUTO_INCREMENT,
	`name` varchar(30) NOT NULL,
	`title` varchar(30) NOT NULL,
	`body` text NOT NULL,
	`date` int NOT NULL DEFAULT 0,
	`player_id` int NOT NULL DEFAULT 0,
	`php` tinyint NOT NULL DEFAULT 0 COMMENT '0 - plain html, 1 - php',
	`enable_tinymce` tinyint NOT NULL DEFAULT 1 COMMENT '1 - enabled, 0 - disabled',
	`access` tinyint NOT NULL DEFAULT 0,
	`hide` tinyint NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	UNIQUE (`name`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

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

CREATE TABLE IF NOT EXISTS `myaac_settings`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL DEFAULT '',
	`key` varchar(255) NOT NULL DEFAULT '',
	`value` text NOT NULL,
	PRIMARY KEY (`id`),
	KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `myaac_spells`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`spell` varchar(255) NOT NULL DEFAULT '',
	`name` varchar(255) NOT NULL,
	`words` varchar(255) NOT NULL DEFAULT '',
	`category` tinyint NOT NULL DEFAULT 0 COMMENT '1 - attack, 2 - healing, 3 - summon, 4 - supply, 5 - support',
	`type` tinyint NOT NULL DEFAULT 0 COMMENT '1 - instant, 2 - conjure, 3 - rune',
	`level` int NOT NULL DEFAULT 0,
	`maglevel` int NOT NULL DEFAULT 0,
	`mana` int NOT NULL DEFAULT 0,
	`soul` tinyint NOT NULL DEFAULT 0,
	`conjure_id` int NOT NULL DEFAULT 0,
	`conjure_count` tinyint NOT NULL DEFAULT 0,
	`reagent` int NOT NULL DEFAULT 0,
	`item_id` int NOT NULL DEFAULT 0,
	`premium` tinyint NOT NULL DEFAULT 0,
	`vocations` varchar(100) NOT NULL DEFAULT '',
	`hide` tinyint NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	UNIQUE (`name`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `myaac_visitors`
(
	`ip` varchar(45) NOT NULL,
	`lastvisit` int NOT NULL DEFAULT 0,
	`page` varchar(2048) NOT NULL,
	`user_agent` varchar(255) NOT NULL DEFAULT '',
	UNIQUE (`ip`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;

CREATE TABLE IF NOT EXISTS `myaac_weapons`
(
	`id` int NOT NULL,
	`level` int NOT NULL DEFAULT 0,
	`maglevel` int NOT NULL DEFAULT 0,
	`vocations` varchar(100) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4;
