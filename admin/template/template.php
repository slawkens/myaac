<?php defined('MYAAC') or die('Direct access not allowed!'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php echo template_header(true);
	$title_full = (isset($title) ? $title . $config['title_separator'] : '') . $config['lua']['serverName'];
	?>
	<title><?php echo $title_full ?></title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>tools/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>tools/css/adminlte.min.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>tools/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>tools/css/datatables.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $template_path; ?>style.css"/>
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="sidebar-mini ">
<?php if ($logged && admin()) { ?>
	<div class="wrapper">
		<nav class="main-header navbar navbar-expand navbar-white navbar-light">
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
				</li>
				<li class="nav-item d-none d-sm-inline-block">
					<a href="<?php echo ADMIN_URL; ?>" class="nav-link">Home</a>
				</li>
			</ul>
			<ul class="navbar-nav ml-auto">
				<li class="nav-item">
					<a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#"><i class="fas fa-th-large"></i></a>
				</li>
			</ul>
		</nav>

		<aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-dark-info">
			<a href="<?php echo ADMIN_URL; ?>" class="brand-link logo-switch navbar-info">
				<span class="brand-text font-weight-light logo-xs"><b>M</b>A</span>
				<span class="brand-text font-weight-light logo-xl"><b>My</b>AAC</span>
			</a>
			<div class="sidebar">
				<nav class="mt-2">
					<ul class="nav nav-pills nav-sidebar flex-column nav-legacy nav-child-indent" data-widget="treeview" data-accordion="false">
						<?php
						$optionsMenu = [];
						foreach(get_plugins() as $name) {
							$pluginJson = Plugins::getPluginJson($name);
							if(!$pluginJson) {
								continue;
							}

							if (!isset($pluginJson['options']) || !file_exists(BASE . $pluginJson['options'])) {
								continue;
							}

							$optionsMenu[] = ['name' => $pluginJson['name'], 'link' => 'options&plugin=' . $name];
						}

						// Name = Display name of
						// link = Page link
						// icon = fontawesome icon namewithout "fas fa-"
						// menu = menu array for sub items.
						$menus = [
							['name' => 'Dashboard', 'link' => 'dashboard', 'icon' => 'tachometer-alt'],
							['name' => 'Config', 'link' => 'config', 'icon' => 'wrench'],
							['name' => 'News', 'link' => 'news', 'icon' => 'newspaper'],
							['name' => 'Mailer', 'link' => 'mailer', 'icon' => 'envelope'],
							['name' => 'Pages', 'icon' => 'book', 'menu' =>
								[
									['name' => 'All Pages', 'link' => 'pages'],
									['name' => 'Add new', 'link' => 'pages&action=new'],
								],
							],
							['name' => 'Menus', 'link' => 'menus', 'icon' => 'list'],
							['name' => 'Plugins', 'link' => 'plugins', 'icon' => 'plug'],
							['name' => 'Options', 'icon' => 'book', 'menu' => $optionsMenu],
							['name' => 'Visitors', 'link' => 'visitors', 'icon' => 'user'],
							['name' => 'Items', 'link' => 'items', 'icon' => 'gavel'],
							['name' => 'Editor', 'icon' => 'wrench', 'menu' =>
								[
									['name' => 'Accounts', 'link' => 'accounts'],
									['name' => 'Players', 'link' => 'players'],
								],
							],
							['name' => 'Tools', 'link' => '', 'icon' => 'tools', 'menu' =>
								[
									['name' => 'Notepad', 'link' => 'notepad'],
									['name' => 'phpinfo', 'link' => 'phpinfo'],
								],
							],
							['name' => 'Logs', 'link' => '', 'icon' => 'bug', 'menu' =>
								[
									['name' => 'Logs', 'link' => 'logs'],
									['name' => 'Reports', 'link' => 'reports'],
								],
							],
						];

						foreach ($menus as $category => $menu) {
							$has_child = isset($menu['menu']);
							if (!$has_child) {
								echo '<li class="nav-item">';
								echo '<a class="nav-link' . ($page == $menu['link'] ? ' active' : '') . '" ';
								echo 'href="?p=' . $menu['link'] . '"><i class="nav-icon fas fa-' . (isset($menu['icon']) ? $menu['icon'] : 'link') . '"></i><p>' . $menu['name'] . '</p></a></li>';
							} else if ($has_child) {
								$used_menu = null;
								$nav_construct = '';
								foreach ($menu['menu'] as $category => $sub_menu) {
									$link_title = $sub_menu['name'];
									$link_path = $sub_menu['link'];
									$link_icon = (isset($sub_menu['icon']) ? $sub_menu['icon'] : 'circle');

									$nav_construct .= '<li class="nav-item">';
									$nav_construct .= '<a href="?p=' . $link_path . '" class="nav-link';
									if ($page == $sub_menu['link']) {
										$nav_construct .= ' active';
										$used_menu = true;
									}
									$nav_construct .= '"><i class="far fa-' . $link_icon . ' nav-icon"></i><p>' . $link_title . '</p></a></li>';
								}

								echo '<li class="nav-item has-treeview' . (($used_menu) ? ' menu-open' : '') . '">
									  <a href="#" class="nav-link' . (($used_menu) ? ' active' : '') . '">
										<i class="nav-icon fas fa-' . (isset($menu['icon']) ? $menu['icon'] : 'link') . '"></i>
										<p>' . $menu['name'] . '<i class="right fas fa-angle-left"></i></p>
									  </a>
									  <ul class="nav nav-treeview">';
								echo $nav_construct;
								echo '</ul>
								</li>';
							}
						}

						$query = $db->query('SELECT `name`, `page`, `flags` FROM `' . TABLE_PREFIX . 'admin_menu` ORDER BY `ordering`');
						$menu_db = $query->fetchAll();
						foreach ($menu_db as $item) {
							if ($item['flags'] == 0 || hasFlag($item['flags'])) {
								echo '<li class="nav-item">';
								echo '<a class="nav-link' . ($page == $item['page'] ? ' active' : '') . '" ';
								echo 'href="?p=' . $item['page'] . '"><i class="nav-icon fas fa-link"></i><p>' . $item['name'] . '</p></a></li>';
							}
						}
						?>
					</ul>
				</nav>
			</div>
		</aside>

		<div class="content-wrapper" style="min-height: 823px;">
			<div class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1 class="m-0 text-dark"><?php echo(isset($title) ? $title : ''); ?><small> - Admin Panel</small></h1>
						</div>
						<div class="col-sm-6">
							<div class="float-sm-right">
								<span class="p-2 right badge badge-<?php echo(($status['online']) ? 'success' : 'danger'); ?>"><?php echo $config['lua']['serverName'] ?></span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="content">
				<div class="container-fluid">
					<?php echo $content; ?>
				</div>
			</div>
		</div>

		<aside class="control-sidebar control-sidebar-dark">
			<div class="p-3">
				<h4>Account:</h4>
				<p><h5><a href="?action=logout"><i class="fas fa-sign-out-alt text-danger"></i> Log out</h5></a>
				<small>This will log you out</small></p>
			</div>
			<div class="p-3">
				<h4>Site:</h4>
				<p><h5><a href="<?php echo BASE_URL; ?>" target="_blank"><i class="far fa-eye text-blue"></i> Preview</a></h5>
				<small>This will open a new tab</small></p>
			</div>
			<div class="p-3">
				<h4>Version:</h4>
				<p><h5><a href="?p=version"><i class="fas fa-code-branch"></i> <?php echo MYAAC_VERSION; ?></a></h5>
				<small>Check for updates</small></p>
			</div>
			<div class="p-3">
				<h4>Site:</h4>
				<p><h5><a href="https://github.com/slawkens/myaac" target="_blank"><i class="fab fa-github"></i> Github</a></h5>
				<small>Goto GitHub Page</small></p>

				<p><h5><a href="http://my-aac.org/" target="_blank"><i class="fas fa-shoe-prints"></i> MyAAC Official</a></h5>
				<small>Goto MyAAC Official Website</small></p>
			</div>
		</aside>

		<footer class="main-footer">
			<div class="float-right d-none d-sm-inline">
				<div id="status">
					<?php if ($status['online']): ?>
						<p class="success" style="width: 120px;">Server Online</p>
					<?php else: ?>
						<p class="error" style="width: 120px;">Server Offline</p>
					<?php endif; ?>
				</div>
			</div><?php echo base64_decode('UG93ZXJlZCBieSA8YSBocmVmPSJodHRwOi8vbXktYWFjLm9yZyIgdGFyZ2V0PSJfYmxhbmsiPk15QUFDLjwvYT4='); ?>
		</footer>
		<div id="sidebar-overlay"></div>
	</div>

<?php } else if (!$logged && !admin()) {
	echo $content;
}
?>

<script src="<?php echo BASE_URL; ?>tools/js/jquery-ui.min.js"></script>
<script src="<?php echo BASE_URL; ?>tools/js/datatables.min.js"></script>
<script src="<?php echo BASE_URL; ?>tools/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>tools/js/adminlte.min.js"></script>
</body>
</html>
