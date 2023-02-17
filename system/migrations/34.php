<?php
// add user_agent column into visitors

$db->exec('ALTER TABLE `' . TABLE_PREFIX . "visitors` ADD `user_agent` VARCHAR(255) NOT NULL DEFAULT '';");
