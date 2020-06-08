<?php

$deprecatedConfig = [
	'template',
	'template_allow_change',
	'vocations_amount',
	'client',
	'session_prefix',
	'friendly_urls',
	'backward_support',
	'charset',
	'meta_description',
	'meta_keywords',
	'footer',
	'language',
	'visitors_counter',
	'visitors_counter_ttl',
	'views_counter',
	'outfit_images_url',
	'item_images_url',
];

foreach ($deprecatedConfig as $value) {
	$config[$value] = $settings['core.'.$value]['value'];
}