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
	['GET', 'news/archive/{id:int}', 'news/archive.php'],
	['GET', 'news/{id:int}', 'news/archive.php'],

	// block access to some files
	['*', 'account/base', '404.php', 10], // this is to block account/base.php
	['*', 'forum/base', '404.php', 10],
	['*', 'guilds/base', '404.php', 10],

	['GET', 'account/confirm-email/{hash:alphanum}', 'account/confirm-email.php'],

	['GET', 'bans/{page:int}', 'bans.php'],
	[['GET', 'POST'], 'characters/{name:[A-Za-z0-9-_%+\' \[\]]+}', 'characters.php'],
	['GET', 'changelog/{page:int}', 'changelog.php'],
	[['GET', 'POST'], 'monsters/{name:string}', 'monsters.php'],

	[['GET', 'POST'], 'faq/{action:string}', 'faq.php'],

	[['GET', 'POST'], 'forum/{action:string}', 'forum.php'],
	['GET', 'forum/board/{id:int}', 'forum/show_board.php'],
	['GET', 'forum/board/{id:int}/{page:[0-9]+}', 'forum/show_board.php'],
	['GET', 'forum/thread/{id:int}', 'forum/show_thread.php'],
	['GET', 'forum/thread/{id:int}/{page:int}', 'forum/show_thread.php'],

	['GET', 'gallery/{image:int}', 'gallery.php'],
	[['GET', 'POST'], 'gallery/{action:string}', 'gallery.php'],

	[['GET', 'POST'], 'guilds/{guild:string}', 'guilds/show.php'],

	['GET', 'highscores/{list:string}/{vocation:string}/{page:int}', 'highscores.php'],
	['GET', 'highscores/{list:string}/{page:int}', 'highscores.php'],
	['GET', 'highscores/{list:string}/{vocation:string}', 'highscores.php'],
	['GET', 'highscores/{list:string}', 'highscores.php'],
/*
	'/^polls\/[0-9]+\/?$/' => array('subtopic' => 'polls', 'id' => '$1'),
	'/^spells\/[A-Za-z0-9-_%]+\/[A-Za-z0-9-_]+\/?$/' => array('subtopic' => 'spells', 'vocation' => '$1', 'order' => '$2'),
	'/^houses\/view\/?$/' => array('subtopic' => 'houses', 'page' => 'view')*/

	/**
	 * Deprecated
	 * To be removed in next versions
	 * Kept just for compatibility
	 */
	[['GET', 'POST'], 'account/password', 'account/change-password.php'],
	[['GET', 'POST'], 'account/register/new', 'account/register-new.php'],
	[['GET', 'POST'], 'account/email', 'account/change-email.php'],
	[['GET', 'POST'], 'account/info', 'account/change-info.php'],
	[['GET', 'POST'], 'account/character/create', 'account/characters/create.php'],
	[['GET', 'POST'], 'account/character/name', 'account/characters/change-name.php'],
	[['GET', 'POST'], 'account/character/sex', 'account/characters/change-sex.php'],
	[['GET', 'POST'], 'account/character/delete', 'account/characters/delete.php'],
	[['GET', 'POST'], 'account/character/comment[/{name:string}]', 'account/characters/change-comment.php'],
	['GET', 'account/confirm_email/{hash:alphanum}', 'account/confirm-email.php'],
];
