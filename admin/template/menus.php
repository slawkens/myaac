<?php

return [
	['name' => 'Dashboard', 'icon' => 'tachometer-alt', 'link' => 'dashboard'],
	['name' => 'Settings', 'icon' => 'edit', 'link' => 'settings&plugin=core'],
	['name' => 'News', 'icon' => 'newspaper',  'link' =>
		[
			['name' => 'View', 'link' => 'news'],
			['name' => 'Add news', 'link' => 'news&action=new&type=1'],
			['name' => 'Add ticker', 'link' => 'news&action=new&type=2'],
			['name' => 'Add article', 'link' => 'news&action=new&type=3'],
		],
	],
	['name' => 'Changelogs', 'icon' => 'newspaper',  'link' =>
		[
			['name' => 'View', 'link' => 'changelog'],
			['name' => 'Add', 'link' => 'changelog&action=new'],
		],
	],
	['name' => 'Mailer', 'icon' => 'envelope', 'link' => 'mailer', 'disabled' => !config('mail_enabled')],
	['name' => 'Pages', 'icon' => 'book', 'link' =>
		[
			['name' => 'View', 'link' => 'pages'],
			['name' => 'Add', 'link' => 'pages&action=new'],
		],
	],
	['name' => 'Menus', 'icon' => 'list', 'link' => 'menus'],
	['name' => 'Plugins', 'icon' => 'plug', 'link' => 'plugins'],
	['name' => 'Server Data', 'icon' => 'gavel', 'link' => 'data'],
	['name' => 'Editor', 'icon' => 'edit', 'link' =>
		[
			['name' => 'Accounts', 'link' => 'accounts'],
			['name' => 'Players', 'link' => 'players'],
		],
	],
	['name' => 'Tools', 'icon' => 'tools', 'link' =>
		[
			['name' => 'Notepad', 'link' => 'notepad'],
			['name' => 'phpinfo', 'link' => 'phpinfo'],
		],
	],
	['name' => 'Logs', 'icon' => 'bug', 'link' =>
		[
			['name' => 'Logs', 'link' => 'logs'],
			['name' => 'Reports', 'link' => 'reports'],
			['name' => 'Visitors', 'icon' => 'user', 'link' => 'visitors'],
		],
	],
];
