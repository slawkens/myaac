<?php
// add look column
$db->exec('ALTER TABLE `' . TABLE_PREFIX . "monsters` ADD `look` VARCHAR(255) NOT NULL AFTER `health`;");