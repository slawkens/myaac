<?php defined('MYAAC') or die('Direct access not allowed!'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<?php echo template_header(true); ?>
	<title><?php echo $title . $config['title_separator'] . $config['lua']['serverName']; ?> - Powered by MyAAC</title>
	<link rel="stylesheet" type="text/css" href="<?php echo $template_path; ?>style.css" />
</head>
<body>
<?php if($page != 'tools'): ?>
	<div id="container">
		<div id="header">
			<?php if($logged && admin()): ?>
			<div id="status">
				<?php if($status['online']): ?>
				<p class="success" style="width: 120px; text-align: center;">Status: Online<br/>
					<?php echo $status['uptimeReadable'] . ', ' . $status['players'] . '/' . $status['playersMax']; ?><br/>
					<?php echo $config['lua']['ip'] . ' : ' . $config['lua']['loginPort']; ?>
				</p>
				<?php else: ?>
				<p class="error" style="width: 120px; text-align: center;">Status: Offline</p>
				<?php endif; ?>
			</div>
			<div id="version">Version: <?php echo MYAAC_VERSION; ?> (<a id="update" href="?p=version">Check for updates</a>)<br/>
				Logged in as: <b><?php echo (USE_ACCOUNT_NAME ? $account_logged->getName() : $account_logged->getId()); ?></b><br/>
				<a href="<?php echo BASE_URL; ?>" target="_blank">Preview</a> <span class="separator">|</span> <a href="?action=logout">Log out<img src="<?php echo BASE_URL; ?>images/icons/logout.png" alt="" title="Log out" /></a>
			</div>
			<?php endif; ?>
			<h1><?php echo $config['lua']['serverName'] . (isset($title) ? ' - ' . $title : ''); ?> - Admin Panel</h1>
		</div>
		<div id="wrapper">
			<?php
			if($logged && admin()) {
			?>
			<div id="sidebar">
				<ul>
				<?php
					$menus = array(
						'Dashboard' => 'dashboard',
						'Mailer' => 'mailer',
						'Pages' => 'pages',
						'Menus' => 'menus',
						'Plugins' => 'plugins',
						'Statistics' => 'statistics',
						'Visitors' => 'visitors',
						'Players' => 'players',
						'Items' => 'items',
						'Tools' => array(
							'phpinfo' => 'phpinfo'
						),
						'Notepad' => 'notepad',
						'Logs' => 'logs'
					);

					$i = 0;
					foreach($menus as $_name => $_page) {
						//echo '<a ' . ($page == $_page ? ' class="current"' : '') . 'href="?p=' . $_page . '">' . $_name . '</a>';
						echo '<li><h3>';
						$has_child = is_array($_page);
						if(!$has_child) {
							echo '<a href="?p=' . $_page . '">';
							if($page == $_page) echo '<u>';
								echo $_name;
							if($page == $_page) echo '</u>';
							echo '</a>';
						}
						else
							echo $_name;

						echo '</h3>';
						if($has_child) {
							echo '<ul>';
							foreach($_page as $__name => $__page)
								echo '<li><a href="?p=' . $__page . '">';
								if($page == $__page) echo '<u>';
									echo $__name;
								if($page == $__page) echo '</u>';
								echo '</a></li>';
							echo '</ul>';
						}
						echo '</li>';
					}
				
					$query = $db->query('SELECT `name`, `page`, `flags` FROM `' . TABLE_PREFIX . 'admin_menu` ORDER BY `ordering`');
					$menu_db = $query->fetchAll();
					foreach($menu_db as $item) {
						if($item['flags'] == 0 || hasFlag($item['flags'])) {
							echo '<li><h3>
							<a href="?p=' . $item['page'] . '">';
							if($page == $item['page']) echo '<u>';
							echo $item['name'];
							if($page == $item['page']) echo '</u>';
							echo '</a></h3></li>';
						}
					}
				?>
				</ul>
			</div>
			<?php
			}
			?>
			<div id="content"><?php echo $content; ?></div>
		</div>
		<div id="footer">
			<?php echo base64_decode('UG93ZXJlZCBieSA8YSBocmVmPSJodHRwOi8vbXktYWFjLm9yZyIgdGFyZ2V0PSJfYmxhbmsiPk15QUFDLjwvYT4='); ?>
		</div>
	</div>
<?php endif; ?>
</body>
</html>
