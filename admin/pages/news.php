<?php
/**
 * Pages
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Forum;
use MyAAC\News;

defined('MYAAC') or die('Direct access not allowed!');

$title = 'News Panel';

csrfProtect();

$use_datatable = true;

if (!hasFlag(FLAG_CONTENT_PAGES) && !superAdmin()) {
	echo 'Access denied.';
	return;
}

header('X-XSS-Protection:0');

// some constants, used mainly by database (cannot be modified without schema changes)
const NEWS_TITLE_LIMIT = 100;
const NEWS_BODY_LIMIT = 65535; // maximum news body length
const ARTICLE_TEXT_LIMIT = 300;
const ARTICLE_IMAGE_LIMIT = 100;

$name = $p_title = '';
if(!empty($action))
{
	$id = $_POST['id'] ?? null;
	$p_title = $_POST['title'] ?? null;
	$body = isset($_POST['body']) ? stripslashes($_POST['body']) : null;
	$comments = $_POST['comments'] ?? null;
	$type = isset($_REQUEST['type']) ? (int)$_REQUEST['type'] : 1;
	$category = isset($_POST['category']) ? (int)$_POST['category'] : null;
	$player_id = isset($_POST['player_id']) ? (int)$_POST['player_id'] : null;
	$article_text = $_POST['article_text'] ?? null;
	$article_image = $_POST['article_image'] ?? null;
	$forum_section = $_POST['forum_section'] ?? null;
	$errors = [];

	if (isRequestMethod('post')) {
		if ($action == 'new') {
			if (isset($forum_section) && $forum_section != '-1') {
				$forum_add = Forum::add_thread($p_title, $body, $forum_section, $player_id, $account_logged->getId(), $errors);
			}

			if (isset($p_title) && News::add($p_title, $body, $type, $category, $player_id, isset($forum_add) && $forum_add != 0 ? $forum_add : 0, $article_text, $article_image, $errors)) {
				$p_title = $body = $comments = $article_text = $article_image = '';
				$type = $category = $player_id = 0;

				success('Added successful.');
			}
		} else if ($action == 'delete') {
			if (News::delete($id, $errors)) {
				success('Deleted successful.');
			}
		} else if ($action == 'edit') {
			if (isset($id) && !isset($p_title)) {
				$news = News::get($id);
				$p_title = $news['title'];
				$body = $news['body'];
				$comments = $news['comments'];
				$type = $news['type'];
				$category = $news['category'];
				$player_id = $news['player_id'];
				$article_text = $news['article_text'];
				$article_image = $news['article_image'];
			} else {
				if (News::update($id, $p_title, $body, $type, $category, $player_id, $forum_section, $article_text, $article_image, $errors)) {
					// update forum thread if exists
					if (isset($forum_section) && Validator::number($forum_section)) {
						$db->query("UPDATE `" . TABLE_PREFIX . "forum` SET `author_guid` = " . (int)$player_id . ", `post_text` = " . $db->quote($body) . ", `post_topic` = " . $db->quote($p_title) . ", `edit_date` = " . time() . " WHERE `id` = " . $db->quote($forum_section));
					}

					$action = $p_title = $body = $comments = $article_text = $article_image = '';
					$type = $category = $player_id = 0;

					success('Updated successful.');
				}
			}
		} else if ($action == 'hide') {
			if (News::toggleHide($id, $errors, $status)) {
				success(($status == 1 ? 'Hide' : 'Show') . ' successful.');
			}
		}
	}

	if(!empty($errors))
		error(implode(", ", $errors));
}

$categories = array();
foreach($db->query('SELECT `id`, `name`, `icon_id` FROM `' . TABLE_PREFIX . 'news_categories` WHERE `hide` != 1') as $cat)
{
	$categories[$cat['id']] = array(
		'name' => $cat['name'],
		'icon_id' => $cat['icon_id']
	);
}

if($action == 'edit' || $action == 'new') {
	if($action == 'edit') {
		$player = new OTS_Player();
		$player->load($player_id);
	}

	$account_players = $account_logged->getPlayersList();
	$account_players->orderBy('group_id', POT::ORDER_DESC);
	$twig->display('admin.news.form.html.twig', array(
		'action' => $action,
		'news_id' => $id ?? null,
		'title' => $p_title ?? '',
		'body' => isset($body) ? escapeHtml($body) : '',
		'type' => $type,
		'player' => isset($player) && $player->isLoaded() ? $player : null,
		'player_id' => $player_id ?? null,
		'account_players' => $account_players,
		'category' => $category ?? 0,
		'categories' => $categories,
		'forum_boards' => getForumBoards(),
		'forum_section' => $forum_section ?? null,
		'comments' => $comments ?? null,
		'article_text' => $article_text ?? null,
		'article_image' => $article_image ?? null
	));
}

$query = $db->query('SELECT * FROM ' . $db->tableName(TABLE_PREFIX . 'news'));
$newses = array();

$cachePlayers = [];
foreach ($query as $_news) {
	$playerId = $_news['player_id'];
	if (isset($cachePlayers[$playerId])) {
		$_player = $cachePlayers[$playerId];
	}
	else {
		$_player = new OTS_Player();
		$_player->load($playerId);
		$cachePlayers[$playerId] = $_player;
	}

	$newses[$_news['type']][] = array(
		'id' => $_news['id'],
		'hide' => $_news['hide'],
		'archive_link' => getLink('news') . '/archive/' . $_news['id'],
		'title' => $_news['title'],
		'date' => $_news['date'],
		'player_name' => $_player->isLoaded() ? $_player->getName() : '',
		'player_link' => $_player->isLoaded() ? getPlayerLink($_player, false) : '',
	);
}

$twig->display('admin.news.html.twig', array(
	'newses' => $newses
));
