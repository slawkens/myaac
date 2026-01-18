<?php
/**
 * Show forum board
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

$links_to_pages = '';
$section_id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : null;

if($section_id == null || !isset($sections[$section_id])) {
	$errors[] = "Board with this id doesn't exist.";
	displayErrorBoxWithBackButton($errors, getLink('forum'));
	return;
}

if(!Forum::hasAccess($section_id)) {
	$errors[] = "You don't have access to this board.";
	displayErrorBoxWithBackButton($errors, getLink('forum'));
	return;
}

$_page = (int) ($_REQUEST['page'] ?? 0);
$threads_count = $db->query("SELECT COUNT(`" . FORUM_TABLE_PREFIX . "forum`.`id`) AS threads_count FROM `players`, `" . FORUM_TABLE_PREFIX . "forum` WHERE `players`.`id` = `" . FORUM_TABLE_PREFIX . "forum`.`author_guid` AND `" . FORUM_TABLE_PREFIX . "forum`.`section` = ".(int) $section_id." AND `" . FORUM_TABLE_PREFIX . "forum`.`first_post` = `" . FORUM_TABLE_PREFIX . "forum`.`id`")->fetch();
for($i = 0; $i < $threads_count['threads_count'] / setting('core.forum_threads_per_page'); $i++) {
	if($i != $_page)
		$links_to_pages .= '<a href="' . getForumBoardLink($section_id, $i) . '">'.($i + 1).'</a> ';
	else
		$links_to_pages .= '<b>'.($i + 1).' </b>';
}

$last_threads = $db->query("SELECT `players`.`id` as `player_id`, `players`.`name`, `" . FORUM_TABLE_PREFIX . "forum`.`first_post`, `" . FORUM_TABLE_PREFIX . "forum`.`post_text`, `" . FORUM_TABLE_PREFIX . "forum`.`post_topic`, `" . FORUM_TABLE_PREFIX . "forum`.`id`, `" . FORUM_TABLE_PREFIX . "forum`.`last_post`, `" . FORUM_TABLE_PREFIX . "forum`.`replies`, `" . FORUM_TABLE_PREFIX . "forum`.`views`, `" . FORUM_TABLE_PREFIX . "forum`.`post_date` FROM `players`, `" . FORUM_TABLE_PREFIX . "forum` WHERE `players`.`id` = `" . FORUM_TABLE_PREFIX . "forum`.`author_guid` AND `" . FORUM_TABLE_PREFIX . "forum`.`section` = ".$section_id." AND `" . FORUM_TABLE_PREFIX . "forum`.`first_post` = `" . FORUM_TABLE_PREFIX . "forum`.`id` ORDER BY `" . FORUM_TABLE_PREFIX . "forum`.`last_post` DESC LIMIT ".setting('core.forum_threads_per_page')." OFFSET ".($_page * setting('core.forum_threads_per_page')))->fetchAll(PDO::FETCH_ASSOC);

$threads = [];
if(count($last_threads) > 0) {
	$player = new OTS_Player();
	foreach($last_threads as $thread) {
		$player->load($thread['player_id']);
		if(!$player->isLoaded()) {
			throw new RuntimeException('Forum error: Player not loaded.');
		}

		$player_account = $player->getAccount();
		$canEditForum = $player_account->hasFlag(FLAG_CONTENT_FORUM) || $player_account->isAdmin();

		$thread['link'] = getForumThreadLink($thread['id']);

		$thread['post_shortened'] = ($canEditForum ? substr(strip_tags($thread['post_text']), 0, 50) : htmlspecialchars(substr($thread['post_text'], 0, 50)));

		$thread['player'] = $player;
		$thread['player_link'] = getPlayerLink($thread['name']);

		if($thread['last_post'] > 0) {
			$last_post = $db->query("SELECT `players`.`name`, `" . FORUM_TABLE_PREFIX . "forum`.`post_date` FROM `players`, `" . FORUM_TABLE_PREFIX . "forum` WHERE `" . FORUM_TABLE_PREFIX . "forum`.`first_post` = ".(int) $thread['id']." AND `players`.`id` = `" . FORUM_TABLE_PREFIX . "forum`.`author_guid` ORDER BY `post_date` DESC LIMIT 1")->fetch();

			$last_post['player_link'] = getPlayerLink($last_post['name']);
			$thread['latest_post'] = $last_post;
		}

		$threads[] = $thread;
	}
}

$twig->display('forum.show_board.html.twig', [
	'threads' => $threads,
	'section_id' => $section_id,
	'section_name' => $sections[$section_id]['name'],
	'links_to_pages' => $links_to_pages,
	'is_moderator' => Forum::isModerator(),
	'closed' => $sections[$section_id]['closed'],
]);
