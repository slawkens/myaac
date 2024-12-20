<?php

namespace MyAAC;

use MyAAC\Cache\Cache;
use MyAAC\Models\News as ModelsNews;

class News
{
	static public function verify($title, $body, $article_text, $article_image, &$errors)
	{
		if(!isset($title[0]) || !isset($body[0])) {
			$errors[] = 'Please fill all inputs.';
			return false;
		}
		if(strlen($title) > NEWS_TITLE_LIMIT) {
			$errors[] = 'News title cannot be longer than ' . NEWS_TITLE_LIMIT . ' characters.';
			return false;
		}
		if(strlen($body) > NEWS_BODY_LIMIT) {
			$errors[] = 'News content cannot be longer than ' . NEWS_BODY_LIMIT . ' characters.';
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
		if(!self::verify($title, $body, $article_text, $article_image, $errors))
			return false;

		$currentTime = time();

		$params = [
			'title' => $title, 'body' => $body,
			'type' => $type, 'category' => $category,
			'date' => $currentTime,
			'player_id' => $player_id ?? 0,
			'comments' => $comments,
			'article_text' => ($type == 3 ? $article_text : ''),
			'article_image' => ($type == 3 ? $article_image : '')
		];

		global $hooks;
		if (!$hooks->trigger(HOOK_ADMIN_NEWS_ADD_PRE, $params)) {
			return false;
		}

		$newsModel = ModelsNews::create($params);

		$hooks->trigger(HOOK_ADMIN_NEWS_ADD,
			$params + ['id' => $newsModel->id],
		);

		self::clearCache();
		return true;
	}

	static public function get($id) {
		return ModelsNews::find($id)->toArray();
	}

	static public function update($id, $title, $body, $type, $category, $player_id, $comments, $article_text, $article_image, &$errors)
	{
		if(!self::verify($title, $body, $article_text, $article_image, $errors)) {
			return false;
		}

		$currentTime = time();

		$params = [
			'id' => $id,
			'title' => $title, 'body' => $body,
			'type' => $type, 'category' => $category,
			'last_modified_by' => $player_id ?? 0, 'last_modified_date' => $currentTime,
			'comments' => $comments,
			'article_text' => ($type == 3 ? $article_text : ''),
			'article_image' => ($type == 3 ? $article_image : ''),
		];

		global $hooks;
		if (!$hooks->trigger(HOOK_ADMIN_NEWS_UPDATE_PRE, $params)) {
			return false;
		}

		unset($params['id']);

		ModelsNews::where('id', $id)->update($params);

		$hooks->trigger(HOOK_ADMIN_NEWS_UPDATE,
			$params + ['id' => $id]
		);

		self::clearCache();
		return true;
	}

	static public function delete($id, &$errors)
	{
		global $hooks;

		if(isset($id)) {
			$row = ModelsNews::find($id);
			if($row) {
				$params = ['id' => $id];

				if (!$hooks->trigger(HOOK_ADMIN_NEWS_DELETE_PRE, $params)) {
					return false;
				}

				if ($row->delete()) {
					$hooks->trigger(HOOK_ADMIN_NEWS_DELETE, $params);
				} else {
					$errors[] = 'Fail during delete News.';
				}
			}
			else {
				$errors[] = 'News with id ' . $id . ' does not exists.';
			}
		}
		else {
			$errors[] = 'News id not set.';
		}

		if(count($errors)) {
			return false;
		}

		self::clearCache();
		return true;
	}

	static public function toggleHide($id, &$errors, &$status)
	{
		global $hooks;

		if(isset($id)) {
			$row = ModelsNews::find($id);
			if($row) {
				$row->hide = ($row->hide == 1 ? 0 : 1);

				$params = ['hide' => $row->hide];

				if (!$hooks->trigger(HOOK_ADMIN_NEWS_TOGGLE_HIDE_PRE, $params)) {
					return false;
				}

				if ($row->save()) {
					$hooks->trigger(HOOK_ADMIN_NEWS_TOGGLE_HIDE, $params);
				}
				else {
					$errors[] = 'Fail during toggle hide News.';
				}

				$status = $row->hide;
			}
			else {
				$errors[] = 'News with id ' . $id . ' does not exists.';
			}
		}
		else {
			$errors[] = 'News id not set.';
		}

		if(count($errors)) {
			return false;
		}

		self::clearCache();
		return true;
	}

	static public function getCached($type)
	{
		global $template_name;

		$cache = Cache::getInstance();
		if ($cache->enabled())
		{
			$tmp = '';
			if ($cache->fetch('news_' . $template_name . '_' . $type, $tmp) && isset($tmp[0])) {
				return $tmp;
			}
		}

		return false;
	}

	static public function clearCache()
	{
		$cache = Cache::getInstance();
		if (!$cache->enabled()) {
			return;
		}

		$tmp = '';
		foreach (get_templates() as $template) {
			if ($cache->fetch('news_' . $template . '_' . NEWS, $tmp)) {
				$cache->delete('news_' . $template . '_' . NEWS);
			}

			if ($cache->fetch('news_' . $template . '_' . TICKER, $tmp)) {
				$cache->delete('news_' . $template . '_' . TICKER);
			}

			if ($cache->fetch('news_' . $template . '_' . ARTICLE, $tmp)) {
				$cache->delete('news_' . $template . '_' . ARTICLE);
			}
		}
	}
}
