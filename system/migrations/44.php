<?php

// 2025-05-14
// update pages links
// server-info conflicts with apache2 mod
// Changelog conflicts with changelog files

use MyAAC\Models\Menu;
use MyAAC\Models\Pages;

$up = function() {
	Menu::where('link', 'server-info')->update(['link' => 'ots-info']);
	Menu::where('link', 'changelog')->update(['link' => 'change-log']);

	Pages::where('name', 'rules_on_the_page')->update(['hide' => 1]);

	$rules = Pages::where('name', 'rules')->first();
	if (!$rules) {
		Pages::create([
			'name' => 'rules',
			'title' => 'Server Rules',
			'body' => '<b>{{ config.lua.serverName }} Rules</b><br/>' . nl2br(file_get_contents(__DIR__ . '/30-rules.txt')),
			'date' => time(),
			'player_id' => 1,
			'php' => 0,
			'enable_tinymce' => 0,
			'access' => 0,
			'hide' => 0,
		]);
	}
};

$down = function() {
	Menu::where('link', 'ots-info')->update(['link' => 'server-info']);
	Menu::where('link', 'change-log')->update(['link' => 'changelog']);

	Pages::where('name', 'rules_on_the_page')->update(['hide' => 0]);
};

