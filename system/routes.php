<?php
/**
 * Routes for nikic/FastRoute
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2021 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

return [
	['GET', '', 'news.php'], // empty URL = show news
	['GET', 'news/archive/{id:int}[/]', 'news/archive.php'],

	// block access to some files
	['*', 'account/base[/]', '404.php'], // this is to block account/base.php
	['*', 'forum/base[/]', '404.php'],
	['*', 'guilds/base[/]', '404.php'],

	[['GET', 'POST'], 'account/password[/]', 'account/change_password.php'],
	[['GET', 'POST'], 'account/register/new[/]', 'account/register_new.php'],
	[['GET', 'POST'], 'account/email[/]', 'account/change_email.php'],
	[['GET', 'POST'], 'account/info[/]', 'account/change_info.php'],
	[['GET', 'POST'], 'account/character/create[/]', 'account/create_character.php'],
	[['GET', 'POST'], 'account/character/name[/]', 'account/change_name.php'],
	[['GET', 'POST'], 'account/character/sex[/]', 'account/change_sex.php'],
	[['GET', 'POST'], 'account/character/delete[/]', 'account/delete_character.php'],
	[['GET', 'POST'], 'account/character/comment[/{name:[A-Za-z0-9-_%+\']+}]', 'account/change_comment.php'],
	['GET', 'account/confirm_email/{hash:alphanum}[/]', 'account/confirm_email.php'],

	['GET', 'bans/{page:int}[/]', 'bans.php'],
	[['GET', 'POST'], 'characters[/{name:string}]', 'characters.php'],
	['GET', 'changelog[/{page:int}]', 'changelog.php'],
	[['GET', 'POST'], 'creatures[/{name:string}]', 'creatures.php'],

	[['GET', 'POST'], 'faq[/{action:string}]', 'faq.php'],

	[['GET', 'POST'], 'forum/{action:string}[/]', 'forum.php'],
	['GET', 'forum/board/{id:int}[/]', 'forum/show_board.php'],
	['GET', 'forum/board/{id:int}/{page:[0-9]+}[/]', 'forum/show_board.php'],
	['GET', 'forum/thread/{id:int}[/]', 'forum/show_thread.php'],
	['GET', 'forum/thread/{id:int}/{page:int}[/]', 'forum/show_thread.php'],

	['GET', 'gallery/{image:int}[/]', 'gallery.php'],
	[['GET', 'POST'], 'gallery/{action:string}[/]', 'gallery.php'],

	[['GET', 'POST'], 'guilds/{guild:string}[/]', 'guilds/show.php'],

	['GET', 'highscores/{list:alphanum}/{vocation:alphanum}/{page:int}[/]', 'highscores.php'],
	['GET', 'highscores/{list:alphanum}/{page:int}[/]', 'highscores.php'],
	['GET', 'highscores/{list:alphanum}/{vocation:alphanum}[/]', 'highscores.php'],
	['GET', 'highscores/{list:alphanum}[/]', 'highscores.php'],
/*
	'/^gifts\/history\/?$/' => array('subtopic' => 'gifts', 'action' => 'show_history'),
	'/^polls\/[0-9]+\/?$/' => array('subtopic' => 'polls', 'id' => '$1'),
	'/^spells\/[A-Za-z0-9-_%]+\/[A-Za-z0-9-_]+\/?$/' => array('subtopic' => 'spells', 'vocation' => '$1', 'order' => '$2'),
	'/^houses\/view\/?$/' => array('subtopic' => 'houses', 'page' => 'view')*/
];
