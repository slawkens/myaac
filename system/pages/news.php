<?php
/**
 * News
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.4
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

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

		$news = $db->query('SELECT * FROM '.$db->tableName(TABLE_PREFIX . 'news').' WHERE `type` = 1 AND `hidden` != 1 AND `' . $field_name . '` = ' . (int)$_REQUEST['id']  . '');
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
	?>
	<center>
	<table cellspacing="0" cellpadding="0" border="0"><form method="post" action="<?php echo getLink('news/archive'); ?>"><tbody><tr><td>
		<input width="120" height="18" border="0" type="image" src="<?php echo $template_path; ?>/images/global/buttons/sbutton_back.gif" alt="Back" name="Back">
	</form></td></tr></tbody></table>
	</center>
	<?php
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

$title = 'Latest News';

$news_cached = false;
// some constants, used mainly by database (cannot by modified without schema changes)
define('TITLE_LIMIT', 100);
define('BODY_LIMIT', 65535); // maximum news body length

$canEdit = hasFlag(FLAG_CONTENT_NEWS) || superAdmin();
if($canEdit)
{
	if(!empty($action))
	{
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : NULL;
		$p_title = isset($_REQUEST['title']) ? $_REQUEST['title'] : NULL;
		$body = isset($_REQUEST['body']) ? stripslashes($_REQUEST['body']) : NULL;
		$comments = isset($_REQUEST['comments']) ? $_REQUEST['comments'] : NULL;
		$type = isset($_REQUEST['type']) ? (int)$_REQUEST['type'] : NULL;
		$category = isset($_REQUEST['category']) ? (int)$_REQUEST['category'] : NULL;
		$player_id = isset($_REQUEST['player_id']) ? (int)$_REQUEST['player_id'] : NULL;

		$forum_section = isset($_REQUEST['forum_section']) ? $_REQUEST['forum_section'] : NULL;
		$errors = array();

		if($action == 'add') {
			if(isset($forum_section) && $forum_section != '-1') {
				$forum_add = Forum::add($p_title, $body, $forum_section, $player_id, $account_logged->getId(), $errors);
			}
			
			if(News::add($p_title, $body, $type, $category, $player_id, isset($forum_add) && $forum_add != 0 ? $forum_add : 0, $errors)) {
				$p_title = $body = $comments = '';
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
			}
			else {
				if(News::update($id, $p_title, $body, $type, $category, $player_id, $comments, $errors)) {
					$action = $p_title = $body = $comments = '';
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
			$cache->set('news_' . $template_name . '_' . TICKET, '', 120);
		}
	}
}
else if($cache->enabled())
	$news_cached = News::getCached(NEWS);

if(!$news_cached)
{
	$categories = array();
	foreach($db->query(
		'SELECT id, name, icon_id FROM ' . TABLE_PREFIX . 'news_categories WHERE hidden != 1') as $cat)
	{
		$categories[$cat['id']] = array(
			'name' => $cat['name'],
			'icon_id' => $cat['icon_id']
		);
	}

	$tickers =
		$db->query(
			'SELECT * FROM ' . $db->tableName(TABLE_PREFIX . 'news') . ' WHERE ' . $db->fieldName('type') . ' = ' . TICKET .
			($canEdit ? '' : ' AND ' . $db->fieldName('hidden') . ' != 1') .
			' ORDER BY ' . $db->fieldName('date') . ' DESC' .
			' LIMIT ' . $config['news_ticker_limit']);

	if($tickers->rowCount() > 0)
	{
		$rows = 0;
		$tickers_to_add = '';
		foreach($tickers as $news)
		{
			$admin_options = '';
			if($canEdit)
			{
				$admin_options = '<a href="?subtopic=news&action=edit&id=' . $news['id'] . '" title="Edit">
					<img src="images/edit.png"/>
					Edit
				</a>
				<a id="delete" href="' . BASE_URL . '?subtopic=news&action=delete&id=' . $news['id'] . '" onclick="return confirm(\'Are you sure?\');" title="Delete">
					<img src="images/del.png"/>
					Delete
				</a>
				<a href="?subtopic=news&action=hide&id=' . $news['id'] . '" title="' . ($news['hidden'] != 1 ? 'Hide' : 'Show') . '">
					<img src="images/' . ($news['hidden'] != 1 ? 'success' : 'error') . '.png"/>
					' . ($news['hidden'] != 1 ? 'Hide' : 'Show') . '
				</a>';
			}
			$tickers_to_add .= '<div id="TickerEntry-'.$rows.'" class="Row" onclick=\'TickerAction("TickerEntry-'.$rows.'")\'>
			  <div class="' . (is_int($rows / 2) ? "Odd" : "Even") . '">
				<div class="NewsTickerIcon" style="background-image: url('.$template_path.'/images/news/icon_'.$categories[$news['category']]['icon_id'].'_small.gif);"></div>
				<div id="TickerEntry-'.$rows.'-Button" class="NewsTickerExtend" style="background-image: url('.$template_path.'/images/general/plus.gif);"></div>
				<div class="NewsTickerText">
				  <span class="NewsTickerDate">'.date("j M Y", $news['date']).' -</span>
				  <div id="TickerEntry-'.$rows.'-ShortText" class="NewsTickerShortText">';
			//if admin show button to delete (hide) ticker
			$tickers_to_add .= short_text(strip_tags($news['body']), 100).'</div>
				  <div id="TickerEntry-'.$rows.'-FullText" class="NewsTickerFullText">';
			//if admin show button to delete (hide) ticker
			$tickers_to_add .= $news['body'] . $admin_options . '</div>
				</div>
			  </div>
			</div>';
			$rows++;
		}
	}
}
else
	$tickers_to_add = News::getCached(TICKET);

if(isset($tickers_to_add[0]))
{
	//show table with tickers
	$news_content = '<div id="newsticker" class="Box">
		<div class="Corner-tl" style="background-image: url('.$template_path.'/images/content/corner-tl.gif);"></div>
		<div class="Corner-tr" style="background-image: url('.$template_path.'/images/content/corner-tr.gif);"></div>
		<div class="Border_1" style="background-image: url('.$template_path.'/images/content/border-1.gif);"></div>
		<div class="BorderTitleText" style="background-image: url('.$template_path.'/images/content/title-background-green.gif);"></div>
		<img class="Title" src="'.$template_path.'/images/header/headline-newsticker.gif" alt="Contentbox headline">
		<div class="Border_2">
			<div class="Border_3">
			<div class="BoxContent" style="background-image: url('.$template_path.'/images/content/scroll.gif);">';

			//add tickers list
	$news_content .= $tickers_to_add;
	//koniec
	$news_content .= '</div>
		  </div>
		</div>
		<div class="Border_1" style="background-image: url('.$template_path.'/images/content/border-1.gif);"></div>
		<div class="CornerWrapper-b"><div class="Corner-bl" style="background-image: url('.$template_path.'/images/content/corner-bl.gif);"></div></div>
		<div class="CornerWrapper-b"><div class="Corner-br" style="background-image: url('.$template_path.'/images/content/corner-br.gif);"></div></div>
	  </div>';

		if($cache->enabled() && !$news_cached && !$canEdit)
			$cache->set('news_' . $template_name . '_' . TICKET, $tickers_to_add, 120);
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
			'forum_section' => isset($forum_section) ? $forum_section : null
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
				<a id="delete" href="' . BASE_URL . '?subtopic=news&action=delete&id=' . $news['id'] . '" onclick="return confirm(\'Are you sure?\');" title="Delete">
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
				'title' => stripslashes($news['title']),
				'content' => $content_ . $admin_options,
				'date' => $news['date'],
				'icon' => $categories[$news['category']]['icon_id'],
				'author' => $config['news_author'] ? $author : '',
				'comments' => $news['comments'] != 0 ? getForumThreadLink($news['comments']) : null,
				'news_date_format' => $config['news_date_format']
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
	static public function verify($title, $body, &$errors)
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
		
		return true;
	}

	static public function add($title, $body, $type, $category, $player_id, $comments, &$errors)
	{
		global $db;
		if(!News::verify($title, $body, $errors))
			return false;

		$db->insert(TABLE_PREFIX . 'news', array('title' => $title, 'body' => $body, 'type' => $type, 'date' => time(), 'category' => $category, 'player_id' => isset($player_id) ? $player_id : 0, 'comments' => $comments));
		return true;
	}

	static public function get($id) {
		global $db;
		return $db->select(TABLE_PREFIX . 'news', array('id' => $id));
	}

	static public function update($id, $title, $body, $type, $category, $player_id, $comments, &$errors)
	{
		global $db;
		if(!News::verify($title, $body, $errors))
			return false;

		$db->update(TABLE_PREFIX . 'news', array('title' => $title, 'body' => $body, 'type' => $type, 'category' => $category, 'last_modified_by' => isset($player_id) ? $player_id : 0, 'last_modified_date' => time(), 'comments' => $comments), array('id' => $id));
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
		global $cache, $config, $template_name;
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

class Forum
{
	static public function add($title, $body, $section_id, $player_id, $account_id, &$errors)
	{
		global $db;
		$thread_id = 0;
		if($db->insert(TABLE_PREFIX . 'forum', array('first_post' => 0, 'last_post' => time(), 'section' => $section_id, 'replies' => 0, 'views' => 0, 'author_aid' => isset($account_id) ? $account_id : 0, 'author_guid' => isset($player_id) ? $player_id : 0, 'post_text' => $body, 'post_topic' => $title, 'post_smile' => 0, 'post_date' => time(), 'last_edit_aid' => 0, 'edit_date' => 0, 'post_ip' => $_SERVER['REMOTE_ADDR']))) {
			$thread_id = $db->lastInsertId();
			$db->query("UPDATE `" . TABLE_PREFIX . "forum` SET `first_post`=".(int) $thread_id." WHERE `id` = ".(int) $thread_id);
		}
		
		return $thread_id;
	}
}
?>
