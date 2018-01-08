<?php
/**
 * Show forum thread
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

$links_to_pages = '';
$thread_id = (int) $_REQUEST['id'];
$_page = (int) (isset($_REQUEST['page']) ? $_REQUEST['page'] : 0);
$thread_starter = $db->query("SELECT `players`.`name`, `" . TABLE_PREFIX . "forum`.`post_topic`, `" . TABLE_PREFIX . "forum`.`section` FROM `players`, `" . TABLE_PREFIX . "forum` WHERE `" . TABLE_PREFIX . "forum`.`first_post` = ".(int) $thread_id." AND `" . TABLE_PREFIX . "forum`.`id` = `" . TABLE_PREFIX . "forum`.`first_post` AND `players`.`id` = `" . TABLE_PREFIX . "forum`.`author_guid` LIMIT 1")->fetch();

if(empty($thread_starter['name'])) {
	echo 'Thread with this ID does not exits.';
	return;
}

if(!Forum::hasAccess($thread_starter['section'])) {
	echo "You don't have access to view this thread.";
	return;
}

$posts_count = $db->query("SELECT COUNT(`" . TABLE_PREFIX . "forum`.`id`) AS posts_count FROM `players`, `" . TABLE_PREFIX . "forum` WHERE `players`.`id` = `" . TABLE_PREFIX . "forum`.`author_guid` AND `" . TABLE_PREFIX . "forum`.`first_post` = ".(int) $thread_id)->fetch();
for($i = 0; $i < $posts_count['posts_count'] / $config['forum_threads_per_page']; $i++)
{
	if($i != $_page)
		$links_to_pages .= '<a href="' . getForumThreadLink($thread_id, $i) . '">'.($i + 1).'</a> ';
	else
		$links_to_pages .= '<b>'.($i + 1).' </b>';
}
$posts = $db->query("SELECT `players`.`id` as `player_id`, `" . TABLE_PREFIX . "forum`.`id`,`" . TABLE_PREFIX . "forum`.`first_post`, `" . TABLE_PREFIX . "forum`.`section`,`" . TABLE_PREFIX . "forum`.`post_text`, `" . TABLE_PREFIX . "forum`.`post_topic`, `" . TABLE_PREFIX . "forum`.`post_date` AS `date`, `" . TABLE_PREFIX . "forum`.`post_smile`, `" . TABLE_PREFIX . "forum`.`post_html`, `" . TABLE_PREFIX . "forum`.`author_aid`, `" . TABLE_PREFIX . "forum`.`author_guid`, `" . TABLE_PREFIX . "forum`.`last_edit_aid`, `" . TABLE_PREFIX . "forum`.`edit_date` FROM `players`, `" . TABLE_PREFIX . "forum` WHERE `players`.`id` = `" . TABLE_PREFIX . "forum`.`author_guid` AND `" . TABLE_PREFIX . "forum`.`first_post` = ".(int) $thread_id." ORDER BY `" . TABLE_PREFIX . "forum`.`post_date` LIMIT ".$config['forum_posts_per_page']." OFFSET ".($_page * $config['forum_posts_per_page']))->fetchAll();
if(isset($posts[0]['player_id'])) {
	$db->query("UPDATE `" . TABLE_PREFIX . "forum` SET `views`=`views`+1 WHERE `id` = ".(int) $thread_id);
}

$lookaddons = $db->hasColumn('players', 'lookaddons');
$groups = new OTS_Groups_List();
foreach($posts as &$post)
{
	$post['player'] = new OTS_Player();
	$player = $post['player'];
	$player->load($post['player_id']);
	if(!$player->isLoaded()) {
		error('Forum error: Player not loaded.');
		die();
	}
	
	if($config['characters']['outfit']) {
		$post['outfit'] = $config['outfit_images_url'] . '?id=' . $player->getLookType() . ($lookaddons ? '&addons=' . $player->getLookAddons() : '') . '&head=' . $player->getLookHead() . '&body=' . $player->getLookBody() . '&legs=' . $player->getLookLegs() . '&feet=' . $player->getLookFeet();
	}

	$groupName = '';
	$group = $player->getGroup();
	if($group->isLoaded()) {
		$groupName = $group->getName();
	}
	
	$post['group'] = $groupName;
	$post['player_link'] = getPlayerLink($player->getName());

	$post['vocation'] = $player->getVocationName();

	$rank = $player->getRank();
	if($rank->isLoaded())
	{
		$guild = $rank->getGuild();
		if($guild->isLoaded())
			$post['guildRank'] = $rank->getName().' of <a href="'.getGuildLink($guild->getName(), false).'">'.$guild->getName().'</a>';
	}

	$player_account = $player->getAccount();
	$post['content'] = Forum::showPost(($post['post_html'] > 0 ? $post['post_topic'] : htmlspecialchars($post['post_topic'])), ($post['post_html'] > 0 ? $post['post_text'] : htmlspecialchars($post['post_text'])), $post['post_smile'] == 0, $post['post_html'] > 0);
	
	$query = $db->query("SELECT COUNT(`id`) AS 'posts' FROM `" . TABLE_PREFIX . "forum` WHERE `author_aid`=".(int) $player_account->getId())->fetch();
	$post['author_posts_count'] = (int)$query['posts'];

	if($post['edit_date'] > 0)
	{
		if($post['last_edit_aid'] != $post['author_aid']) {
			$post['edited_by'] = 'moderator';
		}
		else {
			$post['edited_by'] = $player->getName();
		}
	}
}

echo $twig->render('forum.show_thread.html.twig', array(
	'thread_id' => $thread_id,
	'posts' => $posts,
	'links_to_pages' => $links_to_pages,
	'author_link' => getPlayerLink($thread_starter['name']),
	'section' => array('id' => $posts[0]['section'], 'name' => $sections[$posts[0]['section']]['name']),
	'thread_starter' => $thread_starter,
	'is_moderator' => Forum::isModerator()
));

echo $twig->render('forum.fullscreen.html.twig');
