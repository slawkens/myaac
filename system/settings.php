<?php

return [
	'category_1' => [
		'type' => 'category',
		'title' => 'General'
	],
	'section_1' => [
		'type' => 'section',
		'title' => 'Template'
	],
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
	'section_2' => [
		'type' => 'section',
		'title' => 'Misc'
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
	'section_3' => [
		'type' => 'section',
		'title' => 'Meta Site Settings'
	],
	'charset' => [
		'name' => 'Meta Charset',
		'type' => 'text',
		'desc' => 'Charset used in ' . escapeHtml('<meta>'),
		'default' => 'utf-8',
	],
	'meta_description' => [
		'name' => 'Meta Description',
		'type' => 'textarea',
		'desc' => 'description of the site in ' . escapeHtml('<meta>'),
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
		'desc' => 'For example: "' . escapeHtml('<br/>') . 'Your Server &copy; 2020. All rights reserved."',
		'default' => '',
	],
	/*'language' => [
		'name' => 'Language',
		'type' => 'options',
		'options' => ['en' => 'English'],
		'desc' => 'default language (currently only English available)',
		'default' => 'en',
	],*/
	/*'language_allow_change' => [
		'name' => 'Language Allow Change',
		'type' => 'boolean',
		'default' => false,
		'desc' => 'default language (currently only English available)'
	],*/
	[
		'type' => 'category',
		'title' => 'Counters',
	],
	[
		'type' => 'section',
		'title' => 'Visitors Counter & Views Counter'
	],
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
	[
		'type' => 'category',
		'title' => 'Account',
	],
	[
		'type' => 'section',
		'title' => 'Account Settings'
	],
	'account_management' => [
		'name' => 'Enable Account Management',
		'type' => 'boolean',
		'desc' => "disable if you're using other method to manage users (fe. tfs account manager)",
		'default' => true,
	],
	'account_login_by_email' => [
		'name' => 'Account Login By E-Mail',
		'type' => 'boolean',
		'desc' => "use email instead of Account Name like in latest Tibia",
		'default' => true,
	],
	'account_login_by_email_fallback' => [
		'name' => 'Account Login By E-Mail Fallback',
		'type' => 'boolean',
		'desc' => "allow also additionally login by Account Name/Number (for users that might forget their email). Works only if Account Login By E-Mail is also enabled",
		'default' => false,
	],
	'account_create_auto_login' => [
		'name' => 'Account Create Auto Login',
		'type' => 'boolean',
		'desc' => 'Auto login after creating account?',
		'default' => false,
	],
	'account_create_character_create' => [
		'name' => 'Account Create Character Create',
		'type' => 'boolean',
		'desc' => 'Allow to create character directly on create account page?',
		'default' => true,
	],
	'account_mail_verify' => [
		'name' => 'Account Mail Verify',
		'type' => 'boolean',
		'desc' => 'Force users to confirm their email addresses when registering account',
		'default' => false,
	],
	'account_mail_unique' => [
		'name' => 'Account Mail Unique',
		'type' => 'boolean',
		'desc' => 'Email addresses cannot be duplicated? (one account = one email)',
		'default' => true,
	],
	'account_premium_days' => [
		'name' => 'Default Account Premium Days',
		'type' => 'number',
		'desc' => 'Default premium days on new account',
		'default' => 0,
	],
	'account_premium_points' => [
		'name' => 'Default Account Premium Points',
		'type' => 'number',
		'desc' => 'Default premium points on new account',
		'default' => 0,
	],
	'account_welcome_mail' => [
		'name' => 'Account Welcome Mail',
		'type' => 'boolean',
		'desc' => 'Send welcome email when user registers',
		'default' => true,
	],
	'account_mail_change' => [
		'name' => 'Account Mail Change Days',
		'type' => 'number',
		'desc' => 'How many days user need to change email to account - block hackers',
		'default' => 2,
	],
	'account_country' => [
		'name' => 'Account Country',
		'type' => 'boolean',
		'desc' => 'User will be able to set country of origin when registering account, this information will be viewable in others places as well',
		'default' => true,
	],
	'account_country_recognize' => [
		'name' => 'Auto Recognize Account Country',
		'type' => 'boolean',
		'desc' => 'should country of user be automatically recognized by his IP? This makes an external API call to http://ipinfo.io',
		'default' => true,
	],
	[
		'type' => 'section',
		'title' => 'Reward Users for confirming their E-Mails. Works only with Account Mail Verify enabled'
	],
	'account_mail_confirmed_reward_premium_days' => [
		'name' => 'Reward Premium Points',
		'type' => 'number',
		'desc' => '0 to disable',
		'default' => 0,
	],
	'account_mail_confirmed_reward_premium_points' => [
		'name' => 'Reward Premium Points',
		'type' => 'number',
		'desc' => '0 to disable',
		'default' => 0,
	],
	'account_mail_confirmed_reward_coins' => [
		'name' => 'Reward Premium Points',
		'type' => 'number',
		'desc' => '0 to disable. Works only with servers that supports coins',
		'default' => 0,
	],
	[
		'type' => 'category',
		'title' => 'Images',
	],
	[
		'type' => 'section',
		'title' => 'Item and Outfit Images'
	],
	'outfit_images_url' => [
		'name' => 'Outfit Images URL',
		'type' => 'text',
		'desc' => 'Set to animoutfit.php for animated outfit',
		'default' => 'http://outfit-images.ots.me/outfit.php',
	],
	'outfit_images_wrong_looktypes' => [
		'name' => 'Outfit Images Wrong Looktypes',
		'type' => 'text',
		'desc' => 'This looktypes needs to have different margin-top and margin-left because they are wrong positioned',
		'default' => '75, 126, 127, 266, 302',
	],
	'item_images_url' => [
		'name' => 'Item Images URL',
		'type' => 'text',
		'desc' => 'Set to images/items if you host your own items in images folder',
		'default' => 'http://item-images.ots.me/1092/',
	],
	'item_images_extension' => [
		'name' => 'Item Images File Extension',
		'type' => 'text',
		'desc' => '',
		'default' => '.gif',
	],
	[
		'type' => 'section',
		'title' => 'Monsters images'
	],
	'creatures_images_url' => [
		'name' => 'Creatures images URL',
		'type' => 'text',
		'desc' => 'Set to images/monsters if you host your own creatures in images folder',
		'default' => 'images/monsters/',
	],
	'creatures_images_extension' => [
		'name' => 'Creatures Images File Extension',
		'type' => 'text',
		'desc' => '',
		'default' => '.gif',
	],
	'creatures_images_preview' => [
		'name' => 'Item Images URL',
		'type' => 'boolean',
		'desc' => 'Set to true to allow picture previews for creatures',
		'default' => false,
	],
	'creatures_items_url' => [
		'name' => 'Creatures Items URL',
		'type' => 'text',
		'desc' => 'Set to website which shows details about items',
		'default' => 'https://tibia.fandom.com/wiki/',
	],
	'creatures_loot_percentage' => [
		'name' => 'Creatures Items URL',
		'type' => 'boolean',
		'desc' => 'Set to true to show the loot tooltip percent',
		'default' => true,
	],
	[
		'type' => 'category',
		'title' => 'Guilds',
	],
	[
		'type' => 'section',
		'title' => 'Guilds'
	],
	'guild_management' => [
		'name' => 'Enable Guilds Management',
		'type' => 'boolean',
		'desc' => 'Enable guild management system on the site',
		'default' => true,
	],
	'guild_need_level' => [
		'name' => 'Guild Need Level',
		'type' => 'number',
		'desc' => 'Min. level to form a guild',
		'default' => 1,
	],
	'guild_need_premium' => [
		'name' => 'Guild Need Premium',
		'type' => 'boolean',
		'desc' => 'Require premium account to form a guild?',
		'default' => true,
	],
	'guild_image_size_kb' => [
		'name' => 'Guild Image Size',
		'type' => 'number',
		'desc' => 'Maximum size of the guild logo image in KB (kilobytes)',
		'default' => 80,
	],
	'guild_description_default' => [
		'name' => 'Default Guild Description',
		'type' => 'text',
		'desc' => 'Default description set on new guild',
		'default' => 'New guild. Leader must edit this text :)',
	],
	'guild_description_chars_limit' => [
		'name' => 'Guild Description Characters Limit',
		'type' => 'boolean',
		'desc' => 'How many characters can be in guild description',
		'default' => 1000,
	],
	'guild_description_lines_limit' => [
		'name' => 'Guild Description Lines Limit',
		'type' => 'number',
		'desc' => "Limit of lines, if description has more lines it will be showed as long text, without 'enters'",
		'default' => 6,
	],
	'guild_motd_chars_limit' => [
		'name' => 'Guild MOTD Characters Limit',
		'type' => 'boolean',
		'desc' => 'Limit of MOTD (message of the day) that is shown later in the game on the guild channel',
		'default' => 150,
	],
	[
		'type' => 'category',
		'title' => 'Pages',
	],
	'section_10' => [
		'type' => 'section',
		'title' => 'Online Page'
	],
	'online_record' => [
		'name' => 'Display Players Record',
		'type' => 'boolean',
		'desc' => '',
		'default' => true,
	],
	'online_vocations' => [
		'name' => 'Display Vocation Statistics',
		'type' => 'boolean',
		'desc' => '',
		'default' => false,
	],
	'online_vocations_images' => [
		'name' => 'Display Vocation Images',
		'type' => 'boolean',
		'desc' => 'Only if Display Vocation Statistics enabled',
		'default' => true,
	],
	'online_skulls' => [
		'name' => 'Display skull images',
		'type' => 'boolean',
		'desc' => '',
		'default' => true,
	],
	'online_outfit' => [
		'name' => 'Display Player Outfit',
		'type' => 'boolean',
		'desc' => '',
		'default' => true,
	],
	'online_afk' => [
		'name' => 'Display Players AFK',
		'type' => 'boolean',
		'desc' => '',
		'default' => false,
	],
	[
		'type' => 'section',
		'title' => 'Team Page'
	],
	'team_style' => [
		'name' => 'Style',
		'type' => 'options',
		'desc' => '',
		'options' => ['normal table', 'in boxes, grouped by group id'],
		'default' => 2,
	],
	'team_display_status' => [
		'name' => 'Display Online Status',
		'type' => 'boolean',
		'desc' => '',
		'default' => true,
	],
	'team_display_lastlogin' => [
		'name' => 'Display Last Login',
		'type' => 'boolean',
		'desc' => '',
		'default' => true,
	],
	'team_display_world' => [
		'name' => 'Display World',
		'type' => 'boolean',
		'desc' => '',
		'default' => false,
	],
	'team_display_outfit' => [
		'name' => 'Display Outfit',
		'type' => 'boolean',
		'desc' => '',
		'default' => true,
	],
	[
		'type' => 'section',
		'title' => 'Bans Page'
	],
	'bans_per_page' => [
		'name' => 'Display Players Record',
		'type' => 'boolean',
		'desc' => '',
		'default' => true,
	],
];
