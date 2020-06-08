<?php

return [
	'template' => [
		'name' => 'Template Name',
		'type' => 'options',
		'options' => '$templates',
		'desc' => 'Name of the template used by website',
		'default' => 'kathrine',
	],
	'template_allow_change' => [
		'name' => 'Template Allow Change',
		'type' => 'boolean',
		'desc' => 'Allow changing template of the website by showing a special select in the part of website',
		'default' => true,
	],
	'vocations_amount' => [
		'name' => 'Amount of Vocations',
		'type' => 'number',
		'desc' => 'how much basic vocations your server got (without promotion)',
		'default' => 4,
	],
	'client' => [
		'name' => 'Client Version',
		'type' => 'options',
		'options' => '$clients',
		'desc' => 'what client version are you using on this OT?<br/>
used for the Downloads page and some templates aswell',
		'default' => 710
	],
	'session_prefix' => [
		'name' => 'Session Prefix',
		'type' => 'text',
		'desc' => 'must be unique for every site on your server',
		'default' => 'myaac_',
	],
	'friendly_urls' => [
		'name' => 'Friendly URLs',
		'type' => 'boolean',
		'desc' => 'mod_rewrite is required for this, it makes links looks more elegant to eye, and also are SEO friendly (example: https://my-aac.org/guilds/Testing instead of https://my-aac.org/?subtopic=guilds&name=Testing).<br/><strong>Remember to rename .htaccess.dist to .htaccess</strong>',
		'default' => false,
	],
	'backward_support' => [
		'name' => 'Gesior Backward Support',
		'type' => 'boolean',
		'desc' => 'gesior backward support (templates & pages)<br/>
allows using gesior templates and pages with myaac<br/>
might bring some performance when disabled',
		'default' => true,
	],
	'charset' => [
		'name' => 'Meta Charset',
		'type' => 'text',
		'desc' => 'Charset used in <meta>',
		'default' => 'utf-8',
	],
	'meta_description' => [
		'name' => 'Meta Description',
		'type' => 'textarea',
		'desc' => 'description of the site in <meta>',
		'default' => 'Tibia is a free massive multiplayer online role playing game (MMORPG).',
	],
	'meta_keywords' => [
		'name' => 'Meta Keywords',
		'type' => 'textarea',
		'desc' => 'keywords list separated by commas',
		'default' => 'free online game, free multiplayer game, ots, open tibia server',
	],
	'footer' => [
		'name' => 'Footer',
		'type' => 'textarea',
		'desc' => 'For example: "' . htmlspecialchars('<br/>') . 'Your Server &copy; 2020. All rights reserved."',
		'default' => '',
	],
	'language' => [
		'name' => 'Language',
		'type' => 'options',
		'options' => ['en' => 'English'],
		'desc' => 'default language (currently only English available)',
		'default' => 'en',
	],
	/*'language_allow_change' => [
		'name' => 'Language Allow Change',
		'type' => 'boolean',
		'default' => false,
		'desc' => 'default language (currently only English available)'
	],*/
	'visitors_counter' => [
		'name' => 'Visitors Counter',
		'type' => 'boolean',
		'desc' => 'Enable Visitors Counter? It will show list of online members on the website in Admin Panel',
		'default' => true,
	],
	'visitors_counter_ttl' => [
		'name' => 'Visitors Counter TTL',
		'type' => 'number',
		'desc' => 'Time To Live for Visitors Counter. In other words - how long user will be marked as online. In Minutes',
		'default' => 10
	],
	'views_counter' => [
		'name' => 'Views Counter',
		'type' => 'boolean',
		'desc' => 'Enable Views Counter? It will show how many times the website has been viewed by users',
		'default' => true,
	],
	'outfit_images_url' => [
		'name' => 'Outfit Images URL',
		'type' => 'text',
		'desc' => 'Set to animoutfit.php for animated outfit',
		'default' => 'http://outfit-images.ots.me/outfit.php',
	],
	'item_images_url' => [
		'name' => 'Item Images URL',
		'type' => 'text',
		'desc' => 'Set to images/items if you host your own items in images folder',
		'default' => 'http://item-images.ots.me/1092/',
	],
];