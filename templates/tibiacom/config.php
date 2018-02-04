<?php
$config['darkborder'] = "#D4C0A1";
$config['lightborder'] = "#F1E0C6";
$config['vdarkborder'] = "#505050";
$config['logo_monster'] = "Wyrm";
// separated by comma
// sequence is important! they will be shown in same order that you add them to the list
// List: newcomer,gallery,premium,poll,highscores,networks
$config['boxes'] = "highscores,newcomer,gallery,networks,poll";
$config['network_facebook'] = 'tibia'; // leave empty to disable
$config['network_twitter'] = 'tibia'; // leave empty to disable

$config['background_image'] = "background-artwork.jpg";
$config['logo_image'] = "tibia-logo-artwork-top.gif";
$config['gallery_image'] = 1;
$config['menu_categories'] = array(
	MENU_CATEGORY_NEWS => array('id' => 'news', 'name' => 'Latest News'),
	MENU_CATEGORY_ACCOUNT => array('id' => 'account', 'name' => 'Account'),
	MENU_CATEGORY_COMMUNITY => array('id' => 'community', 'name' => 'Community'),
	MENU_CATEGORY_FORUM => array('id' => 'forum', 'name' => 'Forum'),
	MENU_CATEGORY_LIBRARY => array('id' => 'library', 'name' => 'Library'),
	MENU_CATEGORY_SHOP => array('id' => 'shops', 'name' => 'Shop')
);
?>
