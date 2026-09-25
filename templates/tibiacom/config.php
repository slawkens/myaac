<?php
$config['menu_default_links_color'] = '#ffffff';

$config['menu_categories'] = [
	MENU_CATEGORY_NEWS => ['id' => 'news', 'name' => 'Latest News'],
	MENU_CATEGORY_ACCOUNT => ['id' => 'account', 'name' => 'Account'],
	MENU_CATEGORY_COMMUNITY => ['id' => 'community', 'name' => 'Community'],
	MENU_CATEGORY_FORUM => ['id' => 'forum', 'name' => 'Forum'],
	MENU_CATEGORY_LIBRARY => ['id' => 'library', 'name' => 'Library'],
	MENU_CATEGORY_SHOP => ['id' => 'shops', 'name' => 'Shop'],
];

$config['logo_monster'] = [
	'name' => 'Wyrm', // Will be used to generate a link to monsters page
	'type' => 291,
	//'typeEx' => 2160, // Item image ID for the logo monster
	'addons' => 0,
	'head' => 0,
	'body' => 0,
	'legs' => 0,
	'feet' => 0,
];

$config['menus'] = require __DIR__ . '/menus.php';
