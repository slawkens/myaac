<?php
	if(fieldExist('spell', TABLE_PREFIX . 'spells'))
		$db->query("ALTER TABLE `" . TABLE_PREFIX . "spells` DROP COLUMN `spell`;");
?>