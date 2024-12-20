<?php

// 2024-02-03
// update pages links

use MyAAC\Models\Menu;

$up = function() {
	Menu::where('link', 'lastkills')->update(['link' => 'last-kills']);
	Menu::where('link', 'serverInfo')->update(['link' => 'server-info']);
	Menu::where('link', 'experienceStages')->update(['link' => 'exp-stages']);
	Menu::where('link', 'experienceTable')->update(['link' => 'exp-table']);
	Menu::where('link', 'creatures')->update(['link' => 'monsters']);
};

$down = function() {
	Menu::where('link', 'last-kills')->update(['link' => 'lastkills']);
	Menu::where('link', 'server-info')->update(['link' => 'serverInfo']);
	Menu::where('link', 'exp-stages')->update(['link' => 'experienceStages']);
	Menu::where('link', 'exp-table')->update(['link' => 'experienceTable']);
	Menu::where('link', 'monsters')->update(['link' => 'creatures']);
};

