<?php

// 2023-11-11
// Add Guest page access

use MyAAC\Models\Pages;

$up = function () {
	Pages::query()->where('access', 1)->update(['access' => 0]);
};

$down = function () {
	Pages::query()->where('access', 0)->update(['access' => 1]);
};
