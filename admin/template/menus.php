<?php

$menus = [
	['name' => 'Dashboard', 'icon' => 'tachometer-alt', 'link' => 'dashboard'],
	['name' => 'News', 'icon' => 'newspaper',  'link' =>
		[
			['name' => 'View', 'link' => 'news', 'order' => 1],
			['name' => 'Add news', 'link' => 'news&action=new&type=1', 'order' => 2],
			['name' => 'Add ticker', 'link' => 'news&action=new&type=2', 'order' => 3],
			['name' => 'Add article', 'link' => 'news&action=new&type=3', 'order' => 4],
		],
	],
	['name' => 'Changelogs', 'icon' => 'newspaper',  'link' =>
		[
			['name' => 'View', 'link' => 'changelog', 'order' => 1],
			['name' => 'Add', 'link' => 'changelog&action=new', 'order' => 2],
		],
	],
	['name' => 'Mailer', 'icon' => 'envelope', 'link' => 'mailer', 'disabled' => !config('mail_enabled')],
	['name' => 'Pages', 'icon' => 'book', 'link' =>
		[
			['name' => 'View', 'link' => 'pages', 'order' => 1],
			['name' => 'Add', 'link' => 'pages&action=new', 'order' => 2],
		],
	],
	['name' => 'Menus', 'icon' => 'list', 'link' => 'menus'],
	['name' => 'Plugins', 'icon' => 'plug', 'link' => 'plugins'],
	['name' => 'Server Data', 'icon' => 'gavel', 'link' => 'data'],
	['name' => 'Editor', 'icon' => 'edit', 'link' =>
		[
			['name' => 'Accounts', 'link' => 'accounts', 'order' => 1],
			['name' => 'Players', 'link' => 'players', 'order' => 2],
		],
	],
	['name' => 'Tools', 'icon' => 'tools', 'link' =>
		[
			['name' => 'Notepad', 'link' => 'notepad', 'order' => 1],
			['name' => 'phpinfo', 'link' => 'phpinfo', 'order' => 2],
		],
	],
	['name' => 'Logs', 'icon' => 'bug', 'link' =>
		[
			['name' => 'Logs', 'link' => 'logs', 'order' => 1],
			['name' => 'Reports', 'link' => 'reports', 'order' => 2],
			['name' => 'Visitors', 'icon' => 'user', 'link' => 'visitors', 'order' => 3],
		],
	],
];

$hooks->trigger(HOOK_ADMIN_MENU);

foreach ($menus as $i => $menu) {
	if (isset($menu['link']) && is_array($menu['link'])) {
		usort($menus[$i]['link'], function ($a, $b) {
			return $a['order'] - $b['order'];
		});
	}
}

return $menus;
