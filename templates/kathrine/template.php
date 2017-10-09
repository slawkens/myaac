<?php
defined('MYAAC') or die('Direct access not allowed!');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php echo template_place_holder('head_start'); ?>
		<link rel="stylesheet" href="<?php echo $template_path; ?>/style.css" type="text/css" />
		<script type="text/javascript">
			<?php
				echo $twig->render('menu.js.html.twig');
			?>
		</script>
		<script type="text/javascript" src="tools/basic.js"></script>
		<script type="text/javascript">
			var category = '<?php
				if(in_array(PAGE, array('news', 'newsarchive')))
					echo 'news';
				elseif(in_array(PAGE, array('creatures', 'spells', 'serverinfo', 'downloads', 'commands',
					'movies', 'screenshots', 'experiencetable', 'faq')))
						echo 'library';
				elseif(in_array(PAGE, array('online', 'characters', 'guilds', 'highscores', 'wars', 'lastkills', 'houses', 'bans',
					'forum', 'team')))
						echo 'community';
				elseif(in_array(PAGE, array('account', 'accountmanagement', 'createaccount', 'lostaccount', 'rules', 'bugtracker')))
					echo 'account';
				elseif(in_array(PAGE, array('points', 'gifts')))
					echo 'shops';
				?>';
		</script>
		<?php echo template_place_holder('head_end'); ?>
	</head>

	<body onload="initMenu();">
		<?php echo template_place_holder('body_start'); ?>
		<div id="top"></div>
		<div id="page">
		<!-- Keep all on center of browser -->

			<!-- Header Section -->
			<div id="header"></div>
			<!-- End -->

			<!-- Menu Section -->
			<div id="tabs">
				<span id="news" onclick="menuSwitch('news');" class="tab-active">Latest News</span>
				<span id="account" onclick="menuSwitch('account');" class="tab">Account</span>
				<span id="community" onclick="menuSwitch('community');" class="tab">Community</span>
				<span id="library" onclick="menuSwitch('library');" class="tab">Library</span>
				<?php
				if($config['gifts_system'])
				{
					echo '<span id="shops" onclick="menuSwitch(\'shops\');" class="tab">Shop</span>';
				}
				?>
			</div>

			<div id="mainsubmenu">
				<div id="news-submenu">
					<a href="<?php echo getLink('news'); ?>">Latest News</a>
					<span class="separator"></span>
					<a href="<?php echo getLink('news/archive'); ?>">News Archives</a>
				</div>

				<div id="account-submenu">
<?php
					if($logged)
					{
?>
						<a href="<?php echo getLink('account/manage'); ?>">My Account</a>
						<span class="separator"></span>
						<a href="<?php echo getLink('account/logout'); ?>">Logout</a>
						<span class="separator"></span>
<?php
					}
					else
					{
?>
						<a href="<?php echo getLink('account/manage'); ?>">Login</a>
						<span class="separator"></span>
					<a href="<?php echo getLink('account/create'); ?>">Create Account</a>
					<span class="separator"></span>
					<a href="<?php echo getLink('account/lost'); ?>">Lost Account</a>
					<span class="separator"></span>
<?php
					}
?>
					<a href="<?php echo getLink('rules'); ?>">Server Rules</a>
					<?php if($config['bug_report']): ?>
					<span class="separator"></span>
					<a href="<?php echo getLink('bugtracker'); ?>">Report Bug</a>
					<?php endif; ?>
				</div>

				<div id="community-submenu">
					<a href="<?php echo getLink('online'); ?>">Who is Online?</a>
					<span class="separator"></span>
					<a href="<?php echo getLink('characters'); ?>">Characters</a>
					<span class="separator"></span>
					<a href="<?php echo getLink('guilds'); ?>">Guilds</a>
					<?php
					if(isset($config['wars'])): ?>
					<span class="separator"></span>
					<a href="<?php echo getLink('wars'); ?>">Wars</a>
					<?php endif; ?>
					<span class="separator"></span>
					<a href="<?php echo getLink('highscores'); ?>">Highscores</a>
					<span class="separator"></span>
					<a href="<?php echo getLink('lastkills'); ?>">Last Deaths</a>
					<?php if(fieldExist('name', 'houses')): ?>
					<span class="separator"></span>
					<a href="<?php echo getLink('houses'); ?>">Houses</a>
					<?php endif;
					if($config['otserv_version'] == TFS_03): ?>
					<span class="separator"></span>
					<a href="<?php echo getLink('bans'); ?>">Bans</a>
					<?php endif;
					if($config['forum'] != ''): ?>
					<span class="separator"></span>
					<?php echo $template['link_forum']; ?>Forum</a>
					<?php endif; ?>
					<span class="separator"></span>
					<a href="<?php echo getLink('team'); ?>">Team</a>
				</div>

				<div id="library-submenu">
					<a href="<?php echo getLink('creatures'); ?>">Monsters</a>
					<span class="separator"></span>
					<a href="<?php echo getLink('spells'); ?>">Spells</a>
					<span class="separator"></span>
					<a href="<?php echo getLink('serverInfo'); ?>">Server Info</a>
					<span class="separator"></span>
					<a href="<?php echo getLink('downloads'); ?>">Downloads</a>
					<span class="separator"></span>
					<a href="<?php echo getLink('commands'); ?>">Commands</a>
					<span class="separator"></span>
					<a href="<?php echo getLink('movies'); ?>">Movies</a>
					<span class="separator"></span>
					<a href="<?php echo getLink('screenshots'); ?>">Screenshots</a>
					<span class="separator"></span>
					<a href="<?php echo getLink('experienceTable'); ?>">Experience Table</a>
					<span class="separator"></span>
					<a href="<?php echo getLink('faq'); ?>">FAQ</a>
				</div>
				<?php
				if($config['gifts_system'])
				{
					echo '
					<div id="shops-submenu">
						<a href="' . getLink('points') . '">Buy Premium Points</a>
						<span class="separator"></span>
						<a href="' . getLink('gifts') . '">Shop Offer</a>';
						if($logged)
							echo '<span class="separator"></span><a href="' . getLink('gifts/history') . '">Shop History</a>';
					echo '</div>';
				}
				?>
			</div>
			<!-- End -->

			<!-- Content Section -->
			<div id="content">
				<div id="margins">
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr>
							<td><a href="<?php echo getLink('news'); ?>"><?php echo $config['lua']['serverName']; ?></a> &raquo; <?php echo $title; ?></td>
							<td>
							<?php
							if($status['online'])
								echo '
								<font color="green"><b>Server Online</b></font> &raquo;
								Players Online: ' . $status['players'] . ' / ' . $status['playersMax'] . ' &raquo;
								Monsters: ' . $status['monsters'] . ' &raquo; Uptime: ' . (isset($status['uptimeReadable']) ? $status['uptimeReadable'] : 'Unknown') . '';
							else
								echo '<font color="red"><b>Server Offline</b></font>';
							?>
							</td>
						</tr>
					</table>
					<hr noshade="noshade" size="1" />
					<div class="Content"><div id="ContentHelper">
					<?php echo template_place_holder('center_top') . $content; ?>
					</div></div>
				</div>
			</div>
			<div id="content-bot"></div>
			<div id="copyrights">
				<p><?php echo template_footer(); ?></p>
<?php
	if($config['template_allow_change'])
		 echo '<font color="white">Template:</font><br/>' . template_form();
 ?>
			</div>
			<!-- End -->

		<!-- End -->
		</div>
		<?php echo template_place_holder('body_end'); ?>
	</body>
</html>
