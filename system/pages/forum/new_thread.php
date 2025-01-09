<?php
/**
 * New forum thread
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
	if(isset($_GET['section_id'])) {
		$extra_url = '?action=new_thread&section_id=' . $_GET['section_id'];
	}

	echo 'You are not logged in. <a href="' . getLink('account/manage') . '?redirect=' . urlencode(getLink('forum') . $extra_url) . '">Log in</a> to post on the forum.<br /><br />';
	return;
}

if(Forum::canPost($account_logged)) {
	$players_from_account = $db->query('SELECT `players`.`name`, `players`.`id` FROM `players` WHERE `players`.`account_id` = '.(int) $account_logged->getId())->fetchAll();
	$section_id = $_REQUEST['section_id'] ?? null;
	if($section_id !== null) {
		echo '<a href="' . getLink('forum') . '">Boards</a> >> <a href="' . getForumBoardLink($section_id) . '">' . $sections[$section_id]['name'] . '</a> >> <b>Post new thread</b><br />';

		if(isset($sections[$section_id]['name']) && Forum::hasAccess($section_id)) {
			if ($sections[$section_id]['closed'] && !Forum::isModerator())
				$errors[] = 'You cannot create topic on this board.';

			$quote = (int)(isset($_REQUEST['quote']) ? $_REQUEST['quote'] : 0);
			$text = isset($_REQUEST['text']) ? stripslashes($_REQUEST['text']) : '';
			$char_id = (int)(isset($_REQUEST['char_id']) ? $_REQUEST['char_id'] : 0);
			$post_topic = isset($_REQUEST['topic']) ? stripslashes($_REQUEST['topic']) : '';
			$smile = (isset($_REQUEST['smile']) ? (int)$_REQUEST['smile'] : 0);
			$html = (isset($_REQUEST['html']) ? (int)$_REQUEST['html'] : 0);

			if (!superAdmin()) {
				$html = 0;
			}

			$saved = false;
			if (isset($_REQUEST['save'])) {
				$length = strlen($post_topic);
				if ($length < 1 || $length > 60) {
					$errors[] = "Too short or too long topic (Length: $length letters). Minimum 1 letter, maximum 60 letters.";
				}

				$length = strlen($text);
				if ($length < 1 || $length > 15000) {
					$errors[] = "Too short or too long post (Length: $length letters). Minimum 1 letter, maximum 15000 letters.";
				}

				if ($char_id == 0) {
					$errors[] = 'Please select a character.';
				}

				$player_on_account = false;

				if (count($errors) == 0) {
					foreach ($players_from_account as $player) {
						if ($char_id == $player['id']) {
							$player_on_account = true;
						}
					}

					if (!$player_on_account) {
						$errors[] = "Player with selected ID $char_id doesn't exist or isn't on your account";
					}
				}

				if (count($errors) == 0) {
					$last_post = 0;
					$query = $db->query('SELECT `post_date` FROM `' . FORUM_TABLE_PREFIX . 'forum` ORDER BY `post_date` DESC LIMIT 1');

					if ($query->rowCount() > 0) {
						$query = $query->fetch();
						$last_post = $query['post_date'];
					}

					if ($last_post + setting('core.forum_post_interval') - time() > 0 && !Forum::isModerator())
						$errors[] = 'You can post one time per ' . setting('core.forum_post_interval') . ' seconds. Next post after ' . ($last_post + setting('core.forum_post_interval') - time()) . ' second(s).';
				}

				if (count($errors) == 0) {
					$saved = true;

					$db->insert(FORUM_TABLE_PREFIX . 'forum', [
						'first_post' => 0,
						'last_post' => time(),
						'section' => $section_id,
						'replies' => 0,
						'views' => 0,
						'author_aid' => $account_logged->getId(),
						'author_guid' => $char_id,
						'post_text' => $text,
						'post_topic' => $post_topic,
						'post_smile' => $smile,
						'post_html' => $html,
						'post_date' => time(),
						'last_edit_aid' => 0,
						'edit_date' => 0,
						'post_ip' => get_browser_real_ip(),
					]);

					$thread_id = $db->lastInsertId();

					$db->query("UPDATE `" . FORUM_TABLE_PREFIX . "forum` SET `first_post`=" . (int)$thread_id . " WHERE `id` = " . (int)$thread_id);
					header('Location: ' . getForumThreadLink($thread_id));

					echo '<br />Thank you for posting.<br /><a href="' . getForumThreadLink($thread_id) . '">GO BACK TO LAST THREAD</a>';
				}
			}

			if (!$saved) {
				if (!empty($errors)) {
					$twig->display('error_box.html.twig', array('errors' => $errors));
				}

				$twig->display('forum.new_thread.html.twig', array(
					'section_id' => $section_id,
					'players' => $players_from_account,
					'post_player_id' => $char_id,
					'post_thread' => $post_topic,
					'post_text' => $text,
					'post_smile' => $smile > 0,
					'post_html' => $html > 0,
					'canEdit' => $canEdit
				));
			}
		}
		else {
			$errors[] = "Board with ID $section_id doesn't exist.";
			displayErrorBoxWithBackButton($errors, getLink('forum'));
		}
	}
	else {
		$errors[] = 'Please enter section_id.';
		displayErrorBoxWithBackButton($errors, getLink('forum'));
	}
}
else {
	$errors[] = 'Your account is banned, deleted or you don\'t have any player with level '.setting('core.forum_level_required').' on your account. You can\'t post.';
	displayErrorBoxWithBackButton($errors, getLink('forum'));
}
