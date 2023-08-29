<?php

use MyAAC\Models\Changelog as ModelsChangelog;

class Changelog
{
	static public function verify($body,$date, &$errors)
	{
		if(!isset($date) || !isset($body[0])) {
			$errors[] = 'Please fill all inputs.';
			return false;
		}

		if(strlen($body) > CL_LIMIT) {
			$errors[] = 'Changelog content cannot be longer than ' . CL_LIMIT . ' characters.';
			return false;
		}

		return true;
	}

	static public function add($body, $type, $where, $player_id, $cdate, &$errors)
	{
		if(!self::verify($body,$cdate, $errors))
			return false;

		$row = new ModelsChangelog;
		$row->body = $body;
		$row->type = $type;
		$row->date = $cdate;
		$row->where = $where;
		$row->player_id = $player_id ?? 0;
		if ($row->save()) {
			self::clearCache();
			return true;
		}

		return false;
	}

	static public function get($id) {
		return ModelsChangelog::find($id);
	}

	static public function update($id, $body, $type, $where, $player_id, $date,  &$errors)
	{
		if(!self::verify($body,$date, $errors))
			return false;

		if (ModelsChangelog::where('id', '=', $id)->update([
			'body' => $body,
			'type' => $type,
			'where' => $where,
			'player_id' => $player_id ?? 0,
			'date' => $date
		])) {
			self::clearCache();
			return true;
		}

		return false;
	}

	static public function delete($id, &$errors)
	{
		if(isset($id))
		{
			$row = ModelsChangelog::find($id);
			if ($row) {
				if (!$row->delete()) {
					$errors[] = 'Fail during delete Changelog.';
				}
			} else {
				$errors[] = 'Changelog with id ' . $id . ' does not exist.';
			}
		} else {
			$errors[] = 'Changelog id not set.';
		}

		if(count($errors)) {
			return false;
		}

		self::clearCache();
		return true;
	}

	static public function toggleHidden($id, &$errors, &$status)
	{
		if(isset($id))
		{
			$row = ModelsChangelog::find($id);
			if ($row) {
				$row->hidden = $row->hidden == 1 ? 0 : 1;
				if (!$row->save()) {
					$errors[] = 'Fail during toggle hidden Changelog.';
				}
			} else {
				$errors[] = 'Changelog with id ' . $id . ' does not exists.';
			}

		}
		else
			$errors[] = 'Changelog id not set.';

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
			if ($cache->fetch('changelog_' . $template_name, $tmp) && isset($tmp[0])) {
				return $tmp;
			}
		}

		return false;
	}

	static public function clearCache()
	{
		global $template_name;
		$cache = Cache::getInstance();
		if (!$cache->enabled()) {
			return;
		}

		$tmp = '';
		foreach (get_templates() as $template) {
			if ($cache->fetch('changelog_' . $template_name, $tmp)) {
				$cache->delete('changelog_' . $template_name);
			}

		}
	}
}
