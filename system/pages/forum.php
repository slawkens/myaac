<?php
/**
 * Forum
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2021 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or exit;

require __DIR__ . '/forum/base.php';
require __DIR__ . '/forum/admin.php';

$errors = [];
if(!empty($action))
{
	if(!ctype_alnum(str_replace(array('-', '_'), '', $action))) {
		error('Error: Action contains illegal characters.');
	}
	else if(file_exists(PAGES . 'forum/' . $action . '.php')) {
		require PAGES . 'forum/' . $action . '.php';
		return;
	}
	else {
		error('This page does not exists.');
	}
}

$info = $db->query("SELECT `section`, COUNT(`id`) AS 'threads', SUM(`replies`) AS 'replies' FROM `" . FORUM_TABLE_PREFIX . "forum` WHERE `first_post` = `id` GROUP BY `section`")->fetchAll();

$boards = array();
foreach($info as $data)
	$counters[$data['section']] = array('threads' => $data['threads'], 'posts' => $data['replies'] + $data['threads']);

foreach($sections as $id => $section)
{
	$show = true;
	if(Forum::hasAccess($id)) {
		$last_post = $db->query("SELECT `players`.`name`, `" . FORUM_TABLE_PREFIX . "forum`.`post_date` FROM `players`, `" . FORUM_TABLE_PREFIX . "forum` WHERE `" . FORUM_TABLE_PREFIX . "forum`.`section` = ".(int) $id." AND `players`.`id` = `" . FORUM_TABLE_PREFIX . "forum`.`author_guid` ORDER BY `post_date` DESC LIMIT 1")->fetch();
		$boards[] = array(
			'id' => $id,
			'link' => getForumBoardLink($id),
			'name' => $section['name'],
			'description' => $section['description'],
			'hidden' => $section['hidden'],
			'posts' => isset($counters[$id]['posts']) ? $counters[$id]['posts'] : 0,
			'threads' => isset($counters[$id]['threads']) ? $counters[$id]['threads'] : 0,
			'last_post' => array(
				'name' => isset($last_post['name']) ? $last_post['name'] : null,
				'date' => isset($last_post['post_date']) ? $last_post['post_date'] : null,
				'player_link' => isset($last_post['name']) ? getPlayerLink($last_post['name']) : null,
			)
		);
	}
}

$twig->display('forum.boards.html.twig', array(
	'boards' => $boards,
	'canEdit' => $canEdit,
	'last' => count($sections)
));
