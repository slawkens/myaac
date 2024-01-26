<?php

use MyAAC\Cache\Cache;

$db->exec('DROP TABLE IF EXISTS `' . TABLE_PREFIX . 'hooks`;');

$cache = Cache::getInstance();
if($cache->enabled()) {
	$cache->delete('hooks');
}
