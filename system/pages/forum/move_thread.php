<?php
/**
 * Move forum thread (for moderator)
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Forum;

defined('MYAAC') or die('Direct access not allowed!');

$ret = require __DIR__ . '/base.php';
if ($ret === false) {
	return;
}

if(!$logged) {
	echo 'You are not logged in. <a href="' . getLink('account/manage') . '?redirect=' . urlencode(getLink('forum')) . '">Log in</a> to post on the forum.<br /><br />';
	return;
}

if(!Forum::isModerator()) {
	echo 'You are not logged in or you are not moderator.';
	return;
}

$save = isset($_REQUEST['save']) && (int)$_REQUEST['save'] == 1;
if($save) {
	$post_id = (int)$_REQUEST['id'];
	$board = (int)$_REQUEST['section'];
	if(!Forum::hasAccess($board)) {
		$errors[] = "You don't have access to this board.";
		displayErrorBoxWithBackButton($errors, getLink('forum'));
		return;
	}

	$post = $db->query("SELECT `id`, `first_post`, `section` FROM `" . FORUM_TABLE_PREFIX . "forum` WHERE `id` = " . $post_id . " LIMIT 1")->fetch();
	if ($post['id'] == $post_id) {
		if ($post['id'] == $post['first_post']) {
			$db->query("UPDATE `" . FORUM_TABLE_PREFIX . "forum` SET `section` = " . $board . " WHERE `id` = " . $post['id'] . "");
			$nPost = $db->query('SELECT `section` FROM `' . FORUM_TABLE_PREFIX . 'forum` WHERE `id` = \'' . $post_id . '\' LIMIT 1;')->fetch();
			header('Location: ' . getForumBoardLink($nPost['section']));
		}
	}
	else {
		$errors[] = 'Post with ID ' . $post_id . ' does not exist.';
		displayErrorBoxWithBackButton($errors, getLink('forum'));
	}
}
else {
	$post_id = (int)$_REQUEST['id'];
	$post = $db->query("SELECT `id`, `section`, `first_post`, `post_topic`, `author_guid` FROM `" . FORUM_TABLE_PREFIX . "forum` WHERE `id` = " . $post_id . " LIMIT 1")->fetch();
	$name = $db->query("SELECT `name` FROM `players` WHERE `id` = " . $post['author_guid'] . " ")->fetch();

	$sections_allowed = array();
	foreach($sections as $id => $section) {
		if(Forum::hasAccess($id)) {
			$sections_allowed[$id] = $section;
		}
	}

	if ($post['id'] == $post_id) {
		if ($post['id'] == $post['first_post']) {
			$twig->display('forum.move_thread.html.twig', array(
				'thread' => $post['post_topic'],
				'author' => $name['name'],
				'board' => $sections[$post['section']]['name'],
				'post_id' => $post['id'],
				'sections' => $sections_allowed,
				'section_link' => getForumBoardLink($post['section']),
			));
		}
	}
	else {
		$errors[] = 'Post with ID ' . $post_id . ' does not exist.';
		displayErrorBoxWithBackButton($errors, getLink('forum'));
	}
}
