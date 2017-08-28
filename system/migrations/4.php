<?php
	if(!fieldExist('id', TABLE_PREFIX . 'monsters'))
		$db->query("ALTER TABLE `" . TABLE_PREFIX . "monsters` ADD `id` int(11) NOT NULL AUTO_INCREMENT primary key FIRST;");
?>