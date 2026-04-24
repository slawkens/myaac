<?php

use MyAAC\Models\Gallery;

if(PAGE !== 'news' || !$db->hasTable(TABLE_PREFIX . 'gallery')) {
	return;
}

$gallery = Gallery::find($config['gallery_image_id_from_database']);
if ($gallery) {
	$twig->display('gallery.html.twig', array(
		'image' => $gallery->toArray()
	));
}
