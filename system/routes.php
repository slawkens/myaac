<?php
/**
 * Routes for nikic/FastRoute
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2021 MyAAC
 * @link      https://my-aac.org
 */

return [
	['GET', '', '__redirect__/news'], // redirect empty URL to news
	['GET', 'news/archive/{id:[0-9]+}[/]', 'news/archive.php'],

	['*', 'account/base[/]', '404.php'], // this is to block account/base.php
	[['GET', 'POST'], 'account/password[/]', 'account/change_password.php'],
	[['GET', 'POST'], 'account/register/new[/]', 'account/register_new.php'],
	[['GET', 'POST'], 'account/email[/]', 'account/change_email.php'],
	[['GET', 'POST'], 'account/info[/]', 'account/change_info.php'],
	[['GET', 'POST'], 'account/character/create[/]', 'account/create_character.php'],
	[['GET', 'POST'], 'account/character/name[/]', 'account/change_name.php'],
	[['GET', 'POST'], 'account/character/sex[/]', 'account/change_sex.php'],
	[['GET', 'POST'], 'account/character/delete[/]', 'account/delete_character.php'],
	[['GET', 'POST'], 'account/character/comment[/{name:[A-Za-z0-9-_%+\']+}]', 'account/change_comment.php'],
	['GET', 'account/confirm_email/{hash:[A-Za-z0-9-_]+}[/]', 'account/confirm_email.php'],

	['GET', 'bans/{page:\d+}[/]', 'bans.php'],
	[['GET', 'POST'], 'characters[/{name:[A-Za-z0-9-_%+\']+}]', 'characters.php'],
	['GET', 'changelog[/{page:\d+}]', 'changelog.php'],
	['GET', 'creatures[/{name:[A-Za-z0-9-_%+\']+}]', 'creatures.php'],

	['GET', 'faq[/{action:[A-Za-z0-9-_%+\']+}]', 'faq.php'],

	[['GET', 'POST'], 'forum/{action:[A-Za-z0-9-_]+}[/]', 'forum.php'],
	['GET', 'forum/board/{id:[0-9]+}[/]', 'forum/show_board.php'],
	['GET', 'forum/board/{id:[0-9]+}/{page:[0-9]+}[/]', 'forum/show_board.php'],
	['GET', 'forum/thread/{id:[0-9]+}[/]', 'forum/show_thread.php'],
	//['GET', 'forum/thread/{id:[0-9]+}/{page:[0-9]+}[/]', 'forum/show_thread.php'],

	['GET', 'gallery/{image:[0-9]+}[/]', 'gallery.php'],
	[['GET', 'POST'], 'gallery/{action:[A-Za-z0-9-_]+}[/]', 'gallery.php'],

	[['GET', 'POST'], 'guilds/{guild:[A-Za-z0-9-_%+\']+}[/]', 'guilds/show.php'],

	['GET', 'highscores/{list:[A-Za-z0-9-_]+}/{vocation:[A-Za-z0-9-_]+}/{page:[0-9]+}[/]', 'highscores.php'],
	['GET', 'highscores/{list:[A-Za-z0-9-_]+}/{page:[0-9]+}[/]', 'highscores.php'],
	['GET', 'highscores/{list:[A-Za-z0-9-_]+}/{vocation:[A-Za-z0-9-_]+}[/]', 'highscores.php'],
	['GET', 'highscores/{list:[A-Za-z0-9-_]+}[/]', 'highscores.php'],

	['GET', 'online/{order:[A-Za-z0-9-_]+}[/]', 'online.php'],
/*
	'/^gifts\/history\/?$/' => array('subtopic' => 'gifts', 'action' => 'show_history'),
	'/^polls\/[0-9]+\/?$/' => array('subtopic' => 'polls', 'id' => '$1'),
	'/^spells\/[A-Za-z0-9-_%]+\/[A-Za-z0-9-_]+\/?$/' => array('subtopic' => 'spells', 'vocation' => '$1', 'order' => '$2'),
	'/^houses\/view\/?$/' => array('subtopic' => 'houses', 'page' => 'view')*/
];
