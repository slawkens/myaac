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
};

$down = function() {
	Menu::where('link', 'ots-info')->update(['link' => 'server-info']);
	Menu::where('link', 'change-log')->update(['link' => 'changelog']);
};

