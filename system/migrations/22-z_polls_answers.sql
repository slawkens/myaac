CREATE TABLE `z_polls_answers` (
	`poll_id` int(11) NOT NULL,
	`answer_id` int(11) NOT NULL,
	`answer` varchar(255) NOT NULL,
	`votes` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;
