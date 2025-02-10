<?php
/**
 * @var OTS_DB_MySQL $db
 */

$up = function () use ($db) {
	// rename database tables
	$db->renameTable(TABLE_PREFIX . 'screenshots', TABLE_PREFIX . 'gallery');
	$db->renameTable(TABLE_PREFIX . 'movies', TABLE_PREFIX . 'videos');

	// rename images dir
	if (file_exists(BASE . 'images/screenshots') && !file_exists(BASE . GALLERY_DIR)) {
		rename(BASE . 'images/screenshots', BASE . GALLERY_DIR);
	}

	// convert old database screenshots images to gallery
	$query = $db->query('SELECT `id`, `image`, `thumb` FROM `' . TABLE_PREFIX . 'gallery`;');
	foreach ($query->fetchAll() as $item) {
		$db->update(TABLE_PREFIX . 'gallery', array(
			'image' => str_replace('/screenshots/', '/gallery/', $item['image']),
			'thumb' => str_replace('/screenshots/', '/gallery/', $item['thumb']),
		), array('id' => $item['id']));
	}
};

$down = function () use ($db) {
	// rename database tables
	$db->renameTable(TABLE_PREFIX . 'gallery', TABLE_PREFIX . 'screenshots');
	$db->renameTable(TABLE_PREFIX . 'videos', TABLE_PREFIX . 'movies');

	// rename images dir
	if (file_exists(BASE . GALLERY_DIR) && !file_exists(BASE . 'images/screenshots')) {
		rename(BASE . GALLERY_DIR, BASE . 'images/screenshots');
	}

	// convert new database gallery images to screenshots
	$query = $db->query('SELECT `id`, `image`, `thumb` FROM `' . TABLE_PREFIX . 'screenshots`;');
	foreach ($query->fetchAll() as $item) {
		$db->update(TABLE_PREFIX . 'screenshots', [
			'image' => str_replace('/gallery/', '/screenshots/', $item['image']),
			'thumb' => str_replace('/gallery/', '/screenshots/', $item['thumb']),
		], ['id' => $item['id']]);
	}
};
