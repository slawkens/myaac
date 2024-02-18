<?php

$menus = [
	['name' => 'Dashboard', 'icon' => 'tachometer-alt', 'order' => 10, 'link' => 'dashboard'],
	['name' => 'Settings', 'icon' => 'edit', 'order' => 19, 'link' =>
		require ADMIN . 'includes/settings_menus.php'
	],
	['name' => 'News', 'icon' => 'newspaper', 'order' => 20,  'link' =>
		[
			['name' => 'View', 'link' => 'news', 'icon' => 'list', 'order' => 10],
			['name' => 'Add news', 'link' => 'news&action=new&type=1', 'icon' => 'plus', 'order' => 20],
			['name' => 'Add ticker', 'link' => 'news&action=new&type=2', 'icon' => 'plus', 'order' => 30],
			['name' => 'Add article', 'link' => 'news&action=new&type=3', 'icon' => 'plus', 'order' => 40],
		],
	],
	['name' => 'Changelogs', 'icon' => 'newspaper', 'order' => 30, 'link' =>
		[
			['name' => 'View', 'link' => 'changelog', 'icon' => 'list', 'order' => 10],
			['name' => 'Add', 'link' => 'changelog&action=new', 'icon' => 'plus', 'order' => 20],
		],
	],
	['name' => 'Mailer', 'icon' => 'envelope', 'order' => 40, 'link' => 'mailer', 'disabled' => !setting('core.mail_enabled')],
	['name' => 'Pages', 'icon' => 'book', 'order' => 50, 'link' =>
		[
			['name' => 'View', 'link' => 'pages', 'icon' => 'list', 'order' => 10],
			['name' => 'Add', 'link' => 'pages&action=new', 'icon' => 'plus', 'order' => 20],
		],
	],
	['name' => 'Menus', 'icon' => 'list', 'order' => 60, 'link' => 'menus'],
	['name' => 'Plugins', 'icon' => 'plug', 'order' => 70, 'link' => 'plugins'],
	['name' => 'Server Data', 'icon' => 'gavel', 'order' => 80, 'link' => 'data'],
	['name' => 'Editor', 'icon' => 'edit', 'order' => 90, 'link' =>
		[
			['name' => 'Accounts', 'link' => 'accounts', 'icon' => 'users', 'order' => 10],
			['name' => 'Players', 'link' => 'players', 'icon' => 'user-astronaut', 'order' => 20],
		],
	],
	['name' => 'Tools', 'icon' => 'tools', 'order' => 100, 'link' =>
		[
			['name' => 'Mass Account Actions', 'link' => 'mass_account', 'icon' => 'globe', 'order' => 10],
			['name' => 'Mass Teleport Actions', 'link' => 'mass_teleport', 'icon' => 'globe', 'order' => 20],
			['name' => 'Notepad', 'link' => 'notepad', 'icon' => 'marker', 'order' => 30],
			['name' => 'phpinfo', 'link' => 'phpinfo', 'icon' => 'server', 'order' => 40],
		],
	],
	['name' => 'Logs', 'icon' => 'bug', 'order' => 110, 'link' =>
		[
			['name' => 'Logs', 'link' => 'logs', 'icon' => 'book', 'order' => 10],
			['name' => 'Reports', 'link' => 'reports', 'icon' => 'book', 'order' => 20],
			['name' => 'Visitors', 'link' => 'visitors', 'icon' => 'user', 'order' => 30],
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
