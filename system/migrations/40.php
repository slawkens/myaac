<?php

// 2024-02-03
// update pages links

use MyAAC\Models\Menu;

Menu::where('link', 'lastkills')->update(['link' => 'last-kills']);
Menu::where('link', 'serverInfo')->update(['link' => 'server-info']);
Menu::where('link', 'experienceStages')->update(['link' => 'exp-stages']);
Menu::where('link', 'experienceTable')->update(['link' => 'exp-table']);
Menu::where('link', 'creatures')->update(['link' => 'monsters']);
