<?php

/**
 * Possible types:
 * - boolean - true/false
 * - text/number/email/password
 * - textarea - longer string
 * - options - array of options
 *
 * Additional options
 *  - for number: min, max, step
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
		'env' => [
			'name' => 'App Environment',
			'type' => 'options',
			'options' => ['prod' => 'Production', 'dev' => 'Development'],
			'desc' => 'if you use this script on your live server - set production<br/>' .
						'* if you want to test and debug the script locally, or develop plugins, set to development<br/>' .
						'* WARNING: on "development" cache is disabled, so site will be significantly slower !!!<br/>' .
						'* WARNING2: on "development" all PHP errors/warnings are displayed<br/>' .
						'* Recommended: "production" cause of speed (page load time is better)',
			'default' => 'prod',
			'is_config' => true,
		],
		'server_path' => [
			'name' => 'Server Path',
			'type' => 'text',
			'desc' => 'Path to the server directory (same directory where config file is located)',
			'default' => '',
			'is_config' => true,
		],
		'date_timezone' => [
			'name' => 'Date Timezone',
			'type' => 'options',
			'options' => '$timezones',
			'desc' => 'Timezone of the server, more info at http://php.net/manual/en/timezones.php',
			'default' => 'Europe/Warsaw',
		],
		'friendly_urls' => [
			'name' => 'Friendly URLs',
			'type' => 'boolean',
			'desc' => 'It makes links looks more elegant to eye, and also are SEO friendly<br/><br/>' .
				'yes: http://example.net/guilds/Testing<br/>' .
				'no: http://example.net/index.php/guilds/Testing<br/><br/>' .
				'<strong>apache2:</strong> mod_rewrite is required for this + remember to rename .htaccess.dist to .htaccess<br/>' .
				'<strong>nginx:</strong> check included nginx-sample.conf',
			'default' => false,
		],
		'gzip_output' => [
			'name' => 'gzip Output',
			'type' => 'boolean',
			'desc' => 'gzip page content before sending it to the browser, uses less bandwidth but more cpu cycles',
			'default' => false,
			'is_config' => true,
		],
		'csrf_protection' => [
			'name' => 'CSRF protection',
			'type' => 'boolean',
			'desc' => 'Its recommended to keep it enabled. Disable only if you know what you are doing.',
			'default' => true,
		],
		'google_analytics_id' => [
			'name' => 'Google Analytics ID',
			'type' => 'text',
			'desc' => 'Format: UA-XXXXXXX-X',
			'default' => '',
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
		'cache_engine' => [
			'name' => 'Cache Engine',
			'type' => 'options',
			'options' => ['auto' => 'Auto', 'file' => 'Files', 'apc' => 'APC', 'apcu' => 'APCu', 'eaccelerator' => 'eAccelerator', 'disable' => 'Disable'],
			'desc' => 'Auto is most reasonable. It will detect the best cache engine',
			'default' => 'auto',
			'is_config' => true,
		],
		'cache_prefix' => [
			'name' => 'Cache Prefix',
			'type' => 'text',
			'desc' => 'Have to be unique if running more MyAAC instances on the same server (except file system cache)',
			'default' => 'myaac_' . generateRandomString(8, true, false, true),
			'is_config' => true,
		],
		'session_prefix' => [
			'name' => 'Session Prefix',
			'type' => 'text',
			'desc' => 'must be unique for every site on your server',
			'default' => 'myaac_',
		],
		'backward_support' => [
			'name' => 'Gesior Backward Support',
			'type' => 'boolean',
			'desc' => 'gesior backward support (templates & pages)<br/>' .
						'allows using gesior templates and pages with myaac<br/>' .
						'might bring some performance when disabled',
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
			'title' => 'Game',
		],
		[
			'type' => 'section',
			'title' => 'Game'
		],
		'client' => [
			'name' => 'Client Version',
			'type' => 'options',
			'options' => '$clients',
			'desc' => 'what client version are you using on this OT?<br/>used for the Downloads page and some templates aswell',
			'default' => 710
		],
		'towns' => [
			'name' => 'Towns',
			'type' => 'textarea',
			'desc' => "if you use TFS 1.3 with support for 'towns' table in database, then you can ignore this - it will be configured automatically (from MySQL database - Table - towns)<br/>" .
				"otherwise it will try to load from your .OTBM map file<br/>" .
				"if you don't see towns on website, then you need to fill this out",
			'default' => "0=No Town\n1=Sample Town",
			'callbacks' => [
				'get' => function ($value) {
					$ret = [];
					$towns = array_map('trim', preg_split('/\r\n|\r|\n/', trim($value)));

					foreach ($towns as $town) {
						if (empty($town)) {
							continue;
						}

						$explode = explode('=', $town);
						$ret[$explode[0]] = $explode[1];
					}

					return $ret;
				},
			],
		],
		'genders' => [
			'name' => 'Genders (aka sex)',
			'type' => 'textarea',
			'desc' => 'Separated with comma',
			'default' => 'Female, Male',
			'callbacks' => [
				'get' => function ($value) {
					return array_map('trim', explode(',', $value));
				},
			],
		],
		'account_types' => [
			'name' => 'Account Types',
			'type' => 'textarea',
			'desc' => 'Separated with comma, you may need to adjust this for older tfs versions by removing Community Manager',
			'default' => 'None, Normal, Tutor, Senior Tutor, Gamemaster, Community Manager, God',
			'callbacks' => [
				'get' => function ($value) {
					return array_map('trim', explode(',', $value));
				},
			],
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
		[
			'type' => 'category',
			'title' => 'Database',
		],
		[
			'type' => 'section',
			'title' => 'Database',
		],
		'database_overwrite' => [
			'name' => 'Database Manual',
			'type' => 'boolean',
			'desc' => 'Manual database configuration. Enable if you want to manually enter database details. If set to no - it will get from config.lua',
			'default' => false,
			'is_config' => true,
		],
		'database_host' => [
			'name' => 'Database Host',
			'type' => 'text',
			'default' => '127.0.0.1',
			'show_if' => [
				'database_overwrite', '=', 'true'
			],
			'is_config' => true,
		],
		'database_port' => [
			'name' => 'Database Port',
			'type' => 'number',
			'default' => 3306,
			'show_if' => [
				'database_overwrite', '=', 'true'
			],
			'is_config' => true,
		],
		'database_user' => [
			'name' => 'Database User',
			'type' => 'text',
			'default' => '',
			'show_if' => [
				'database_overwrite', '=', 'true'
			],
			'is_config' => true,
		],
		'database_password' => [
			'name' => 'Database Password',
			'type' => 'text',
			'default' => '',
			'show_if' => [
				'database_overwrite', '=', 'true'
			],
			'is_config' => true,
		],
		'database_name' => [
			'name' => 'Database Name',
			'type' => 'text',
			'default' => '',
			'show_if' => [
				'database_overwrite', '=', 'true'
			],
			'is_config' => true,
		],
		'database_socket' => [
			'name' => 'Database Socket',
			'desc' => 'Set if you want to connect to database through socket (example: /var/run/mysqld/mysqld.sock)',
			'type' => 'text',
			'default' => '',
			'show_if' => [
				'database_overwrite', '=', 'true'
			],
			'is_config' => true,
		],
		'database_hash' => [
			'name' => 'Database Hashing Algorithm',
			'desc' => 'Hashing algorithm: sha1 or md5 are most common',
			'type' => 'text',
			'default' => 'sha1',
			'show_if' => [
				'database_overwrite', '=', 'true'
			],
			'is_config' => true,
		],
		'database_log' => [
			'name' => 'Database Log',
			'desc' => 'Should database queries be logged and saved into system/logs/database.log?',
			'type' => 'boolean',
			'default' => false,
			'is_config' => true,
		],
		'database_persistent' => [
			'name' => 'Database Persistent Connection',
			'desc' => 'Use database permanent connection (like server), may speed up your site',
			'type' => 'boolean',
			'default' => false,
			'is_config' => true,
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
		'mail_other' => [
			'type' => 'section',
			'title' => 'Account E-Mails',
			'show_if' => [
				'mail_enabled', '=', 'true'
			],
		],
		'account_welcome_mail' => [
			'name' => 'Account Welcome E-Mail',
			'type' => 'boolean',
			'desc' => 'Send welcome e-mail when user registers',
			'default' => true,
			'show_if' => [
				'mail_enabled', '=', 'true'
			],
		],
		'account_mail_verify' => [
			'name' => 'Account E-Mail Verify',
			'type' => 'boolean',
			'desc' => 'Force users to confirm their e-mail addresses when registering account',
			'default' => false,
			'show_if' => [
				'mail_enabled', '=', 'true'
			],
		],
		'mail_send_when_change_password' => [
			'name' => 'Change Password E-Mail',
			'type' => 'boolean',
			'desc' => 'Send e-mail with new password when change password to account',
			'default' => true,
			'show_if' => [
				'mail_enabled', '=', 'true',
			],
		],
		'mail_send_when_generate_reckey' => [
			'name' => 'Generate Recovery Key E-Mail',
			'type' => 'boolean',
			'desc' => 'Send e-mail with recovery key (key is displayed on page anyway when generate)',
			'default' => true,
			'show_if' => [
				'mail_enabled', '=', 'true',
			],
		],
		'mail_lost_account_interval' => [
			'name' => 'Mail Lost Interface Interval',
			'type' => 'number',
			'desc' => 'Time in seconds between e-mails to one account from lost account interface, block spam',
			'default' => 60,
			'show_if' => [
				'mail_enabled', '=', 'true',
			],
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
			'show_if' => [
				'account_login_by_email', '=', 'true'
			],
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
		'account_mail_change' => [
			'name' => 'Account Mail Change Days',
			'type' => 'number',
			'desc' => 'How many days user need to change email to account - block hackers',
			'default' => 2,
		],
		'account_mail_block_plus_sign' => [
			'name' => 'Account Mail Block Plus Sign (+)',
			'type' => 'boolean',
			'desc' => "Block E-Mails with '+' signs like test+box@gmail.com (help protect against spamming accounts)",
			'default' => true,
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
		'characters_per_account' => [
			'name' => 'Characters per Account',
			'type' => 'number',
			'desc' => 'Max. number of characters per account',
			'default' => 10,
		],
		'create_character' => [
			'type' => 'section',
			'title' => 'Create Character',
		],
		'character_samples' => [
			'name' => 'Character Samples',
			'type' => 'textarea',
			'desc' => "Character Samples used when creating character.<br/>" .
						"Format: <strong>ID_of_vocation =Name of Character to copy</strong><br/>" .
			"For Rook use - <strong>0=Rook Sample</strong>",
			'default' => "1=Sorcerer Sample\n2=Druid Sample\n3=Paladin Sample\n4=Knight Sample",
			'callbacks' => [
				'get' => function ($value) {
					$ret = [];
					$vocs = array_map('trim', preg_split('/\r\n|\r|\n/', trim($value)));

					foreach ($vocs as $voc) {
						if (empty($voc)) {
							continue;
						}

						$explode = explode('=', $voc);
						$ret[$explode[0]] = $explode[1];
					}

					return $ret;
				},
			],
		],
		'character_towns' => [
			'name' => 'Towns List',
			'type' => 'text',
			'desc' => "Towns List used when creating character separated by comma (,). Won't be displayed if there is only one item (rookgaard for example)",
			'default' => '1,2',
			'callbacks' => [
				'get' => function ($value) {
					return array_map('trim', explode(',', $value));
				},
			],
		],
		'create_character_name_min_length' => [
			'name' => 'Name Min Length',
			'type' => 'number',
			'desc' => '',
			'default' => 4,
		],
		'create_character_name_max_length' => [
			'name' => 'Name Max Length',
			'type' => 'number',
			'desc' => 'It is highly recommend the maximum length to be 21',
			'default' => 21,
		],
		'create_character_name_blocked_prefix' => [
			'name' => 'Create Character Name Blocked Prefix',
			'type' => 'textarea',
			'desc' => 'Space after is important!',
			'default' => 'admin ,administrator ,gm ,cm ,god ,tutor',
			'callbacks' => [
				'get' => function ($value) {
					return explode(',', $value);
				},
			],
		],
		'create_character_name_blocked_names' => [
			'name' => 'Create Character Name Blocked Names',
			'type' => 'textarea',
			'desc' => 'Separated by comma (,)',
			'default' => 'admin,administrator,gm,cm,god,tutor',
			'callbacks' => [
				'get' => function ($value) {
					return array_map('trim', explode(',', $value));
				},
			],
		],
		'create_character_name_blocked_words' => [
			'name' => 'Create Character Name Blocked Words',
			'type' => 'textarea',
			'desc' => 'Separated by comma (,)',
			'default' => "admin,administrator,gamemaster,game master,game-master,game'master,fuck,sux,suck,noob,tutor",
			'callbacks' => [
				'get' => function ($value) {
					return array_map('trim', explode(',', $value));
				},
			],
		],
		'create_character_name_monsters_check' => [
			'name' => 'Block Monsters Names',
			'type' => 'boolean',
			'desc' => 'Should monsters names be blocked when creating character?',
			'default' => true,
		],
		'create_character_name_npc_check' => [
			'name' => 'Block NPC Names',
			'type' => 'boolean',
			'desc' => 'Should NPC names be blocked when creating character?',
			'default' => true,
		],
		'create_character_name_spells_check' => [
			'name' => 'Block Spells Names',
			'type' => 'boolean',
			'desc' => 'Should spells names and words be blocked when creating character?',
			'default' => true,
		],
		'use_character_sample_skills' => [
			'name' => 'Use Character Sample Skills',
			'type' => 'boolean',
			'desc' => 'No = default skill = 10, yes - use sample skills',
			'default' => false,
		],
		'account_mail_confirmed_reward' => [
			'type' => 'section',
			'title' => 'Reward Users for confirming their E-Mails. Works only with Account Mail Verify enabled',
			'show_if' => [
				'account_mail_verify', '=', 'true'
			],
		],
		'account_mail_confirmed_reward_premium_days' => [
			'name' => 'Reward Premium Days',
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
			'name' => 'Reward Coins',
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
			'title' => 'News Page',
		],
		'news_author' => [
			'name' => 'News Author',
			'type' => 'boolean',
			'desc' => 'Show author of the news',
			'default' => true,
		],
		'news_limit' => [
			'name' => 'News Limit',
			'type' => 'number',
			'min' => 0,
			'desc' => 'Limit of news on the latest news page (0 to disable)',
			'default' => 5,
		],
		'news_ticker_limit' => [
			'name' => 'News Ticker Limit',
			'type' => 'number',
			'min' => 0,
			'desc' => 'Limit of news in tickers (mini news) (0 to disable)',
			'default' => 5,
		],
		'news_date_format' => [
			'name' => 'News Date Format',
			'type' => 'text',
			'desc' => 'Check php manual date() function for more info about this',
			'default' => 'j.n.Y',
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
			'title' => 'Highscores Page',
		],
		'highscores_per_page' => [
			'name' => 'Highscores per Page',
			'type' => 'number',
			'min' => 1,
			'desc' => 'How many records per page on highscores',
			'default' => 100,
		],
		'highscores_cache_ttl' => [
			'name' => 'Highscores Cache TTL (in minutes)',
			'type' => 'number',
			'min' => 1,
			'desc' => 'How often to update highscores from database in minutes (default 15 minutes). Too low may cause lags on website.',
			'default' => 15,
		],
		'highscores_vocation_box' => [
			'name' => 'Display Vocation Box',
			'type' => 'boolean',
			'desc' => 'show "Choose a vocation" box on the highscores (allowing peoples to sort highscores by vocation)?',
			'default' => true,
		],
		'highscores_vocation' => [
			'name' => 'Display Vocation',
			'type' => 'boolean',
			'desc' => 'Show player vocation under his nickname?',
			'default' => true,
		],
		'highscores_frags' => [
			'name' => 'Display Top Frags',
			'type' => 'boolean',
			'desc' => 'Show "Frags" tab (best fraggers on the server)?',
			'default' => false,
		],
		'highscores_balance' => [
			'name' => 'Display Balance',
			'type' => 'boolean',
			'desc' => 'Show "Balance" tab (richest players on the server)?',
			'default' => false,
		],
		'highscores_outfit' => [
			'name' => 'Display Player Outfit',
			'type' => 'boolean',
			'desc' => 'Show player outfit?',
			'default' => true,
		],
		'highscores_country_box' => [ // not implemented yet
			'hidden' => true,
			'name' => 'Display Country Box',
			'type' => 'boolean',
			'desc' => 'Show player outfit?',
			'default' => false,
		],
		'highscores_groups_hidden' => [
			'name' => 'Hidden Groups',
			'type' => 'number',
			'desc' => "This group id and higher won't be shown on highscores",
			'default' => 3,
		],
		'highscores_ids_hidden' => [
			'name' => 'Hidden IDs of players',
			'type' => 'textarea',
			'desc' => "this ids of players will be hidden on the highscores (should be ids of samples)",
			'default' => '0',
			'callbacks' => [
				'get' => function ($value) {
					return array_map('trim', explode(',', $value));
				},
			],
		],
		[
			'type' => 'section',
			'title' => 'Characters Page',
		],
		'characters_search_limit' => [
			'name' => 'Characters Search Limit',
			'type' => 'number',
			'desc' => "How many characters (players) to show when using search function",
			'default' => 15,
		],
		'characters_level' => [
			'name' => 'Display Level',
			'type' => 'boolean',
			'desc' => 'Show characters level',
			'default' => true,
		],
		'characters_experience' => [
			'name' => 'Display Experience',
			'type' => 'boolean',
			'desc' => 'Show characters experience points',
			'default' => false,
		],
		'characters_magic_level' => [
			'name' => 'Display Magic Level',
			'type' => 'boolean',
			'desc' => 'Show characters magic level',
			'default' => false,
		],
		'characters_balance' => [
			'name' => 'Display Balance',
			'type' => 'boolean',
			'desc' => 'Show characters bank balance',
			'default' => false,
		],
		'characters_marriage' => [
			'name' => 'Display Marriage',
			'type' => 'boolean',
			'desc' => 'Show characters marriage info. Works only in TFS 0.3',
			'default' => true,
		],
		'characters_outfit' => [
			'name' => 'Display Outfit',
			'type' => 'boolean',
			'desc' => 'Show characters outfit',
			'default' => true,
		],
		'characters_creation_date' => [
			'name' => 'Display Creation Date',
			'type' => 'boolean',
			'desc' => 'Show characters date of creation',
			'default' => true,
		],
		'characters_quests' => [
			'name' => 'Display Quests',
			'type' => 'boolean',
			'desc' => 'Show characters quests. Can be configured below',
			'default' => false,
		],
		'quests' => [
			'name' => 'Quests List',
			'type' => 'textarea',
			'desc' => 'Character Quests List. Format: NameOfQuest=StorageValue',
			'default' => "Some Quest=123\nSome Quest Two=456",
			'show_if' => [
				'characters_quests', '=', 'true'
			],
			'callbacks' => [
				'get' => function ($value) {
					$ret = [];
					$quests = array_map('trim', preg_split('/\r\n|\r|\n/', trim($value)));

					foreach ($quests as $quest) {
						if (empty($quest)) {
							continue;
						}

						$explode = explode('=', $quest);
						$ret[$explode[0]] = $explode[1];
					}

					return $ret;
				},
			],
		],
		'characters_skills' => [
			'name' => 'Display Skills',
			'type' => 'boolean',
			'desc' => 'Show characters skills',
			'default' => true,
		],
		'characters_equipment' => [
			'name' => 'Display Equipment',
			'type' => 'boolean',
			'desc' => 'Show characters equipment',
			'default' => true,
		],
		'characters_frags' => [
			'name' => 'Display Frags',
			'type' => 'boolean',
			'desc' => 'Show characters frags',
			'default' => false,
		],
		'characters_deleted' => [
			'name' => 'Display Deleted',
			'type' => 'boolean',
			'desc' => 'Should deleted characters from same account be still listed on the list of characters? When enabled it will show that character is "[DELETED]',
			'default' => false,
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
			'desc' => 'How to show groups',
			'options' => [1 => 'normal table', 2 => 'in boxes, grouped by group id'],
			'default' => 2,
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
			'name' => 'Bans per Page',
			'type' => 'number',
			'min' => 1,
			'default' => 20,
			'desc' => '',
		],
		[
			'type' => 'section',
			'title' => 'Last Kills Page'
		],
		'last_kills_limit' => [
			'name' => 'Last Kills Limit',
			'type' => 'number',
			'desc' => 'Max. number of kills shown on the last kills page',
			'default' => 50,
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
			'title' => 'Signatures'
		],
		'signature_enabled' => [
			'name' => 'Enable Signatures',
			'type' => 'boolean',
			'desc' => 'Signature is a small picture with character info and server to paste on forums etc. It can be viewed on characters page, when enabled.',
			'default' => true,
		],
		'signature_type' => [
			'name' => 'Signature Type',
			'type' => 'options',
			'options' => ['tibian' => 'tibian', 'mango' => 'mango', 'gesior' => 'gesior'],
			'desc' => 'Signature engine to use',
			'default' => 'tibian',
			'show_if' => [
				'signature_enabled', '=', 'true'
			],
		],
		'signature_cache_time' => [
			'name' => 'Signature Cache Time',
			'type' => 'number',
			'min' => 1,
			'desc' => 'How long to store cached file (in minutes)',
			'default' => 5,
			'show_if' => [
				'signature_enabled', '=', 'true',
			],
		],
		'signature_browser_cache' => [
			'name' => 'Signature Browser Cache Time',
			'type' => 'number',
			'min' => 1,
			'desc' => 'How long to cache by browser (in minutes)',
			'default' => 60,
			'show_if' => [
				'signature_enabled', '=', 'true',
			],
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
			'desc' => 'Set to yes to allow picture previews for creatures',
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
			'desc' => 'Set to yes to show the loot tooltip percent',
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
		[
			'type' => 'category',
			'title' => 'Admin',
		],
		[
			'type' => 'section',
			'title' => 'Admin Panel'
		],
		'admin_plugins_manage_enable' => [
			'name' => 'Enable Plugins Manage',
			'type' => 'boolean',
			'desc' => 'You can disable possibility to upload, enable/disable and uninstall plugins, for security',
			'default' => true,
		],
		'admin_pages_php_enable' => [
			'name' => 'Enable PHP Pages',
			'type' => 'boolean',
			'desc' => 'You can disable support for plain php pages in admin panel, for security.<br/>Existing pages still will be working, so you need to delete them manually',
			'default' => false,
		],
		'admin_panel_modules' => [
			'name' => 'Modules Enabled',
			'type' => 'textarea',
			'desc' => 'What modules will be shown on Admin Panel Dashboard page',
			'default' => 'statistics,web_status,server_status,lastlogin,created,points,coins,balance',
			'callbacks' => [
				'get' => function ($value) {
					return array_map('trim', explode(',', $value));
				},
			],
		],
		[
			'type' => 'category',
			'title' => 'Shop',
		],
		[
			'type' => 'section',
			'title' => 'Gifts/shop system'
		],
		'gifts_system' => [
			'name' => 'Enable gifts system',
			'desc' => 'Plugin needs to be installed',
			'type' => 'boolean',
			'default' => false,
		],
		'donate_column' => [
			'name' => 'Donate Column',
			'type' => 'options',
			'desc' => 'What to give to player after donation - what column in accounts table to use.',
			'options' => ['premium_points' => 'Premium Points', 'coins' => 'Coins'],
			'default' => 'premium_points',
			'callbacks' => [
				'beforeSave' => function($key, $value, &$errorMessage) {
					global $db;
					if ($value == 'coins' && !$db->hasColumn('accounts', 'coins')) {
						$errorMessage = "Shop: Donate Column: Cannot set column to coins, because it doesn't exist in database.";
						return false;
					}
					return true;
				}
			]
		],
		'account_generate_new_reckey' => [
			'name' => 'Allow Generate New Key',
			'desc' => "Allow to generate new key for premium points. The player will receive e-mail with new rec key (not display on page, hacker can't generate rec key)",
			'type' => 'boolean',
			'default' => false,
		],
		'account_generate_new_reckey_price' => [
			'name' => 'Generate New Key Price',
			'type' => 'number',
			'min' => 0,
			'desc' => 'Price for new recovery key',
			'default' => 20,
			'show_if' => [
				'account_generate_new_reckey', '=', 'true',
			],
		],
		'account_change_character_name' => [
			'name' => 'Allow Change Name',
			'desc' => 'Can user change their character name for premium points?',
			'type' => 'boolean',
			'default' => false,
		],
		'account_change_character_name_price' => [
			'name' => 'Change Name Price',
			'type' => 'number',
			'min' => 0,
			'desc' => 'Cost of name change',
			'default' => 30,
			'show_if' => [
				'account_change_character_name', '=', 'true',
			],
		],
		'account_change_character_sex' => [
			'name' => 'Allow Change Sex',
			'desc' => 'Can user change their character sex for premium points?',
			'type' => 'boolean',
			'default' => false,
		],
		'account_change_character_sex_price' => [
			'name' => 'Change Sex Price',
			'type' => 'number',
			'min' => 0,
			'desc' => 'Cost of change sex',
			'default' => 30,
			'show_if' => [
				'account_change_character_sex', '=', 'true',
			],
		],
	],
	'callbacks' => [
		'beforeSave' => function(&$settings, &$values) {
			global $config;

			$configToSave = [];

			$server_path = '';
			$database = [];
			foreach ($settings['settings'] as $key => $value) {
				if (isset($value['is_config']) && getBoolean($value['is_config'])) {
					if ($value['type'] === 'boolean') {
						$values[$key] = ($values[$key] === 'true');
					}
					elseif ($value['type'] === 'number') {
						$values[$key] = (int)$values[$key];
					}
					//elseif ($value['type'] === 'options') {
					//
					//}

					$configToSave[$key] = $values[$key];

					if ($key == 'server_path') {
						$server_path = $values[$key];
					}
					elseif (str_contains($key, 'database_')) {
						$database[$key] = $values[$key];
					}

					unset($settings[$key]);
					unset($values[$key]);
				}
			}

			if($server_path[strlen($server_path) - 1] != '/')
				$server_path .= '/';

			// test config.lua existence
			// if fail - revert the setting and inform the user
			if (!file_exists($server_path . 'config.lua')) {
				error('Server Path is invalid - cannot find config.lua in the directory. Setting have been reverted.');
				$configToSave['server_path'] = $config['server_path'];
			}

			// test database connection
			// if fail - revert the setting and inform the user
			if ($database['database_overwrite'] && !Settings::testDatabaseConnection($database)) {
				foreach ($database as $key => $value) {
					if (!in_array($key, ['database_log', 'database_persistent'])) { // ignore these two
						$configToSave[$key] = $config[$key];
					}
				}
			}

			return Settings::saveConfig($configToSave, BASE . 'config.local.php');
		},
	],
];

