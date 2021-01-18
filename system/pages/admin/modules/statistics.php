<?php
defined('MYAAC') or die('Direct access not allowed!');
$count = $db->query('SELECT
  (SELECT COUNT(*) FROM `accounts`) as total_accounts, 
  (SELECT COUNT(*) FROM `players`) as total_players,
  (SELECT COUNT(*) FROM `guilds`) as total_guilds,
  (SELECT COUNT(*) FROM `' . TABLE_PREFIX . 'monsters`) as total_monsters,
  (SELECT COUNT(*) FROM `houses`) as total_houses;')->fetch();

$twig->display('statistics.html.twig', array(
	'count' => $count,
));
