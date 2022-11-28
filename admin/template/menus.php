<?php

$menus = [
	['name' => 'Dashboard', 'icon' => 'tachometer-alt', 'order' => 10, 'link' => 'dashboard'],
	['name' => 'News', 'icon' => 'newspaper', 'order' => 20, 'link' =>
		[
			['name' => 'View', 'link' => 'news', 'order' => 10],
			['name' => 'Add news', 'link' => 'news&action=new&type=1', 'order' => 20],
			['name' => 'Add ticker', 'link' => 'news&action=new&type=2', 'order' => 30],
			['name' => 'Add article', 'link' => 'news&action=new&type=3', 'order' => 40],
		],
	],
	['name' => 'Changelogs', 'icon' => 'newspaper', 'order' => 30, 'link' =>
		[
			['name' => 'View', 'link' => 'changelog', 'order' => 10],
			['name' => 'Add', 'link' => 'changelog&action=new', 'order' => 20],
		],
	],
	['name' => 'Mailer', 'icon' => 'envelope', 'order' => 40, 'link' => 'mailer', 'disabled' => !config('mail_enabled')],
	['name' => 'Pages', 'icon' => 'book', 'order' => 50, 'link' =>
		[
			['name' => 'View', 'link' => 'pages', 'order' => 10],
			['name' => 'Add', 'link' => 'pages&action=new', 'order' => 20],
		],
	],
	['name' => 'Menus', 'icon' => 'list', 'order' => 60, 'link' => 'menus'],
	['name' => 'Plugins', 'icon' => 'plug', 'order' => 70, 'link' => 'plugins'],
	['name' => 'Server Data', 'icon' => 'gavel', 'order' => 80, 'link' => 'data'],
	['name' => 'Editor', 'icon' => 'edit', 'order' => 90, 'link' =>
		[
			['name' => 'Accounts', 'link' => 'accounts', 'order' => 10],
			['name' => 'Players', 'link' => 'players', 'order' => 20],
		],
	],
	['name' => 'Tools', 'icon' => 'tools', 'order' => 100, 'link' =>
		[
			['name' => 'Notepad', 'link' => 'notepad', 'order' => 10],
			['name' => 'phpinfo', 'link' => 'phpinfo', 'order' => 20],
		],
	],
	['name' => 'Logs', 'icon' => 'bug', 'order' => 110, 'link' =>
		[
			['name' => 'Logs', 'link' => 'logs', 'order' => 10],
			['name' => 'Reports', 'link' => 'reports', 'order' => 20],
			['name' => 'Visitors', 'icon' => 'user', 'link' => 'visitors', 'order' => 30],
		],
	],
];

$hooks->trigger(HOOK_ADMIN_MENU);

usort($menus, function ($a, $b) {
	return $a['order'] - $b['order'];
});

foreach ($menus as $i => $menu) {
	if (isset($menu['link']) && is_array($menu['link'])) {
		usort($menus[$i]['link'], function ($a, $b) {
			return $a['order'] - $b['order'];
		});
	}
}

return $menus;
