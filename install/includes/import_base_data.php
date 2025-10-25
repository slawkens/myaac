<?php
defined('MYAAC') or die('Direct access not allowed!');

use MyAAC\Models\Changelog;
use MyAAC\Models\Config;
use MyAAC\Models\ForumBoard;
use MyAAC\Models\Gallery;
use MyAAC\Models\NewsCategory;

if (Changelog::count() === 0) {
	Changelog::create([
		'type' => 3,
		'where' => 2,
		'date' => time(),
		'body' => 'MyAAC installed. (:',
		'hide' => 0,
	]);
}

if (Config::where('name', 'database_version')->count() === 0) {
	Config::create([
		'name' => 'database_version',
		'value' => DATABASE_VERSION,
	]);
}

if (ForumBoard::count() === 0) {
	$forumBoards = [
		['name' => 'News', 'description' => 'News commenting', 'closed' => 1],
		['name' => 'Trade', 'description' => 'Trade offers.', 'closed' => 0],
		['name' => 'Quests', 'description' => 'Quest making.', 'closed' => 0],
		['name' => 'Pictures', 'description' => 'Your pictures.', 'closed' => 0],
		['name' => 'Bug Report', 'description' => 'Report bugs there.', 'closed' => 0],
	];

	$i = 0;
	foreach ($forumBoards as $forumBoard) {
		ForumBoard::create([
			'name' => $forumBoard['name'],
			'description' => $forumBoard['description'],
			'ordering' => $i++,
			'closed' => $forumBoard['closed'],
		]);
	}
}

if (NewsCategory::count() === 0) {
	$newsCategoriesIcons = [
		0, 1, 2, 3, 4
	];

	foreach ($newsCategoriesIcons as $iconId) {
		NewsCategory::create([
			'icon_id' => $iconId,
		]);
	}
}

if (Gallery::count() === 0) {
	Gallery::create([
		'comment' => 'Demon',
		'image' => 'images/gallery/demon.jpg',
		'thumb' => 'images/gallery/demon_thumb.gif',
		'author' => 'MyAAC',
		'ordering' => 0,
	]);
}

success($locale['step_database_success_import_data']);
