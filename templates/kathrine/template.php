<?php
defined('MYAAC') or die('Direct access not allowed!');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    	<?php echo template_place_holder('head_start'); ?>
        <link rel="stylesheet" href="<?php echo $template_path; ?>/style.css" type="text/css" />
        <script src="<?php echo $template_path; ?>/menu.js" type="text/javascript"></script>
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
				elseif(in_array(PAGE, array('account', 'accountmanagement', 'createaccount', 'lostaccount', 'rules')))
					echo 'account';
				elseif(in_array(PAGE, array('points', 'gifts')))
					echo 'shops';
				?>';
		</script>
        <?php echo template_place_holder('head_end'); ?>
    </head>

    <body onload="initMenu();">
		<?php echo template_place_holder('body_start'); ?>
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
                    <a href="<?php echo $template['link_news']; ?>">Latest News</a>
                    <span class="separator"></span>
                    <a href="<?php echo $template['link_news_archive']; ?>">News Archives</a>
                </div>

                <div id="account-submenu">
<?php
					if($logged)
					{
?>
						<a href="<?php echo $template['link_account_manage']; ?>">My Account</a>
						<span class="separator"></span>
						<a href="<?php echo $template['link_account_logout']; ?>">Logout</a>
						<span class="separator"></span>
<?php
					}
					else
					{
?>
						<a href="<?php echo $template['link_account_manage']; ?>">Login</a>
						<span class="separator"></span>
					<a href="<?php echo $template['link_account_create']; ?>">Create Account</a>
                    <span class="separator"></span>
					<a href="<?php echo $template['link_account_lost']; ?>">Lost Account</a>
                    <span class="separator"></span>
<?php
					}
?>
					<a href="<?php echo $template['link_rules']; ?>">Server Rules</a>
                </div>

                <div id="community-submenu">
                    <a href="<?php echo $template['link_online']; ?>">Who is Online?</a>
                    <span class="separator"></span>
                	<a href="<?php echo $template['link_characters']; ?>">Characters</a>
                    <span class="separator"></span>
                    <a href="<?php echo $template['link_guilds']; ?>">Guilds</a>
                    <span class="separator"></span>
                    <?php
                    if(isset($config['wars']))
						echo '
							<a href="' . $template['link_wars'] . '">Wars</a>
							<span class="separator"></span>';
					?>
                    <a href="<?php echo $template['link_highscores']; ?>">Highscores</a>
                    <span class="separator"></span>
                    <a href="<?php echo $template['link_lastkills']; ?>">Last Deaths</a>
					<?php if(fieldExist('name', 'houses')): ?>
                    <span class="separator"></span>
                    <a href="<?php echo $template['link_houses']; ?>">Houses</a>
					<?php endif; ?>
                    <span class="separator"></span>
					<?php if($config['otserv_version'] == TFS_03): ?>
                    <a href="<?php echo $template['link_bans']; ?>">Bans</a>
					<?php endif; ?>
                    <span class="separator"></span>
                    <?php
                    if($config['forum'] != '')
						echo 
						$template['link_forum'] . 'Forum</a>
						<span class="separator"></span>';
					?>
                    <a href="<?php echo $template['link_team']; ?>">Team</a>
                </div>

                <div id="library-submenu">
                	<a href="<?php echo $template['link_creatures']; ?>">Monsters</a>
                    <span class="separator"></span>
                    <a href="<?php echo $template['link_spells']; ?>">Spells</a>
                    <span class="separator"></span>
                    <a href="<?php echo $template['link_serverInfo']; ?>">Server Info</a>
                    <span class="separator"></span>
                    <a href="<?php echo $template['link_downloads']; ?>">Downloads</a>
                    <span class="separator"></span>
                    <a href="<?php echo $template['link_commands']; ?>">Commands</a>
                    <span class="separator"></span>
                    <a href="<?php echo $template['link_movies']; ?>">Movies</a>
                    <span class="separator"></span>
                    <a href="<?php echo $template['link_screenshots']; ?>">Screenshots</a>
                    <span class="separator"></span>
                    <a href="<?php echo $template['link_experienceTable']; ?>">Experience Table</a>
                    <span class="separator"></span>
                    <a href="<?php echo $template['link_faq']; ?>">FAQ</a>
                </div>
    			<?php
				if($config['gifts_system'])
				{
					echo '
					<div id="shops-submenu">
						<a href="' . $template['link_points'] . '">Buy Premium Points</a>
						<span class="separator"></span>
						<a href="' . $template['link_gifts'] . '">Shop Offer</a>';
						if($logged)
							echo '<span class="separator"></span><a href="' . $template['link_gifts_history'] . '">Shop History</a>';
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
                        	<td><a href="<?php echo $template['link_news']; ?>"><?php echo $config['lua']['serverName']; ?></a> &raquo; <?php echo $title; ?></td>
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
                    <?php echo $content; ?>
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
