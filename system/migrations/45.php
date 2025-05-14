<?php

// 2025-05-14
// update pages links
// server-info conflicts with apache2 mod
// Changelog conflicts with changelog files

use MyAAC\Models\Pages;

$up = function() {
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
			'enable_tinymce' => 1,
			'access' => 0,
			'hide' => 0,
		]);
	}
};

$down = function() {
	Pages::where('name', 'rules_on_the_page')->update(['hide' => 0]);
};

