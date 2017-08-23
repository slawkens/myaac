<?php
	if(fieldExist('cities', TABLE_PREFIX . 'spells'))
		$db->query("ALTER TABLE `" . TABLE_PREFIX . "spells` DROP COLUMN cities;");
?>