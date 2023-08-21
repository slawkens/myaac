<?php

$deprecatedConfig = [
	'date_timezone',
	'genders',
	'template',
	'template_allow_change',
	'vocations_amount',
	'vocations',
	'client',
	'session_prefix',
	'friendly_urls',
	'backward_support',
	'charset',
	'meta_description',
	'meta_keywords',
	'footer',
	'database_encryption' => 'database_hash',
	//'language',
	'visitors_counter',
	'visitors_counter_ttl',
	'views_counter',
	'outfit_images_url',
	'outfit_images_wrong_looktypes',
	'item_images_url',
	'account_country',
	'towns',
	'quests',
	'character_samples',
	'character_towns',
	'characters_per_account',
	'characters_search_limit',
	'news_author',
	'news_limit',
	'news_ticker_limit',
	'news_date_format',
	'guild_management',
	'guild_need_level',
	'guild_need_premium',
	'guild_image_size_kb',
	'guild_description_default',
	'guild_description_chars_limit',
	'guild_motd_chars_limit',
	'highscores_groups_hidden',
	'highscores_ids_hidden',
	'highscores_vocation_box',
	'highscores_vocation',
	'highscores_outfit',
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
	'last_kills_limit',
	'multiworld',
	'forum',
	'signature_enabled',
	'signature_type',
	'signature_cache_time',
	'signature_browser_cache',
	'gifts_system',
	'status_enabled',
	'status_ip',
	'status_port',
	'mail_enabled',
	'account_login_by_email',
	'account_login_by_email_fallback',
	'account_mail_verify',
	'account_mail_unique',
	'account_premium_days',
	'account_premium_points',
	'account_create_character_create',
	'account_change_character_name',
	'account_change_character_name_points' => 'account_change_character_name_price',
	'account_change_character_sex',
	'account_change_character_sex_points' => 'account_change_character_name_price',
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

$deprecatedConfigCharacters = [
	'level',
	'experience',
	'magic_level',
	'balance',
	'marriage_info' => 'marriage',
	'outfit',
	'creation_date',
	'quests',
	'skills',
	'equipment',
	'frags',
	'deleted',
];

$tmp = [];
foreach ($deprecatedConfigCharacters as $key => $value) {
	$tmp[(is_string($key) ? $key : $value)] = setting('core.characters_'.$value);
}

config(['characters', $tmp]);
unset($tmp);
