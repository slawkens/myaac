<?php
/**
 * New forum post
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
	$extra_url = '';
	if(isset($_GET['thread_id'])) {
		$extra_url = '?action=new_post&thread_id=' . $_GET['thread_id'];
	}

	echo 'You are not logged in. <a href="' . getLink('account/manage') . '?redirect=' . urlencode(getLink('forum') . $extra_url) . '">Log in</a> to post on the forum.<br /><br />';
	return;
}

csrfProtect();

if(Forum::canPost($account_logged)) {
	$players_from_account = $db->query("SELECT `players`.`name`, `players`.`id` FROM `players` WHERE `players`.`account_id` = ".(int) $account_logged->getId())->fetchAll();
	$thread_id = isset($_REQUEST['thread_id']) ? (int) $_REQUEST['thread_id'] : 0;
	if($thread_id == 0) {
		$errors[] = "Thread with this id doesn't exist.";
		displayErrorBoxWithBackButton($errors, getLink('forum'));
		return;
	}

	$thread = $db->query("SELECT `" . FORUM_TABLE_PREFIX . "forum`.`post_topic`, `" . FORUM_TABLE_PREFIX . "forum`.`id`, `" . FORUM_TABLE_PREFIX . "forum`.`section` FROM `" . FORUM_TABLE_PREFIX . "forum` WHERE `" . FORUM_TABLE_PREFIX . "forum`.`id` = ".(int) $thread_id." AND `" . FORUM_TABLE_PREFIX . "forum`.`first_post` = ".$thread_id." LIMIT 1")->fetch();

	if(isset($thread['id']) && Forum::hasAccess($thread['section'])) {
		echo '<a href="' . getLink('forum') . '">Boards</a> >> <a href="' . getForumBoardLink($thread['section']) . '">'.$sections[$thread['section']]['name'].'</a> >> <a href="' . getForumThreadLink($thread_id) . '">'.htmlspecialchars($thread['post_topic']).'</a> >> <b>Post new reply</b><br /><h3>'.htmlspecialchars($thread['post_topic']).'</h3>';

		$quote = isset($_REQUEST['quote']) ? (int) $_REQUEST['quote'] : NULL;
		$text = isset($_POST['text']) ? stripslashes(trim($_POST['text'])) : NULL;
		$char_id = (int) ($_POST['char_id'] ?? 0);
		$post_topic = isset($_POST['topic']) ? stripslashes(trim($_POST['topic'])) : '';
		$smile = (int)($_POST['smile'] ?? 0);
		$html = (int)($_POST['html'] ?? 0);
		$saved = false;

		if (!superAdmin()) {
			$html = 0;
		}

		if(isset($_REQUEST['quote'])) {
			$quoted_post = $db->query("SELECT `players`.`name`, `" . FORUM_TABLE_PREFIX . "forum`.`post_text`, `" . FORUM_TABLE_PREFIX . "forum`.`post_date` FROM `players`, `" . FORUM_TABLE_PREFIX . "forum` WHERE `players`.`id` = `" . FORUM_TABLE_PREFIX . "forum`.`author_guid` AND `" . FORUM_TABLE_PREFIX . "forum`.`id` = ".(int) $quote)->fetchAll();
			if(isset($quoted_post[0]['name'])) {
				$text = '[i]Originally posted by ' . $quoted_post[0]['name'] . ' on ' . date('d.m.y H:i:s', $quoted_post[0]['post_date']) . ':[/i][quote]' . $quoted_post[0]['post_text'] . '[/quote]';
			}
		}
		elseif(isset($_POST['save'])) {
			$length = strlen($text);
			if($length < 1 || strlen($text) > 15000) {
				$errors[] = "Too short or too long post (Length: $length letters). Minimum 1 letter, maximum 15000 letters.";
			}

			if($char_id == 0) {
				$errors[] = 'Please select a character.';
			}

			$player_on_account = false;
			if(count($errors) == 0) {
				foreach($players_from_account as $player) {
					if ($char_id == $player['id']) {
						$player_on_account = true;
					}
				}

				if(!$player_on_account) {
					$errors[] = "Player with selected ID $char_id doesn't exist or isn't on your account";
				}
			}

			if(count($errors) == 0) {
				$last_post = 0;
				$query = $db->query('SELECT post_date FROM ' . FORUM_TABLE_PREFIX . 'forum ORDER BY post_date DESC LIMIT 1');
				if($query->rowCount() > 0) {
					$query = $query->fetch();
					$last_post = $query['post_date'];
				}
				if($last_post+setting('core.forum_post_interval')-time() > 0 && !Forum::isModerator())
					$errors[] = 'You can post one time per ' . setting('core.forum_post_interval') . ' seconds. Next post after '.($last_post + setting('core.forum_post_interval')-time()).' second(s).';
			}

			if(count($errors) == 0) {
				$saved = true;
				Forum::add_post($thread['id'], $thread['section'], $account_logged->getId(), $char_id, $text, $post_topic, $smile, $html);
				$db->query("UPDATE `" . FORUM_TABLE_PREFIX . "forum` SET `replies`=`replies`+1, `last_post`=".time()." WHERE `id` = ".$thread_id);
				$post_page = $db->query("SELECT COUNT(`" . FORUM_TABLE_PREFIX . "forum`.`id`) AS posts_count FROM `players`, `" . FORUM_TABLE_PREFIX . "forum` WHERE `players`.`id` = `" . FORUM_TABLE_PREFIX . "forum`.`author_guid` AND `" . FORUM_TABLE_PREFIX . "forum`.`post_date` <= ".time()." AND `" . FORUM_TABLE_PREFIX . "forum`.`first_post` = ".(int) $thread['id'])->fetch();
				$_page = (int) ceil($post_page['posts_count'] / setting('core.forum_threads_per_page')) - 1;
				header('Location: ' . getForumThreadLink($thread_id, $_page));
				echo '<br />Thank you for posting.<br /><a href="' . getForumThreadLink($thread_id, $_page) . '">GO BACK TO LAST THREAD</a>';
			}
		}

		if(!$saved) {
			if(!empty($errors)) {
				$twig->display('error_box.html.twig', array('errors' => $errors));
			}

			$threads = $db->query("SELECT `players`.`name`, `" . FORUM_TABLE_PREFIX . "forum`.`post_text`, `" . FORUM_TABLE_PREFIX . "forum`.`post_topic`, `" . FORUM_TABLE_PREFIX . "forum`.`post_smile`, `" . FORUM_TABLE_PREFIX . "forum`.`post_html`, `" . FORUM_TABLE_PREFIX . "forum`.`author_aid` FROM `players`, `" . FORUM_TABLE_PREFIX . "forum` WHERE `players`.`id` = `" . FORUM_TABLE_PREFIX . "forum`.`author_guid` AND `" . FORUM_TABLE_PREFIX . "forum`.`first_post` = ".(int) $thread_id." ORDER BY `" . FORUM_TABLE_PREFIX . "forum`.`post_date` DESC LIMIT 5")->fetchAll();

			foreach($threads as &$thread) {
				$player_account = new OTS_Account();
				$player_account->load($thread['author_aid']);
				if($player_account->isLoaded()) {
					$thread['post'] = Forum::showPost(($thread['post_html'] > 0 ? $thread['post_topic'] : htmlspecialchars($thread['post_topic'])), ($thread['post_html'] > 0 ? $thread['post_text'] : htmlspecialchars($thread['post_text'])), $thread['post_smile'] == 0, $thread['post_html'] > 0);
				}
			}

			$twig->display('forum.new_post.html.twig', array(
				'thread_id' => $thread_id,
				'post_player_id' => $char_id,
				'players' => $players_from_account,
				'post_topic' => $post_topic,
				'post_text' => $text,
				'post_smile' => $smile > 0,
				'post_html' => $html > 0,
				'topic' => $thread['post_topic'],
				'threads' => $threads,
				'canEdit' => $canEdit
			));
		}
	}
	else {
		$errors[] = "Thread with ID " . $thread_id . " doesn't exist.";
		displayErrorBoxWithBackButton($errors, getLink('forum'));
	}
}
else {
	$errors[] = "Your account is banned, deleted or you don't have any player with level " . setting('core.forum_level_required') . " on your account. You can't post.";
	displayErrorBoxWithBackButton($errors, getLink('forum'));
}

$twig->display('forum.fullscreen.html.twig');
