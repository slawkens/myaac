<?php
	if(fieldExist('name', TABLE_PREFIX . 'screenshots'))
		$db->query("ALTER TABLE `" . TABLE_PREFIX . "screenshots` DROP `name`;");
?>