<?php
/**
 * Guilds base
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2021 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

if($db->hasTable('guild_members'))
	define('GUILD_MEMBERS_TABLE', 'guild_members');
else
	define('GUILD_MEMBERS_TABLE', 'guild_membership');

define('MOTD_EXISTS', $db->hasColumn('guilds', 'motd'));
