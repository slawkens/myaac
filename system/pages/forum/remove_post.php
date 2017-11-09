<?php
/**
 * Remove forum post
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

if(Forum::isModerator())
{
	$id = (int) $_REQUEST['id'];
	$post = $db->query("SELECT `id`, `first_post`, `section` FROM `" . TABLE_PREFIX . "forum` WHERE `id` = ".$id." LIMIT 1")->fetch();
	if($post['id'] == $id && Forum::hasAccess($post['section']))
	{
		if($post['id'] == $post['first_post'])
		{
			$db->query("DELETE FROM `" . TABLE_PREFIX . "forum` WHERE `first_post` = ".$post['id']);
			header('Location: ' . getForumBoardLink($post['section']));
		}
		else
		{
			$post_page = $db->query("SELECT COUNT(`" . TABLE_PREFIX . "forum`.`id`) AS posts_count FROM `players`, `" . TABLE_PREFIX . "forum` WHERE `players`.`id` = `" . TABLE_PREFIX . "forum`.`author_guid` AND `" . TABLE_PREFIX . "forum`.`id` < ".$id." AND `" . TABLE_PREFIX . "forum`.`first_post` = ".(int) $post['first_post'])->fetch();
			$_page = (int) ceil($post_page['posts_count'] / $config['forum_threads_per_page']) - 1;
			$db->query("DELETE FROM `" . TABLE_PREFIX . "forum` WHERE `id` = ".$post['id']);
			header('Location: ' . getForumThreadLink($post['first_post'], (int) $_page));
		}
	}
	else
		echo 'Post with ID ' . $id . ' does not exist.';
}
else
	echo 'You are not logged in or you are not moderator.';