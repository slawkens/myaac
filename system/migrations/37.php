<?php

// 2023-11-11
// Add Guest page access

use MyAAC\Models\Pages;

Pages::query()->where('access', 1)->update(['access' => 0]);
