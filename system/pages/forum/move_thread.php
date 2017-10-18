<?php
/**
 * Move forum thread (for moderator)
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.1
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

$save = isset($_REQUEST['save']) ? (int)$_REQUEST['save'] == 1 : false;
if($save) {
	if (Forum::isModerator()) {
		$id = (int)$_REQUEST['id'];
		$board = (int)$_REQUEST['section'];
		$post = $db->query("SELECT `id`, `first_post`, `section` FROM `" . TABLE_PREFIX . "forum` WHERE `id` = " . $id . " LIMIT 1")->fetch();
		if ($post['id'] == $id) {
			if ($post['id'] == $post['first_post']) {
				$db->query("UPDATE `" . TABLE_PREFIX . "forum` SET `section` = " . $board . " WHERE `id` = " . $post['id'] . "") or die(mysql_error());
				$nPost = $db->query('SELECT `section` FROM `' . TABLE_PREFIX . 'forum` WHERE `id` = \'' . $id . '\' LIMIT 1;')->fetch();
				header('Location: ' . getForumBoardLink($nPost['section']));
			}
		} else
			echo 'Post with ID ' . $id . ' does not exist.';
	} else
		echo 'You are not logged in or you are not moderator.';
}
else {
	if (Forum::isModerator()) {
		$id = (int)$_REQUEST['id'];
		$post = $db->query("SELECT `id`, `section`, `first_post`, `post_topic`, `author_guid` FROM `" . TABLE_PREFIX . "forum` WHERE `id` = " . $id . " LIMIT 1")->fetch();
		$name = $db->query("SELECT `name` FROM `players` WHERE `id` = " . $post['author_guid'] . " ")->fetch();
		if ($post['id'] == $id) {
			if ($post['id'] == $post['first_post']) {
				echo $twig->render('forum.move_thread.html.twig', array(
					'thread' => $post['post_topic'],
					'author' => $name[0],
					'board' => $sections[$post['section']]['name'],
					'post_id' => $post['id'],
					'sections' => $sections,
					'section_link' => getForumBoardLink($post['section']),
				));
			}
		} else
			echo 'Post with ID ' . $id . ' does not exist.';
	} else
		echo 'You are not logged in or you are not moderator.';
}
?>