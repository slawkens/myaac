<?php
/**
 * Gallery
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Cache\Cache;
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Gallery';

const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

$images = Cache::remember('gallery', 5 * 60, function () {
	$images = glob(BASE . GALLERY_DIR . '*.*');

	$images = array_filter($images, function ($image) {
		$ext = pathinfo($image, PATHINFO_EXTENSION);

		return (in_array($ext, ALLOWED_EXTENSIONS) && !str_contains($image, '_thumb'));
	});

	return array_map(function ($image) {
		return basename($image);
	}, $images);
});

$twig->display('gallery.html.twig', [
	'images' => $images,
]);
