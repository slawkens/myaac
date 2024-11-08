CREATE TABLE `z_polls` (
	`id` int(11) NOT NULL auto_increment,
	`question` varchar(255) NOT NULL,
	`description` varchar(255) NOT NULL,
	`end` int(11) NOT NULL DEFAULT 0,
	`start` int(11) NOT NULL DEFAULT 0,
	`answers` int(11) NOT NULL DEFAULT 0,
	`votes_all` int(11) NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;
