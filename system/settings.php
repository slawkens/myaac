<?php

/**
 * Possible types:
 * - boolean - true/false
 * - text - string
 * - options - comma separated list of options
 */
return [
	'name' => 'MyAAC',
	'settings' =>
	[
		[
			'type' => 'category',
			'title' => 'General'
		],
		[
			'type' => 'section',
			'title' => 'General'
		],
		'date_timezone' => [
			'name' => 'Date Timezone',
			'type' => 'options',
			'options' => '$timezones',
			'desc' => 'Timezone of the server, more info at http://php.net/manual/en/timezones.php',
			'default' => 'Europe/Warsaw',
		],
		[
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
		[
			'type' => 'section',
			'title' => escapeHtml('<meta>') . ' - Header'
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
		[
			'type' => 'section',
			'title' => 'Footer'
		],
		'footer' => [
			'name' => 'Custom Text',
			'type' => 'textarea',
			'desc' => 'Text displayed in the footer.<br/>For example: <i>' . escapeHtml('<br/>') . 'Your Server &copy; 2023. All rights reserved.</i>',
			'default' => '',
		],
		'footer_load_time' => [
			'name' => 'Load Time',
			'type' => 'boolean',
			'desc' => 'Display load time of the page in the footer',
			'default' => true,
		],
		// do we really want this? I'm leaving it for consideration
		/*
		'footer_powered_by' => [
			'name' => 'Display Powered by MyAAC',
			'type' => 'boolean',
			'desc' => 'Do you want to show <i>Powered by MyAAC</i> slogan in the footer?',
			'default' => true,
		],
		*/
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
			'type' => 'section',
			'title' => 'Counters'
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
			'default' => 10,
			'show_if' => [
				'visitors_counter', '=', 'true'
			]
		],
		'views_counter' => [
			'name' => 'Views Counter',
			'type' => 'boolean',
			'desc' => 'Enable Views Counter? It will show how many times the website has been viewed by users',
			'default' => true,
		],
		[
			'type' => 'section',
			'title' => 'Misc'
		],
		'vocations_amount' => [
			'name' => 'Vocations Amount',
			'type' => 'number',
			'desc' => 'How much basic vocations your server got (without promotion)',
			'default' => 4,
		],
		'vocations' => [
			'name' => 'Vocation Names',
			'type' => 'textarea',
			'desc' => 'Separated by comma ,',
			'default' => 'None, Sorcerer, Druid, Paladin, Knight, Master Sorcerer, Elder Druid,Royal Paladin, Elite Knight',
			'callbacks' => [
				'get' => function ($value) {
					return array_map('trim', explode(',', $value));
				},
			],
		],
		'client' => [
			'name' => 'Client Version',
			'type' => 'options',
			'options' => '$clients',
			'desc' => 'what client version are you using on this OT?<br/>used for the Downloads page and some templates aswell',
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
			'desc' => 'It makes links looks more elegant to eye, and also are SEO friendly<br/><br/>
				yes: http://example.net/guilds/Testing<br/>
				no: http://example.net/?subtopic=guilds&name=Testing<br/><br/>
				<strong>apache2:</strong> mod_rewrite is required for this + remember to rename .htaccess.dist to .htaccess<br/>
				<strong>nginx:</strong> check included nginx-sample.conf',
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
		'anonymous_usage_statistics' => [
			'name' => 'Anonymous Usage Statistics',
			'type' => 'boolean',
			'desc' => 'Allow MyAAC to report anonymous usage statistics to developers? The data is sent only once per 30 days and is fully confidential. It won\'t affect the performance of your website',
			'default' => true,
		],
		[
			'type' => 'category',
			'title' => 'Mailing',
		],
		[
			'type' => 'section',
			'title' => 'Mailing'
		],
		'mail_enabled' => [
			'name' => 'Mailing enabled',
			'type' => 'boolean',
			'desc' => 'Is AAC configured to send e-mails?',
			'default' => false,
			/*'script' => <<<'SCRIPT'
<script>
$(function () {
	$('#mail_enabled_yes, #mail_enabled_no').on('change', function() {

		let elements = ['mail_address', 'mail_signature_plain', 'mail_signature_html', 'mail_option',
		'smtp_host', 'smtp_port', 'smtp_auth', 'smtp_user', 'smtp_pass', 'smtp_security', 'smtp_debug'];
		let show = ($(this).val() === 'true');
		let heading = $("h3:contains('SMTP (Mail Server)'):last");

		elements.forEach(function(el) {
			if (show) {
				$('#row_' + el).show(500);
				heading.show(500);
			}
			else {
				$('#row_' + el).hide(500);
				heading.hide(500);
			}
		});
	});
});
</script>
SCRIPT,*/
		],
		'mail_address' => [
			'name' => 'Mail Address',
			'type' => 'email',
			'desc' => 'Server e-mail address (from:)',
			'default' => 'no-reply@your-server.org',
			'show_if' => [
				'mail_enabled', '=', 'true'
			],
		],
		/*'mail_admin' => [
			'name' => 'Mail Admin Address',
			'type' => 'email',
			'desc' => 'Admin email address, where mails from contact form will be sent',
			'default' => 'your-address@your-server.org',
		],*/
		'mail_signature_plain' => [
			'name' => 'Mail Signature (Plain)',
			'type' => 'textarea',
			'desc' => 'Signature that will be included at the end of every message sent.<br/><b>In Normal Format!</b>',
			'default' => '--
Sent by MyAAC,
https://my-aac.org',
			'show_if' => [
				'mail_enabled', '=', 'true'
			]
		],
		'mail_signature_html' => [
			'name' => 'Mail Signature (HTML)',
			'type' => 'textarea',
			'desc' => 'Signature that will be included at the end of every message sent.<br/><b>In HTML Format!</b>',
			'default' => escapeHtml('<br/>
Sent by MyAAC,<br/>
<a href="https://my-aac.org">my-aac.org</a>'),
			'show_if' => [
				'mail_enabled', '=', 'true'
			]
		],
		'section_smtp' => [
			'type' => 'section',
			'title' => 'SMTP (Mail Server)',
			'show_if' => [
				'mail_enabled', '=', 'true'
			]
		],
		'mail_option' => [
			'name' => 'Mail Option',
			'type' => 'options',
			'options' => [0 => 'Mail (PHP Built-in)', 1 => 'SMTP (Gmail or Microsoft Outlook)'],
			'desc' => 'Mail sender. Set to SMTP if using Gmail or Microsoft Outlook, or any other provider',
			'default' => 0,
			'show_if' => [
				'mail_enabled', '=', 'true'
			]
		],
		'smtp_host' => [
			'name' => 'SMTP Host',
			'type' => 'text',
			'desc' => 'SMTP mail host. smtp.gmail.com for GMail / smtp-mail.outlook.com for Microsoft Outlook',
			'default' => '',
			'show_if' => [
				'mail_enabled', '=', 'true'
			]
		],
		'smtp_port' => [
			'name' => 'SMTP Host',
			'type' => 'number',
			'desc' => '25 (default) / 465 (ssl, GMail) / 587 (tls, Microsoft Outlook)',
			'default' => 25,
			'show_if' => [
				'mail_enabled', '=', 'true'
			]
		],
		'smtp_auth' => [
			'name' => 'SMTP Auth',
			'type' => 'boolean',
			'desc' => 'Need authorization for Server? In normal situation, almost always Yes.',
			'default' => true,
			'show_if' => [
				'mail_enabled', '=', 'true'
			]
		],
		'smtp_user' => [
			'name' => 'SMTP Username',
			'type' => 'text',
			'desc' => 'Here your email username to authenticate with SMTP',
			'default' => 'admin@example.org',
			'show_if' => [
				'mail_enabled', '=', 'true'
			]
		],
		'smtp_pass' => [
			'name' => 'SMTP Password',
			'type' => 'password',
			'desc' => 'Here your email password to authenticate with SMTP',
			'default' => '',
			'show_if' => [
				'mail_enabled', '=', 'true'
			]
		],
		'smtp_security' => [
			'name' => 'SMTP Security',
			'type' => 'options',
			'options' => ['None', 'SSL', 'TLS'],
			'desc' => 'What kind of encryption to use on the SMTP connection',
			'default' => 0,
			'show_if' => [
				'mail_enabled', '=', 'true'
			]
		],
		'smtp_debug' => [
			'name' => 'SMTP Debug',
			'type' => 'boolean',
			'desc' => 'Activate to see more logs about mailing errors in error.log',
			'default' => false,
			'show_if' => [
				'mail_enabled', '=', 'true'
			]
		],
		[
			'type' => 'category',
			'title' => 'Accounts',
		],
		[
			'type' => 'section',
			'title' => 'Accounts Settings'
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
		'account_mail_confirmed_reward' => [
			'type' => 'section',
			'title' => 'Reward Users for confirming their E-Mails. Works only with Account Mail Verify enabled',
			'show_if' => [
				'account_mail_verify', '=', 'true'
			],
		],
		'account_mail_confirmed_reward_premium_days' => [
			'name' => 'Reward Premium Points',
			'type' => 'number',
			'desc' => '0 to disable',
			'default' => 0,
			'show_if' => [
				'account_mail_verify', '=', 'true'
			],
		],
		'account_mail_confirmed_reward_premium_points' => [
			'name' => 'Reward Premium Points',
			'type' => 'number',
			'desc' => '0 to disable',
			'default' => 0,
			'show_if' => [
				'account_mail_verify', '=', 'true'
			],
		],
		'account_mail_confirmed_reward_coins' => [
			'name' => 'Reward Premium Points',
			'type' => 'number',
			'desc' => '0 to disable. Works only with servers that supports coins',
			'default' => 0,
			'show_if' => [
				'account_mail_verify', '=', 'true'
			],
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
			'show_if' => [
				'guild_management', '=', 'true',
			],
		],
		'guild_need_premium' => [
			'name' => 'Guild Need Premium',
			'type' => 'boolean',
			'desc' => 'Require premium account to form a guild?',
			'default' => true,
			'show_if' => [
				'guild_management', '=', 'true',
			],
		],
		'guild_image_size_kb' => [
			'name' => 'Guild Image Size',
			'type' => 'number',
			'desc' => 'Maximum size of the guild logo image in KB (kilobytes)',
			'default' => 80,
			'show_if' => [
				'guild_management', '=', 'true',
			],
		],
		'guild_description_default' => [
			'name' => 'Default Guild Description',
			'type' => 'text',
			'desc' => 'Default description set on new guild',
			'default' => 'New guild. Leader must edit this text :)',
			'show_if' => [
				'guild_management', '=', 'true',
			],
		],
		'guild_description_chars_limit' => [
			'name' => 'Guild Description Characters Limit',
			'type' => 'number',
			'desc' => 'How many characters can be in guild description',
			'default' => 1000,
			'show_if' => [
				'guild_management', '=', 'true',
			],
		],
		'guild_description_lines_limit' => [
			'name' => 'Guild Description Lines Limit',
			'type' => 'number',
			'desc' => "Limit of lines, if description has more lines it will be showed as long text, without 'enters'",
			'default' => 6,
			'show_if' => [
				'guild_management', '=', 'true',
			],
		],
		'guild_motd_chars_limit' => [
			'name' => 'Guild MOTD Characters Limit',
			'type' => 'number',
			'desc' => 'Limit of MOTD (message of the day) that is shown later in the game on the guild channel',
			'default' => 150,
			'show_if' => [
				'guild_management', '=', 'true',
			],
		],
		[
			'type' => 'category',
			'title' => 'Pages',
		],
		[
			'type' => 'section',
			'title' => 'Forum'
		],
		'forum' => [
			'name' => 'Forum',
			'type' => 'text',
			'desc' => 'Do you want to use built-in forum feature? Enter <strong>"site"</strong> if you want to use built-in forum feature, if you want use custom forum - enter URL here, otherwise leave empty (to disable)',
			'default' => 'site',
		],
		'forum_level_required' => [
			'name' => 'Forum Level Required',
			'type' => 'number',
			'desc' => 'Level required to post on forum. 0 to disable',
			'min' => 0,
			'max' => 99999999999,
			'default' => 0,
			'show_if' => [
				'forum', '=', 'site',
			],
		],
		'forum_post_interval' => [
			'name' => 'Forum Post Interval',
			'type' => 'number',
			'desc' => 'How often user can post on forum, in seconds',
			'min' => 0,
			'max' => 99999999999,
			'default' => 30,
			'show_if' => [
				'forum', '=', 'site',
			],
		],
		'forum_posts_per_page' => [
			'name' => 'Forum Posts per Page',
			'type' => 'number',
			'desc' => 'How many posts per page',
			'min' => 0,
			'max' => 99999999999,
			'default' => 20,
			'show_if' => [
				'forum', '=', 'site',
			],
		],
		'forum_threads_per_page' => [
			'name' => 'Forum Threads per Page',
			'type' => 'number',
			'desc' => 'How many threads per page',
			'min' => 0,
			'max' => 99999999999,
			'default' => 20,
			'show_if' => [
				'forum', '=', 'site',
			],
		],
		'forum_table_prefix' => [
			'name' => 'Forum Table Prefix',
			'type' => 'text',
			'desc' => 'What forum mysql table to use, z_ (for gesior old forum) or myaac_ (for myaac)',
			'default' => 'myaac_',
			'show_if' => [
				'forum', '=', 'site',
			],
		],
		[
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
			'name' => 'Display Skull Images',
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
			'name' => 'Display AFK Players',
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
			'default' => 1,
		],
		'team_status' => [
			'name' => 'Display Online Status',
			'type' => 'boolean',
			'desc' => '',
			'default' => true,
		],
		'team_lastlogin' => [
			'name' => 'Display Last Login',
			'type' => 'boolean',
			'desc' => '',
			'default' => true,
		],
		'team_world' => [
			'name' => 'Display World',
			'type' => 'boolean',
			'desc' => '',
			'default' => false,
		],
		'team_outfit' => [
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
		[
			'type' => 'section',
			'title' => 'Experience Table Page'
		],
		'experience_table_columns' => [
			'name' => 'Columns',
			'type' => 'number',
			'desc' => 'How many columns to display in experience table page, * rows, 5 = 500 (will show up to 500 level)',
			'default' => 3,
		],
		'experience_table_rows' => [
			'name' => 'Rows',
			'type' => 'number',
			'desc' => 'Till how many levels in one column',
			'default' => 200,
		],
		[
			'type' => 'category',
			'title' => 'Images',
		],
		[
			'type' => 'section',
			'title' => 'Item Images'
		],
		'item_images_url' => [
			'name' => 'Item Images URL',
			'type' => 'text',
			'desc' => 'Set to <strong>images/items</strong> if you host your own items in images folder',
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
			'title' => 'Outfit Images'
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
			'callbacks' => [
				'get' => function ($value) {
					return array_map('trim', explode(',', $value));
				},
			],
		],
		[
			'type' => 'section',
			'title' => 'Monster Images'
		],
		'monsters_images_url' => [
			'name' => 'Monsters Images URL',
			'type' => 'text',
			'desc' => 'Set to <i>images/monsters/</i> if you host your own creatures in images folder',
			'default' => 'images/monsters/',
		],
		'monsters_images_extension' => [
			'name' => 'Monsters Images File Extension',
			'type' => 'text',
			'desc' => '',
			'default' => '.gif',
		],
		'monsters_images_preview' => [
			'name' => 'Monsters Images Preview',
			'type' => 'boolean',
			'desc' => 'Set to true to allow picture previews for creatures',
			'default' => false,
		],
		'monsters_items_url' => [
			'name' => 'Monsters Items URL',
			'type' => 'text',
			'desc' => 'Set to website which shows details about items',
			'default' => 'https://tibia.fandom.com/wiki/',
		],
		'monsters_loot_percentage' => [
			'name' => 'Monsters Items URL',
			'type' => 'boolean',
			'desc' => 'Set to true to show the loot tooltip percent',
			'default' => true,
		],
		// this is hidden, because no implemented yet
		'multiworld' => [
			'hidden' => true,
			'type' => 'boolean',
			'default' => false,
		],
		[
			'type' => 'category',
			'title' => 'Status',
		],
		[
			'type' => 'section',
			'title' => 'Server Status'
		],
		'status_enabled' => [
			'name' => 'Enable Server Status',
			'type' => 'boolean',
			'desc' => 'You can disable status checking here',
			'default' => true,
		],
		'status_ip' => [
			'name' => 'Status IP',
			'type' => 'text',
			'desc' => 'Leave empty to get automatically from config',
			'default' => '127.0.0.1',
			'show_if' => [
				'status_enabled', '=', 'true',
			]
		],
		'status_port' => [
			'name' => 'Status Port',
			'type' => 'number',
			'min' => 0,
			'desc' => 'Leave empty to get automatically from config',
			'default' => 7171,
			'show_if' => [
				'status_enabled', '=', 'true',
			]
		],
		'status_timeout' => [
			'name' => 'Status Timeout',
			'type' => 'number',
			'min' => 0,
			'max' => 10, // more than 10 seconds waiting makes no sense
			'step' => 0.1,
			'desc' => 'How long to wait for the initial response from the server',
			'default' => 2.0,
			'show_if' => [
				'status_enabled', '=', 'true',
			]
		],
		'status_interval' => [
			'name' => 'Status Interval',
			'type' => 'number',
			'min' => 0,
			'desc' => 'How often to connect to server and update status.<br/>If your status timeout in config.lua is bigger, that it will be used instead. When server is offline, it will be checked every time web refreshes, ignoring this variable',
			'default' => 60,
			'show_if' => [
				'status_enabled', '=', 'true',
			]
		],
	],
];
