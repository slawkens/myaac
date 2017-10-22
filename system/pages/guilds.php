<?php
/**
 * Guilds
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.6
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Guilds';

if(tableExist('guild_members'))
	define('GUILD_MEMBERS_TABLE', 'guild_members');
else
	define('GUILD_MEMBERS_TABLE', 'guild_membership');

define('MOTD_EXISTS', fieldExist('motd', 'guilds'));

//show list of guilds
if(empty($action)) {
	require(PAGES . 'guilds/list_of_guilds.php');
}
else if(file_exists(PAGES . 'guilds/' . $action . '.php')) {
	require(PAGES . 'guilds/' . $action . '.php');
}
?>
