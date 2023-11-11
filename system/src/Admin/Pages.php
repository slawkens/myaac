<?php
namespace MyAAC\Admin;

use MyAAC\Models\Pages as ModelsPages;

class Pages
{
	static public function verify($name, $title, $body, $player_id, $php, $enable_tinymce, $access, &$errors)
	{
		if(!isset($title[0]) || !isset($body[0])) {
			$errors[] = 'Please fill all inputs.';
			return false;
		}
		if(strlen($name) > PAGE_NAME_LIMIT) {
			$errors[] = 'Page name cannot be longer than ' . PAGE_NAME_LIMIT . ' characters.';
			return false;
		}
		if(strlen($title) > PAGE_TITLE_LIMIT) {
			$errors[] = 'Page title cannot be longer than ' . PAGE_TITLE_LIMIT . ' characters.';
			return false;
		}
		if(strlen($body) > PAGE_BODY_LIMIT) {
			$errors[] = 'Page content cannot be longer than ' . PAGE_BODY_LIMIT . ' characters.';
			return false;
		}
		if(!isset($player_id) || $player_id == 0) {
			$errors[] = 'Player ID is wrong.';
			return false;
		}
		if(!isset($php) || ($php != 0 && $php != 1)) {
			$errors[] = 'Enable PHP is wrong.';
			return false;
		}
		if ($php == 1 && !getBoolean(setting('core.admin_pages_php_enable'))) {
			$errors[] = 'PHP pages disabled on this server. To enable go to Settings in Admin Panel and enable <strong>Enable PHP Pages</strong>.';
			return false;
		}
		if(!isset($enable_tinymce) || ($enable_tinymce != 0 && $enable_tinymce != 1)) {
			$errors[] = 'Enable TinyMCE is wrong.';
			return false;
		}
		if(!isset($access) || $access < 0 || $access > PHP_INT_MAX) {
			$errors[] = 'Access is wrong.';
			return false;
		}

		return true;
	}

	static public function get($id)
	{
		$row = ModelsPages::find($id);
		if ($row) {
			return $row->toArray();
		}

		return false;
	}

	static public function add($name, $title, $body, $player_id, $php, $enable_tinymce, $access, &$errors)
	{
		if(!self::verify($name, $title, $body, $player_id, $php, $enable_tinymce, $access, $errors)) {
			return false;
		}

		if (!ModelsPages::where('name', $name)->exists())
			ModelsPages::create([
				'name' => $name,
				'title' => $title,
				'body' => $body,
				'player_id' => $player_id,
				'php' => $php ? '1' : '0',
				'enable_tinymce' => $enable_tinymce ? '1' : '0',
				'access' => $access
			]);
		else
			$errors[] = 'Page with this link already exists.';

		return !count($errors);
	}

	static public function update($id, $name, $title, $body, $player_id, $php, $enable_tinymce, $access, &$errors)
	{
		if(!self::verify($name, $title, $body, $player_id, $php, $enable_tinymce, $access, $errors)) {
			return false;
		}

		ModelsPages::where('id', $id)->update([
			'name' => $name,
			'title' => $title,
			'body' => $body,
			'player_id' => $player_id,
			'php' => $php ? '1' : '0',
			'enable_tinymce' => $enable_tinymce ? '1' : '0',
			'access' => $access
		]);
		return true;
	}

	static public function delete($id, &$errors)
	{
		if (isset($id)) {
			$row = ModelsPages::find($id);
			if ($row) {
				$row->delete();
			}
			else
				$errors[] = 'Page with id ' . $id . ' does not exists.';
		} else
			$errors[] = 'id not set';

		return !count($errors);
	}

	static public function toggleHidden($id, &$errors, &$status)
	{
		if (isset($id)) {
			$row = ModelsPages::find($id);
			if ($row) {
				$row->hidden = $row->hidden == 1 ? 0 : 1;
				if (!$row->save()) {
					$errors[] = 'Fail during toggle hidden Page.';
				}
				$status = $row->hidden;
			}
			else {
				$errors[] = 'Page with id ' . $id . ' does not exists.';
			}
		} else
			$errors[] = 'id not set';

		return !count($errors);
	}
}
