SET @myaac_database_version = 32;

CREATE TABLE `myaac_account_actions`
(
	`account_id` int NOT NULL,
	`ip` int UNSIGNED NOT NULL DEFAULT '0',
	`ipv6` binary(16) NOT NULL DEFAULT '0',
	`date` int NOT NULL DEFAULT '0',
	`action` varchar(255) NOT NULL DEFAULT '',
	KEY (`account_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE `myaac_admin_menu`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL DEFAULT '',
	`page` varchar(255) NOT NULL DEFAULT '',
	`ordering` int NOT NULL DEFAULT '0',
	`flags` int NOT NULL DEFAULT '0',
	`enabled` int NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE `myaac_bugtracker`
(
	`account` varchar(255) NOT NULL,
	`type` int NOT NULL DEFAULT '0',
	`status` int NOT NULL DEFAULT '0',
	`text` text NOT NULL,
	`id` int NOT NULL DEFAULT '0',
	`subject` varchar(255) NOT NULL DEFAULT '',
	`reply` int NOT NULL DEFAULT '0',
	`who` int NOT NULL DEFAULT '0',
	`uid` int NOT NULL AUTO_INCREMENT,
	`tag` int NOT NULL DEFAULT '0',
	PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE `myaac_changelog`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`body` varchar(500) NOT NULL DEFAULT '',
	`type` tinyint NOT NULL DEFAULT '0' COMMENT '1 - added, 2 - removed, 3 - changed, 4 - fixed',
	`where` tinyint NOT NULL DEFAULT '0' COMMENT '1 - server, 2 - site',
	`date` int NOT NULL DEFAULT '0',
	`player_id` int NOT NULL DEFAULT '0',
	`hidden` tinyint NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

INSERT INTO `myaac_changelog` (`id`, `type`, `where`, `date`, `body`, `hidden`) VALUES (1, 3, 2, UNIX_TIMESTAMP(), 'MyAAC installed. (:', 0);

CREATE TABLE `myaac_config`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`name` varchar(30) NOT NULL,
	`value` varchar(1000) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE (`name`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

INSERT INTO `myaac_config` (`name`, `value`) VALUES ('database_version', @myaac_database_version);

CREATE TABLE `myaac_faq`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`question` varchar(255) NOT NULL DEFAULT '',
	`answer` varchar(1020) NOT NULL DEFAULT '',
	`ordering` int NOT NULL DEFAULT '0',
	`hidden` tinyint NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE `myaac_forum_boards`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`name` varchar(32) NOT NULL,
	`description` varchar(255) NOT NULL DEFAULT '',
	`ordering` int NOT NULL DEFAULT '0',
	`guild` int NOT NULL DEFAULT '0',
	`access` int NOT NULL DEFAULT '0',
	`closed` tinyint NOT NULL DEFAULT '0',
	`hidden` tinyint NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;
INSERT INTO `myaac_forum_boards` (`id`, `name`, `description`, `ordering`, `closed`) VALUES (NULL, 'News', 'News commenting', 0, 1);
INSERT INTO `myaac_forum_boards` (`id`, `name`, `description`, `ordering`) VALUES (NULL, 'Trade', 'Trade offers.', 1);
INSERT INTO `myaac_forum_boards` (`id`, `name`, `description`, `ordering`) VALUES (NULL, 'Quests', 'Quest making.', 2);
INSERT INTO `myaac_forum_boards` (`id`, `name`, `description`, `ordering`) VALUES (NULL, 'Pictures', 'Your pictures.', 3);
INSERT INTO `myaac_forum_boards` (`id`, `name`, `description`, `ordering`) VALUES (NULL, 'Bug Report', 'Report bugs there.', 4);

CREATE TABLE `myaac_forum`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`first_post` int NOT NULL DEFAULT '0',
	`last_post` int NOT NULL DEFAULT '0',
	`section` int NOT NULL DEFAULT '0',
	`replies` int NOT NULL DEFAULT '0',
	`views` int NOT NULL DEFAULT '0',
	`author_aid` int NOT NULL DEFAULT '0',
	`author_guid` int NOT NULL DEFAULT '0',
	`post_text` text NOT NULL,
	`post_topic` varchar(255) NOT NULL DEFAULT '',
	`post_smile` tinyint NOT NULL DEFAULT '0',
	`post_html` tinyint NOT NULL DEFAULT '0',
	`post_date` int NOT NULL DEFAULT '0',
	`last_edit_aid` int NOT NULL DEFAULT '0',
	`edit_date` int NOT NULL DEFAULT '0',
	`post_ip` varchar(32) NOT NULL DEFAULT '0.0.0.0',
	`sticked` tinyint NOT NULL DEFAULT '0',
	`closed` tinyint NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `section` (`section`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE `myaac_menu`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`template` varchar(255) NOT NULL,
	`name` varchar(255) NOT NULL,
	`link` varchar(255) NOT NULL,
	`blank` tinyint NOT NULL DEFAULT '0',
	`color` varchar(6) NOT NULL DEFAULT '',
	`category` int NOT NULL DEFAULT '1',
	`ordering` int NOT NULL DEFAULT '0',
	`enabled` int NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

/* MENU_CATEGORY_NEWS kathrine */
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Latest News', 'news', 1, 0);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'News Archive', 'news/archive', 1, 1);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Changelog', 'changelog', 1, 2);
/* MENU_CATEGORY_ACCOUNT kathrine */
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Account Management', 'account/manage', 2, 0);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Create Account', 'account/create', 2, 1);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Lost Account?', 'account/lost', 2, 2);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Server Rules', 'rules', 2, 3);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Downloads', 'downloads', 5, 4);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Report Bug', 'bugtracker', 2, 5);
/* MENU_CATEGORY_COMMUNITY kathrine */
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Who is Online?', 'online', 3, 0);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Characters', 'characters', 3, 1);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Guilds', 'guilds', 3, 2);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Highscores', 'highscores', 3, 3);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Last Deaths', 'lastkills', 3, 4);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Houses', 'houses', 3, 5);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Bans', 'bans', 3, 6);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Forum', 'forum', 3, 7);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Team', 'team', 3, 8);
/* MENU_CATEGORY_LIBRARY kathrine */
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Monsters', 'creatures', 5, 0);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Spells', 'spells', 5, 1);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Server Info', 'serverInfo', 5, 2);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Commands', 'commands', 5, 3);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Gallery', 'gallery', 5, 4);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Experience Table', 'experienceTable', 5, 5);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'FAQ', 'faq', 5, 6);
/* MENU_CATEGORY_SHOP kathrine */
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Buy Points', 'points', 6, 0);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Shop Offer', 'gifts', 6, 1);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('kathrine', 'Shop History', 'gifts/history', 6, 2);
/* MENU_CATEGORY_NEWS tibiacom */
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Latest News', 'news', 1, 0);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'News Archive', 'news/archive', 1, 1);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Changelog', 'changelog', 1, 2);
/* MENU_CATEGORY_ACCOUNT tibiacom */
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Account Management', 'account/manage', 2, 0);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Create Account', 'account/create', 2, 1);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Lost Account?', 'account/lost', 2, 2);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Server Rules', 'rules', 2, 3);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Downloads', 'downloads', 2, 4);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Report Bug', 'bugtracker', 2, 5);
/* MENU_CATEGORY_COMMUNITY tibiacom */
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Characters', 'characters', 3, 0);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Who Is Online?', 'online', 3, 1);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Highscores', 'highscores', 3, 2);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Last Kills', 'lastkills', 3, 3);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Houses', 'houses', 3, 4);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Guilds', 'guilds', 3, 5);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Polls', 'polls', 3, 6);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Bans', 'bans', 3, 7);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Support List', 'team', 3, 8);
/* MENU_CATEGORY_FORUM tibiacom */
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Forum', 'forum', 4, 0);
/* MENU_CATEGORY_LIBRARY tibiacom */
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Creatures', 'creatures', 5, 0);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Spells', 'spells', 5, 1);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Commands', 'commands', 5, 2);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Exp Stages', 'experienceStages', 5, 3);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Gallery', 'gallery', 5, 4);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Server Info', 'serverInfo', 5, 5);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Experience Table', 'experienceTable', 5, 6);
/* MENU_CATEGORY_SHOP tibiacom */
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Buy Points', 'points', 6, 0);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Shop Offer', 'gifts', 6, 1);
INSERT INTO `myaac_menu` (`template`, `name`, `link`, `category`, `ordering`) VALUES ('tibiacom', 'Shop History', 'gifts/history', 6, 2);

CREATE TABLE `myaac_monsters` (
	`id` int NOT NULL AUTO_INCREMENT,
	`hidden` tinyint NOT NULL DEFAULT '0',
	`name` varchar(255) NOT NULL,
	`mana` int NOT NULL DEFAULT '0',
	`exp` int NOT NULL,
	`health` int NOT NULL,
	`speed_lvl` int NOT NULL DEFAULT '1',
	`use_haste` tinyint NOT NULL,
	`voices` text NOT NULL,
	`immunities` varchar(255) NOT NULL,
	`summonable` tinyint NOT NULL,
	`convinceable` tinyint NOT NULL,
	`race` varchar(255) NOT NULL,
	`loot` text NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE `myaac_videos`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`title` varchar(100) NOT NULL DEFAULT '',
	`youtube_id` varchar(20) NOT NULL,
	`author` varchar(50) NOT NULL DEFAULT '',
	`ordering` int NOT NULL DEFAULT '0',
	`hidden` tinyint NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE `myaac_news`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`title` varchar(100) NOT NULL,
	`body` text NOT NULL,
	`type` tinyint NOT NULL DEFAULT '0' COMMENT '1 - news, 2 - ticker, 3 - article',
	`date` int NOT NULL DEFAULT '0',
	`category` tinyint NOT NULL DEFAULT '0',
	`player_id` int NOT NULL DEFAULT '0',
	`last_modified_by` int NOT NULL DEFAULT '0',
	`last_modified_date` int NOT NULL DEFAULT '0',
	`comments` varchar(50) NOT NULL DEFAULT '',
	`article_text` varchar(300) NOT NULL DEFAULT '',
	`article_image` varchar(100) NOT NULL DEFAULT '',
	`hidden` tinyint NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE `myaac_news_categories`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`name` varchar(50) NOT NULL DEFAULT '',
	`description` varchar(50) NOT NULL DEFAULT '',
	`icon_id` int NOT NULL DEFAULT '0',
	`hidden` tinyint NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

INSERT INTO `myaac_news_categories` (`id`, `icon_id`) VALUES (NULL, 0);
INSERT INTO `myaac_news_categories` (`id`, `icon_id`) VALUES (NULL, 1);
INSERT INTO `myaac_news_categories` (`id`, `icon_id`) VALUES (NULL, 2);
INSERT INTO `myaac_news_categories` (`id`, `icon_id`) VALUES (NULL, 3);
INSERT INTO `myaac_news_categories` (`id`, `icon_id`) VALUES (NULL, 4);

CREATE TABLE `myaac_notepad`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`account_id` int NOT NULL,
	/*`name` varchar(30) NOT NULL,*/
	`content` text NOT NULL,
	/*`public` tinyint NOT NULL DEFAULT '0'*/
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE `myaac_pages`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`name` varchar(30) NOT NULL,
	`title` varchar(30) NOT NULL,
	`body` text NOT NULL,
	`date` int NOT NULL DEFAULT '0',
	`player_id` int NOT NULL DEFAULT '0',
	`php` tinyint NOT NULL DEFAULT '0' COMMENT '0 - plain html, 1 - php',
	`enable_tinymce` tinyint NOT NULL DEFAULT '1' COMMENT '1 - enabled, 0 - disabled',
	`access` tinyint NOT NULL DEFAULT '0',
	`hidden` tinyint NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE (`name`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE `myaac_gallery`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`comment` varchar(255) NOT NULL DEFAULT '',
	`image` varchar(255) NOT NULL,
	`thumb` varchar(255) NOT NULL,
	`author` varchar(50) NOT NULL DEFAULT '',
	`ordering` int NOT NULL DEFAULT '0',
	`hidden` tinyint NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

INSERT INTO `myaac_gallery` (`id`, `ordering`, `comment`, `image`, `thumb`, `author`) VALUES (NULL, 1, 'Demon', 'images/gallery/demon.jpg', 'images/gallery/demon_thumb.gif', 'MyAAC');

CREATE TABLE `myaac_spells`
(
	`id` int NOT NULL AUTO_INCREMENT,
	`spell` varchar(255) NOT NULL DEFAULT '',
	`name` varchar(255) NOT NULL,
	`words` varchar(255) NOT NULL DEFAULT '',
	`category` tinyint NOT NULL DEFAULT '0' COMMENT '1 - attack, 2 - healing, 3 - summon, 4 - supply, 5 - support',
	`type` tinyint NOT NULL DEFAULT '0' COMMENT '1 - instant, 2 - conjure, 3 - rune',
	`level` int NOT NULL DEFAULT '0',
	`maglevel` int NOT NULL DEFAULT '0',
	`mana` int NOT NULL DEFAULT '0',
	`soul` tinyint NOT NULL DEFAULT '0',
	`conjure_id` int NOT NULL DEFAULT '0',
	`conjure_count` tinyint NOT NULL DEFAULT '0',
	`reagent` int NOT NULL DEFAULT '0',
	`item_id` int NOT NULL DEFAULT '0',
	`premium` tinyint NOT NULL DEFAULT '0',
	`vocations` varchar(100) NOT NULL DEFAULT '',
	`hidden` tinyint NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE (`name`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE `myaac_visitors`
(
	`ip` varchar(16) NOT NULL,
	`lastvisit` int NOT NULL DEFAULT '0',
	`page` varchar(2048) NOT NULL,
	UNIQUE (`ip`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

CREATE TABLE `myaac_weapons`
(
	`id` int NOT NULL,
	`level` int NOT NULL DEFAULT '0',
	`maglevel` int NOT NULL DEFAULT '0',
	`vocations` varchar(100) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;
