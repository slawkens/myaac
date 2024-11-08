<?php

use MyAAC\Models\Pages;

$up = function () {
	$rulesOnPage = Pages::where('name', 'rules_on_the_page')->first();
	if (!$rulesOnPage) {
		Pages::create([
			'name' => 'rules_on_the_page',
			'title' => 'Rules',
			'body' => file_get_contents(__DIR__ . '/30-rules.txt'),
			'date' => time(),
			'player_id' => 1,
			'php' => 0,
			'enable_tinymce' => 0,
			'access' => 0,
			'hidden' => 0,
		]);
	}
};

$down = function () {
	$rulesOnPage = Pages::where('name', 'rules_on_the_page')->first();
	if ($rulesOnPage) {
		Pages::where('name', 'rules_on_the_page')->delete();
	}
};
