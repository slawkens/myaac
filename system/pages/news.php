<?php
/**
 * News
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

require_once(LIBS . 'forum.php');

if(isset($_GET['archive']))
{
	$title = 'News Archive';

	$categories = array();
	foreach($db->query(
		'SELECT id, name, icon_id FROM ' . TABLE_PREFIX . 'news_categories WHERE hidden != 1') as $cat)
	{
		$categories[$cat['id']] = array(
			'name' => $cat['name'],
			'icon_id' => $cat['icon_id']
		);
	}

	// display big news by id
	if(isset($_GET['id']))
	{
		$field_name = 'date';
		if($_REQUEST['id'] < 100000)
			$field_name = 'id';

		$news = $db->query('SELECT * FROM `'.TABLE_PREFIX . 'news` WHERE `hidden` != 1 AND `' . $field_name . '` = ' . (int)$_REQUEST['id']  . '');
		if($news->rowCount() == 1)
		{
			$news = $news->fetch();
			$author = '';
			$query = $db->query('SELECT `name` FROM `players` WHERE id = ' . $db->quote($news['player_id'] . ' LIMIT 1;'));
			if($query->rowCount() > 0) {
				$query = $query->fetch();
				$author = $query['name'];
			}
			
			$content_ = $news['body'];
			$firstLetter = '';
			if($content_[0] != '<')
			{
				$tmp = $template_path.'/images/letters/' . $content_[0] . '.gif';
				if(file_exists($tmp)) {
					$firstLetter = '<img src="' . $tmp . '" alt="' . $content_[0] . '" border="0" align="bottom">';
					$content_ = $firstLetter . substr($content_, 1);
				}
			}
			
			echo $twig->render('news.html.twig', array(
				'title' => stripslashes($news['title']),
				'content' => $content_,
				'date' => $news['date'],
				'icon' => $categories[$news['category']]['icon_id'],
				'author' => $config['news_author'] ? $author : '',
				'comments' => $news['comments'] != 0 ? getForumThreadLink($news['comments']) : null,
				'news_date_format' => $config['news_date_format']
			));
		}
		else
			echo "This news doesn't exist or is hidden.<br/>";

		echo $twig->render('news.back_button.html.twig');
		return;
	}
	?>
	
	<?php

	$newses = array();
	$news_DB = $db->query('SELECT * FROM '.$db->tableName(TABLE_PREFIX . 'news').' WHERE `type` = 1 AND `hidden` != 1 ORDER BY `date` DESC');
	foreach($news_DB as $news)
	{
		$newses[] = array(
			'link' => getLink('news') . '/archive/' . $news['id'],
			'icon_id' => $categories[$news['category']]['icon_id'],
			'title' => stripslashes($news['title']),
			'date' => $news['date']
		);
	}
	
	echo $twig->render('news.archive.html.twig', array(
		'newses' => $newses
	));
	
	return;
}

header('X-XSS-Protection: 0');
$title = 'Latest News';

$news_cached = false;
// some constants, used mainly by database (cannot by modified without schema changes)
define('TITLE_LIMIT', 100);
define('BODY_LIMIT', 65535); // maximum news body length
define('ARTICLE_TEXT_LIMIT', 300);
define('ARTICLE_IMAGE_LIMIT', 100);

$canEdit = hasFlag(FLAG_CONTENT_NEWS) || superAdmin();
if($canEdit)
{
	if(!empty($action))
	{
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
		$p_title = isset($_REQUEST['title']) ? $_REQUEST['title'] : null;
		$body = isset($_REQUEST['body']) ? stripslashes($_REQUEST['body']) : null;
		$comments = isset($_REQUEST['comments']) ? $_REQUEST['comments'] : null;
		$type = isset($_REQUEST['type']) ? (int)$_REQUEST['type'] : null;
		$category = isset($_REQUEST['category']) ? (int)$_REQUEST['category'] : null;
		$player_id = isset($_REQUEST['player_id']) ? (int)$_REQUEST['player_id'] : null;
		$article_text = isset($_REQUEST['article_text']) ? $_REQUEST['article_text'] : null;
		$article_image = isset($_REQUEST['article_image']) ? $_REQUEST['article_image'] : null;
		$forum_section = isset($_REQUEST['forum_section']) ? $_REQUEST['forum_section'] : null;
		$errors = array();

		if($action == 'add') {
			if(isset($forum_section) && $forum_section != '-1') {
				$forum_add = Forum::add_thread($p_title, $body, $forum_section, $player_id, $account_logged->getId(), $errors);
			}
			
			if(News::add($p_title, $body, $type, $category, $player_id, isset($forum_add) && $forum_add != 0 ? $forum_add : 0, $article_text, $article_image, $errors)) {
				$p_title = $body = $comments = $article_text = $article_image = '';
				$type = $category = $player_id = 0;
			}
		}
		else if($action == 'delete') {
			News::delete($id, $errors);
		}
		else if($action == 'edit')
		{
			if(isset($id) && !isset($p_title)) {
				$news = News::get($id);
				$p_title = $news['title'];
				$body = $news['body'];
				$comments = $news['comments'];
				$type = $news['type'];
				$category = $news['category'];
				$player_id = $news['player_id'];
				$article_text = $news['article_text'];
				$article_image = $news['article_image'];
			}
			else {
				if(News::update($id, $p_title, $body, $type, $category, $player_id, $forum_section, $article_text, $article_image, $errors)) {
					// update forum thread if exists
					if(isset($forum_section) && Validator::number($forum_section)) {
					$db->query("UPDATE `" . TABLE_PREFIX . "forum` SET `author_guid` = ".(int) $player_id.", `post_text` = ".$db->quote($body).", `post_topic` = ".$db->quote($p_title).", `edit_date` = " . time() . " WHERE `id` = " . $db->quote($forum_section));
					}
					
					$action = $p_title = $body = $comments = $article_text = $article_image = '';
					$type = $category = $player_id = 0;
				}
			}
		}
		else if($action == 'hide') {
			News::toggleHidden($id, $errors);
		}

		if(!empty($errors))
			echo $twig->render('error_box.html.twig', array('errors' => $errors));

		if($cache->enabled())
		{
			$cache->set('news_' . $template_name . '_' . NEWS, '', 120);
			$cache->set('news_' . $template_name . '_' . TICKER, '', 120);
		}
	}
}
else if($cache->enabled())
	$news_cached = News::getCached(NEWS);

if(!$news_cached)
{
	$categories = array();
	foreach($db->query(
		'SELECT `id`, `name`, `icon_id` FROM `' . TABLE_PREFIX . 'news_categories` WHERE `hidden` != 1') as $cat)
	{
		$categories[$cat['id']] = array(
			'name' => $cat['name'],
			'icon_id' => $cat['icon_id']
		);
	}

	$tickers_db =
		$db->query(
			'SELECT * FROM `' . TABLE_PREFIX . 'news` WHERE `type` = ' . TICKER .
			($canEdit ? '' : ' AND `hidden` != 1') .
			' ORDER BY `date` DESC LIMIT ' . $config['news_ticker_limit']);
	
	$tickers_content = '';
	if($tickers_db->rowCount() > 0)
	{
		$tickers = $tickers_db->fetchAll();
		foreach($tickers as &$ticker) {
			$ticker['icon'] = $categories[$ticker['category']]['icon_id'];
			$ticker['body_short'] = short_text(strip_tags($ticker['body']), 100);
		}
		
		$tickers_content = $twig->render('news.tickers.html.twig', array(
			'tickers' => $tickers,
			'canEdit' => $canEdit
		));
	}
	
	if($cache->enabled() && !$canEdit)
		$cache->set('news_' . $template_name . '_' . TICKER, $tickers_content, 120);
	
	$featured_article_db =
		$db->query(
			'SELECT `id`, `title`, `article_text`, `article_image`, `hidden` FROM `' . TABLE_PREFIX . 'news` WHERE `type` = ' . ARTICLE .
			($canEdit ? '' : ' AND `hidden` != 1') .
			' ORDER BY `date` DESC LIMIT 1');
	
	$article = '';
	if($featured_article_db->rowCount() > 0) {
		$article = $featured_article_db->fetch();
		
		$featured_article = '';
		if($twig->getLoader()->exists('news.featured_article.html.twig')) {
			$featured_article = $twig->render('news.featured_article.html.twig', array(
				'article' => array(
					'id' => $article['id'],
					'title' => $article['title'],
					'text' => $article['article_text'],
					'image' => $article['article_image'],
					'hidden' => $article['hidden'],
					'read_more'=> getLink('news/archive/') . $article['id']
				),
				'canEdit' => $canEdit
			));
		}
		
		if($cache->enabled() && !$canEdit)
			$cache->set('news_' . $template_name . '_' . ARTICLE, $featured_article, 120);
	}
}
else {
	$tickers_content = News::getCached(TICKER);
	$featured_article = News::getCached(ARTICLE);
}

if(!$news_cached)
{
	ob_start();
	if($canEdit)
	{
		if($action == 'edit') {
			$player = new OTS_Player();
			$player->load($player_id);
		}
		
		$account_players = $account_logged->getPlayersList();
		$account_players->orderBy('group_id', POT::ORDER_DESC);

		echo $twig->render('news.add.html.twig', array(
			'action' => $action,
			'news_link' => getLink(PAGE),
			'news_link_form' => getLink('news/' . ($action == 'edit' ? 'edit' : 'add')),
			'news_id' => isset($id) ? $id : null,
			'title' => isset($p_title) ? $p_title : '',
			'body' => isset($body) ? $body : '',
			'type' => isset($type) ? $type : null,
			'player' => isset($player) && $player->isLoaded() ? $player : null,
			'player_id' => isset($player_id) ? $player_id : null,
			'account_players' => $account_players,
			'category' => isset($category) ? $category : 0,
			'categories' => $categories,
			'forum_boards' => getForumBoards(),
			'forum_section' => isset($forum_section) ? $forum_section : null,
			'comments' => isset($comments) ? $comments : null,
			'article_text' => isset($article_text) ? $article_text : null,
			'article_image' => isset($article_image) ? $article_image : null
		));
	}

	$newses =
		$db->query(
			'SELECT * FROM '.$db->tableName(TABLE_PREFIX . 'news').
			' WHERE type = ' . NEWS .
			($canEdit ? '' : ' AND hidden != 1') .
			' ORDER BY date' .
			' DESC LIMIT ' . $config['news_limit']);
	if($newses->rowCount() > 0)
	{
		foreach($newses as $news)
		{
			$author = '';
			$query = $db->query('SELECT `name` FROM `players` WHERE id = ' . $db->quote($news['player_id'] . ' LIMIT 1'));
			if($query->rowCount() > 0) {
				$query = $query->fetch();
				$author = $query['name'];
			}

			$admin_options = '';
			if($canEdit)
			{
				$admin_options = '<br/><br/><a href="?subtopic=news&action=edit&id=' . $news['id'] . '" title="Edit">
					<img src="images/edit.png"/>Edit
				</a>
				<a id="delete" href="?subtopic=news&action=delete&id=' . $news['id'] . '" onclick="return confirm(\'Are you sure?\');" title="Delete">
					<img src="images/del.png"/>Delete
				</a>
				<a href="?subtopic=news&action=hide&id=' . $news['id'] . '" title="' . ($news['hidden'] != 1 ? 'Hide' : 'Show') . '">
					<img src="images/' . ($news['hidden'] != 1 ? 'success' : 'error') . '.png"/>
					' . ($news['hidden'] != 1 ? 'Hide' : 'Show') . '
				</a>';
			}
			
			$content_ = $news['body'];
			$firstLetter = '';
			if($content_[0] != '<')
			{
				$tmp = $template_path.'/images/letters/' . $content_[0] . '.gif';
				if(file_exists($tmp)) {
					$firstLetter = '<img src="' . $tmp . '" alt="' . $content_[0] . '" border="0" align="bottom">';
					$content_ = $firstLetter . substr($content_, 1);
				}
			}
			
			echo $twig->render('news.html.twig', array(
				'id' => $news['id'],
				'title' => stripslashes($news['title']),
				'content' => $content_ . $admin_options,
				'date' => $news['date'],
				'icon' => $categories[$news['category']]['icon_id'],
				'author' => $config['news_author'] ? $author : '',
				'comments' => $news['comments'] != 0 ? getForumThreadLink($news['comments']) : null,
				'news_date_format' => $config['news_date_format'],
				'hidden'=> $news['hidden']
			));
		}
	}

	$tmp_content = ob_get_contents();
	ob_end_clean();

	if($cache->enabled() && !$canEdit)
		$cache->set('news_' . $template_name . '_' . NEWS, $tmp_content, 120);

	echo $tmp_content;
}
else
	echo $news_cached;

class News
{
	static public function verify($title, $body, $article_text, $article_image, &$errors)
	{
		if(!isset($title[0]) || !isset($body[0])) {
			$errors[] = 'Please fill all inputs.';
			return false;
		}

		if(strlen($title) > TITLE_LIMIT) {
			$errors[] = 'News title cannot be longer than ' . TITLE_LIMIT . ' characters.';
			return false;
		}
	
		if(strlen($body) > BODY_LIMIT) {
			$errors[] = 'News content cannot be longer than ' . BODY_LIMIT . ' characters.';
			return false;
		}
		
		if(strlen($article_text) > ARTICLE_TEXT_LIMIT) {
			$errors[] = 'Article text cannot be longer than ' . ARTICLE_TEXT_LIMIT . ' characters.';
			return false;
		}
		
		if(strlen($article_image) > ARTICLE_IMAGE_LIMIT) {
			$errors[] = 'Article image cannot be longer than ' . ARTICLE_IMAGE_LIMIT . ' characters.';
			return false;
		}
		
		return true;
	}

	static public function add($title, $body, $type, $category, $player_id, $comments, $article_text, $article_image, &$errors)
	{
		global $db;
		if(!self::verify($title, $body, $article_text, $article_image, $errors))
			return false;

		$db->insert(TABLE_PREFIX . 'news', array('title' => $title, 'body' => $body, 'type' => $type, 'date' => time(), 'category' => $category, 'player_id' => isset($player_id) ? $player_id : 0, 'comments' => $comments, 'article_text' => ($type == 3 ? $article_text : ''), 'article_image' => ($type == 3 ? $article_image : '')));
		return true;
	}

	static public function get($id) {
		global $db;
		return $db->select(TABLE_PREFIX . 'news', array('id' => $id));
	}

	static public function update($id, $title, $body, $type, $category, $player_id, $comments, $article_text, $article_image, &$errors)
	{
		global $db;
		if(!self::verify($title, $body, $article_text, $article_image, $errors))
			return false;

		$db->update(TABLE_PREFIX . 'news', array('title' => $title, 'body' => $body, 'type' => $type, 'category' => $category, 'last_modified_by' => isset($player_id) ? $player_id : 0, 'last_modified_date' => time(), 'comments' => $comments, 'article_text' => $article_text, 'article_image' => $article_image), array('id' => $id));
		return true;
	}

	static public function delete($id, &$errors)
	{
		global $db;
		if(isset($id))
		{
			if($db->select(TABLE_PREFIX . 'news', array('id' => $id)) !== false)
				$db->delete(TABLE_PREFIX . 'news', array('id' => $id));
			else
				$errors[] = 'News with id ' . $id . ' does not exists.';
		}
		else
			$errors[] = 'News id not set.';

		return !count($errors);
	}

	static public function toggleHidden($id, &$errors)
	{
		global $db;
		if(isset($id))
		{
			$query = $db->select(TABLE_PREFIX . 'news', array('id' => $id));
			if($query !== false)
				$db->update(TABLE_PREFIX . 'news', array('hidden' => ($query['hidden'] == 1 ? 0 : 1)), array('id' => $id));
			else
				$errors[] = 'News with id ' . $id . ' does not exists.';
		}
		else
			$errors[] = 'News id not set.';

		return !count($errors);
	}

	static public function getCached($type)
	{
		global $cache, $template_name;
		if($cache->enabled())
		{
			$tmp = '';
			if($cache->fetch('news_' . $template_name . '_' . $type, $tmp) && isset($tmp[0])) {
				return $tmp;
			}
		}

		return false;
	}
}
?>
