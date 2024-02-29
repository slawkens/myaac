<?php defined('MYAAC') or die('Direct access not allowed!'); ?>
<!doctype html>
<html lang="en">
<head>
	<?php $hooks->trigger(HOOK_ADMIN_HEAD_START); ?>
	<?php echo template_header(true); ?>
	<title><?php echo (isset($title) ? $title . ' - ' : '') . $config['lua']['serverName'];?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>tools/css/adminlte.min.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>tools/css/font-awesome.min.css">
	<?php if (isset($use_datatable)) { ?>
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>tools/css/datatables.bs.min.css">
	<?php } ?>
	<link rel="stylesheet" type="text/css" href="<?php echo $template_path; ?>style.css"/>
	<!--[if lt IE 9]>
	<script src="<?php echo BASE_URL; ?>tools/js/html5shiv.min.js"></script>
	<script src="<?php echo BASE_URL; ?>tools/js/respond.min.js"></script>
	<![endif]-->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
	<?php $hooks->trigger(HOOK_ADMIN_HEAD_END); ?>
</head>
<body class="sidebar-mini ">
<?php $hooks->trigger(HOOK_ADMIN_BODY_START); ?>
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
		<aside class="main-sidebar sidebar-dark-info elevation-4">
			<a href="<?php echo ADMIN_URL; ?>" class="brand-link navbar-info">
				<img src="<?php echo ADMIN_URL; ?>images/logo.png" class="brand-image img-circle elevation-3" style="opacity: .8">
				<span class="brand-text"><b>My</b>AAC</span>
			</a>
			<div class="sidebar">
				<nav class="mt-1">
					<ul class="nav nav-pills nav-sidebar flex-column nav-legacy nav-child-indent" data-widget="treeview" data-accordion="false">
						<li class="menu-text-li">
							<span class="menu-text">
								<a class="text-info" href="<?php echo BASE_URL; ?>" target="_blank">
									<?php echo $config['lua']['serverName'] ?>
								</a>
							</span>
						</li>
						<?php
						// name = Display name of link
						// icon = fontawesome icon name without "fas fa-"
						// link = Page link or use as array for sub items
						$menus = require __DIR__ . '/menus.php';

						foreach ($menus as $category => $menu) {
							if (isset($menu['disabled']) && $menu['disabled']) {
								continue;
							}

							$has_child = is_array($menu['link']);
							if (!$has_child) { ?>
								<li class="nav-item">
									<a class="nav-link<?php echo(strpos($menu['link'], $page) !== false ? ' active' : '') ?>" href="?p=<?php echo $menu['link'] ?>">
										<i class="nav-icon fas fa-<?php echo($menu['icon'] ?? 'link') ?>"></i>
										<p><?php echo $menu['name'] ?></p>
									</a>
								</li>
								<?php
							} else if ($has_child) {
								$used_menu = null;
								$nav_construct = '';
								foreach ($menu['link'] as $sub_category => $sub_menu) {
									$nav_construct .= '<li class="nav-item"><a href="?p=' . $sub_menu['link'] . '" class="nav-link';
									if ($_SERVER['QUERY_STRING'] == 'p=' . $sub_menu['link']) {
										$nav_construct .= ' active';
										$used_menu = true;
									}
									$nav_construct .= '"><i class="fas fa-' . ($sub_menu['icon'] ?? 'circle') . ' nav-icon"></i><p>' . $sub_menu['name'] . '</p></a></li>';
								}
								?>
								<li class="nav-item has-treeview<?php echo($used_menu ? ' menu-open' : '') ?>">
									<a href="#" class="nav-link<?php echo($used_menu ? ' active' : '') ?>">
										<i class="nav-icon fas fa-<?php echo($menu['icon'] ?? 'link') ?>"></i>
										<p><?php echo $menu['name'] ?></p><i class="right fas fa-angle-left"></i>
									</a>
									<ul class="nav nav-treeview">
										<?php echo $nav_construct; ?>
									</ul>
								</li>
								<?php
							}
						}

						$query = $db->query('SELECT `name`, `page`, `flags` FROM `' . TABLE_PREFIX . 'admin_menu` ORDER BY `ordering`');
						$menu_db = $query->fetchAll();
						foreach ($menu_db as $item) {
							if ($item['flags'] == 0 || hasFlag($item['flags'])) { ?>
								<li class="nav-item">
									<a class="nav-link<?php echo($page == $item['page'] ? ' active' : '') ?>" href="?p=<?php echo $item['page'] ?>">
										<i class="nav-icon fas fa-link"></i>
										<p><?php echo $item['name'] ?></p>
									</a>
								</li>
								<?php
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
							<h3 class="m-0 text-dark"><?php echo(isset($title) ? $title : ''); ?><small> - Admin Panel</small></h3>
						</div>
						<div class="col-sm-6">
							<div class="float-sm-right d-none d-sm-inline">
								<span class="p-2 right badge badge-<?php echo((isset($status['online']) and $status['online']) ? 'success' : 'danger'); ?>"><?php echo $config['lua']['serverName'] ?></span>
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

				<p><h5><a href="?p=open_source"><i class="fas fa-wrench"></i> Open Source</a></h5>
				<small>View Open Source Software MyAAC is using</small></p>
			</div>
		</aside>

		<footer class="main-footer">
			<div class="float-sm-right d-none d-sm-inline">
				<span class="p-2 right badge badge-<?php echo((isset($status['online']) and $status['online']) ? 'success' : 'danger'); ?>"><?php echo $config['lua']['serverName'] ?></span>
			</div>
			<?php echo base64_decode('UG93ZXJlZCBieSA8YSBocmVmPSJodHRwOi8vbXktYWFjLm9yZyIgdGFyZ2V0PSJfYmxhbmsiPk15QUFDLjwvYT4='); ?>
		</footer>
		<div id="sidebar-overlay"></div>
	</div>

<?php } else if (!$logged && !admin()) {
	echo $content;
}
?>
<?php
/**
 * @var OTS_Account $account_logged
 */
if ($logged && admin()) {
	$twig->display('admin-bar.html.twig', [
		'username' => USE_ACCOUNT_NAME ? $account_logged->getName() : $account_logged->getId()
	]);
}
?>
<script src="<?php echo BASE_URL; ?>tools/ext/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>tools/ext/jquery-ui/jquery-ui.min.js"></script>
<?php if (isset($use_datatable))  { ?>
<script src="<?php echo BASE_URL; ?>tools/js/datatables.min.js"></script>
<script src="<?php echo BASE_URL; ?>tools/js/datatables.bs.min.js"></script>
<?php } ?>
<script src="<?php echo BASE_URL; ?>tools/js/adminlte.min.js"></script>
<?php $hooks->trigger(HOOK_ADMIN_BODY_END); ?>
</body>
</html>
