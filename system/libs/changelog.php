<?php

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
		global $db;
		if(!self::verify($body,$cdate, $errors))
			return false;

		$db->insert(TABLE_PREFIX . 'changelog', array('body' => $body, 'type' => $type, 'date' => $cdate, 'where' => $where, 'player_id' => isset($player_id) ? $player_id : 0));
		self::clearCache();
		return true;
	}

	static public function get($id) {
		global $db;
		return $db->select(TABLE_PREFIX . 'changelog', array('id' => $id));
	}

	static public function update($id, $body, $type, $where, $player_id, $date,  &$errors)
	{
		global $db;
		if(!self::verify($body,$date, $errors))
			return false;

		$db->update(TABLE_PREFIX . 'changelog', array('body' => $body, 'type' => $type, 'where' => $where, 'player_id' => isset($player_id) ? $player_id : 0, 'date' => $date), array('id' => $id));
		self::clearCache();
		return true;
	}

	static public function delete($id, &$errors)
	{
		global $db;
		if(isset($id))
		{
			if($db->select(TABLE_PREFIX . 'changelog', array('id' => $id)) !== false)
				$db->delete(TABLE_PREFIX . 'changelog', array('id' => $id));
			else
				$errors[] = 'Changelog with id ' . $id . ' does not exist.';
		}
		else
			$errors[] = 'Changelog id not set.';

		if(count($errors)) {
			return false;
		}

		self::clearCache();
		return true;
	}

	static public function toggleHidden($id, &$errors, &$status)
	{
		global $db;
		if(isset($id))
		{
			$query = $db->select(TABLE_PREFIX . 'changelog', array('id' => $id));
			if($query !== false)
			{
				$db->update(TABLE_PREFIX . 'changelog', array('hidden' => ($query['hidden'] == 1 ? 0 : 1)), array('id' => $id));
				$status = $query['hidden'];
			}
			else
				$errors[] = 'Changelog with id ' . $id . ' does not exists.';
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
