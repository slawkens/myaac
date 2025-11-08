<?php
$config['menu_default_links_color'] = '#ffffff';

// max 7 menus for kathrine
$config['menu_categories'] = [
	MENU_CATEGORY_NEWS => ['id' => 'news', 'name' => 'Latest News'],
	// you can add custom menu by uncommenting this
	// after doing it, go to admin panel -> Menus and add your entries for this category
	// tip: you can move it up/down to show it on specific position
	//7 => array('id' => 'testing', 'name' => 'Test Menu 1'),
	//8 => array('id' => 'testing2', 'name' => 'Test Menu 2'),
	MENU_CATEGORY_ACCOUNT => ['id' => 'account', 'name' => 'Account'],
	MENU_CATEGORY_COMMUNITY => ['id' => 'community', 'name' => 'Community'],
	MENU_CATEGORY_LIBRARY => ['id' => 'library', 'name' => 'Library'],
	MENU_CATEGORY_SHOP => ['id' => 'shops', 'name' => 'Shop']
];

$config['menus'] = require __DIR__ . '/menus.php';
