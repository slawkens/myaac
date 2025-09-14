<?php
/**
 * News
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Cache\Cache;
use MyAAC\News;

defined('MYAAC') or die('Direct access not allowed!');

$canEdit = hasFlag(FLAG_CONTENT_NEWS) || superAdmin();
if(isset($_GET['archive']))
{
	$title = 'News Archive';

	$categories = array();
	foreach($db->query('SELECT id, name, icon_id FROM ' . TABLE_PREFIX . 'news_categories WHERE hide != 1') as $cat)
	{
		$categories[$cat['id']] = array(
			'name' => $cat['name'],
			'icon_id' => $cat['icon_id']
		);
	}

	// display big news by id
	if(isset($_GET['id']))
	{
		$id = (int)$_GET['id'];

		$field_name = 'date';
		if($id < 100000)
			$field_name = 'id';

		$news = $db->query('SELECT * FROM `'.TABLE_PREFIX . 'news` WHERE `hide` != 1 AND `' . $field_name . '` = ' . $id  . '');
		if($news->rowCount() == 1)
		{
			$news = $news->fetch();
			$author = '';
			$query = $db->query('SELECT `name` FROM `players` WHERE id = ' . $db->quote($news['player_id']) . ' LIMIT 1;');
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

			$admin_options = '';
			if($canEdit) {
				$admin_options = '<br/><br/>' . $twig->render('admin.links.html.twig', ['page' => 'news', 'id' => $news['id'], 'hide' => $news['hide']]);
			}

			$twig->display('news.html.twig', array(
				'title' => stripslashes($news['title']),
				'content' => $content_ . $admin_options,
				'date' => $news['date'],
				'icon' => $categories[$news['category']]['icon_id'],
				'author' => setting('core.news_author') ? $author : '',
				'comments' => $news['comments'] != 0 ? getForumThreadLink($news['comments']) : null,
			));
		}
		else
			echo "This news doesn't exist or is hidden.<br/>";

		$twig->display('news.back_button.html.twig');
		return;
	}
	?>

	<?php

	$newses = array();
	$news_DB = $db->query('SELECT * FROM '.$db->tableName(TABLE_PREFIX . 'news').' WHERE `type` = 1 AND `hide` != 1 ORDER BY `date` DESC');
	foreach($news_DB as $news)
	{
		$newses[] = array(
			'link' => getLink('news/archive') . '/' . $news['id'],
			'icon_id' => $categories[$news['category']]['icon_id'],
			'title' => stripslashes($news['title']),
			'date' => $news['date']
		);
	}

	$twig->display('news.archive.html.twig', array(
		'newses' => $newses
	));

	return;
}

header('X-XSS-Protection: 0');
$title = 'Latest News';

$cache = Cache::getInstance();

$news_cached = false;
if($cache->enabled())
	$news_cached = News::getCached(NEWS);

if(!$news_cached)
{
	$categories = array();
	foreach($db->query('SELECT `id`, `name`, `icon_id` FROM `' . TABLE_PREFIX . 'news_categories` WHERE `hide` != 1') as $cat)
	{
		$categories[$cat['id']] = array(
			'name' => $cat['name'],
			'icon_id' => $cat['icon_id']
		);
	}

	$tickers_db = $db->query('SELECT * FROM `' . TABLE_PREFIX . 'news` WHERE `type` = ' . TICKER .($canEdit ? '' : ' AND `hide` != 1') .' ORDER BY `date` DESC LIMIT ' . setting('core.news_ticker_limit'));
	$tickers_content = '';
	if($tickers_db->rowCount() > 0)
	{
		$tickers = $tickers_db->fetchAll();
		foreach($tickers as &$ticker) {
			$ticker['icon'] = $categories[$ticker['category']]['icon_id'];
			$ticker['body_short'] = short_text(strip_tags($ticker['body']), 100);
			$ticker['hidden'] = $ticker['hide'];
		}

		$tickers_content = $twig->render('news.tickers.html.twig', array(
			'tickers' => $tickers,
			'canEdit' => $canEdit
		));
	}

	if($cache->enabled() && !$canEdit)
		$cache->set('news_' . $template_name . '_' . TICKER, $tickers_content, 60 * 60);

	$featured_article_db =$db->query('SELECT `id`, `title`, `article_text`, `article_image`, `hide` FROM `' . TABLE_PREFIX . 'news` WHERE `type` = ' . ARTICLE . ($canEdit ? '' : ' AND `hide` != 1') .' ORDER BY `date` DESC LIMIT 1');
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
					'hide' => $article['hide'],
					'hidden' => $article['hide'],
					'read_more'=> getLink('news/archive/') . $article['id']
				),
				'canEdit' => $canEdit
			));
		}

		if($cache->enabled() && !$canEdit)
			$cache->set('news_' . $template_name . '_' . ARTICLE, $featured_article, 60 * 60);
	}
}
else {
	$tickers_content = News::getCached(TICKER);
	$featured_article = News::getCached(ARTICLE);
}

if(!$news_cached)
{
	ob_start();
	$newses = $db->query('SELECT * FROM ' . $db->tableName(TABLE_PREFIX . 'news') . ' WHERE type = ' . NEWS . ($canEdit ? '' : ' AND hide != 1') . ' ORDER BY date' . ' DESC LIMIT ' . setting('core.news_limit'));
	if($newses->rowCount() > 0)
	{
		foreach($newses as $news)
		{
			$author = '';
			$query = $db->query('SELECT `name` FROM `players` WHERE id = ' . $db->quote($news['player_id']) . ' LIMIT 1');
			if($query->rowCount() > 0) {
				$query = $query->fetch();
				$author = $query['name'];
			}

			$admin_options = '';
			if($canEdit) {
				$admin_options = '<br/><br/>' . $twig->render('admin.links.html.twig', ['page' => 'news', 'id' => $news['id'], 'hide' => $news['hide']]);
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

			$twig->display('news.html.twig', array(
				'id' => $news['id'],
				'title' => stripslashes($news['title']),
				'content' => $content_ . $admin_options,
				'date' => $news['date'],
				'icon' => $categories[$news['category']]['icon_id'],
				'author' => setting('core.news_author') ? $author : '',
				'comments' => $news['comments'] != 0 ? getForumThreadLink($news['comments']) : null,
				'hide'=> $news['hide']
			));
		}
	}

	$tmp_content = ob_get_contents();
	ob_end_clean();

	if($cache->enabled() && !$canEdit)
		$cache->set('news_' . $template_name . '_' . NEWS, $tmp_content, 60 * 60);

	echo $tmp_content;
}
else
	echo $news_cached;
