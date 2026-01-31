<?php

if(PAGE !== 'news') {
	return;
}

$configGalleryImageThumb = config('gallery_image_thumb');
if (!empty($configGalleryImageThumb)) {
	$twig->display('gallery.html.twig', array(
		'image' => GALLERY_DIR . $configGalleryImageThumb,
	));
}
