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
	//'language',
	'visitors_counter',
	'visitors_counter_ttl',
	'views_counter',
	'outfit_images_url',
	'outfit_images_wrong_looktypes',
	'item_images_url',
	'account_country',
	'online_record',
	'online_vocations',
	'online_vocations_images',
	'online_skulls',
	'online_outfit',
	'online_afk',
	'team_display_outfit' => 'team_outfit',
	'team_display_status' => 'team_status',
	'team_display_world' => 'team_world',
	'team_display_lastlogin' => 'team_lastlogin',
	'multiworld',
];

foreach ($deprecatedConfig as $key => $value) {
	config(
		[
			(is_string($key) ? $key : $value),
			setting('core.'.$value)
		]
	);

	//var_dump($settings['core.'.$value]['value']);
}

$vocationsParsed = array_map(
	function(string $value): string {
		return trim($value);
	},
	explode(',', setting('core.vocations'))
);

config(['vocations', $vocationsParsed]);
