<?php

$db->exec('ALTER TABLE `' . TABLE_PREFIX . 'monsters` MODIFY `loot` VARCHAR(10000) NOT NULL;');