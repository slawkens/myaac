<?php
/**
 * Validator class
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

namespace MyAAC;

use MyAAC\Models\Monster;
use MyAAC\Models\Spell;

class Validator
{
	private static $lastError = '';
	public static function number($number) {
		if(!preg_match("/^([0-9]+)$/", $number)) {
			self::$lastError = 'Invalid number format.';
			return false;
		}

		return true;
	}

	/**
	 * Validate account id
	 * Id lenght must be 6-10 chars
	 *
	 * @param string $id Account id to check
	 * @return bool Is account name valid?
	 */
	public static function accountId($id)
	{
		if(!isset($id[0]))
		{
			self::$lastError = 'Please enter your account number!';
			return false;
		}

		if(!Validator::number($id)) {
			self::$lastError = 'Invalid account number format. Please use only numbers 0-9.';
			return false;
		}

		$length = strlen($id);
		if($length < 6)
		{
			self::$lastError = 'Account is too short (min. 6 chars).';
			return false;
		}

		if($length > 10)
		{
			self::$lastError = 'Account is too long (max. 10 chars).';
			return false;
		}

		return true;
	}

	/**
	 * Validate account name
	 * Name lenght must be 3-32 chars
	 *
	 * @param string $name Account name to check
	 * @return bool Is account name valid?
	 */
	public static function accountName($name)
	{
		if(!isset($name[0]))
		{
			self::$lastError = 'Please enter your account name!';
			return false;
		}

		$length = strlen($name);
		if($length < 3)
		{
			self::$lastError = 'Account name is too short (min. 3 chars).';
			return false;
		}

		if($length > 32)
		{
			self::$lastError = 'Account name is too long (max. 32 chars).';
			return false;
		}

		if(preg_match('/ {2,}/', $name))
		{
			self::$lastError = 'Invalid account name format. Use only A-Z and numbers 0-9 and no double spaces.';
			return false;
		}

		if(!preg_match("/^[A-Z0-9]+$/i", $name))
		{
			self::$lastError = 'Invalid account name format. Use only A-Z and numbers 0-9.';
			return false;
		}

		return true;
	}

	/**
	 * Advanced mail validator
	 *
	 * @param string $email
	 * @return bool Is email valid?
	 */
	public static function email($email) {
		if(empty($email)) {
			self::$lastError = 'Please enter your new email address.';
			return false;
		}

		if(strlen($email) > 255) {
			self::$lastError = 'E-mail is too long (max. 255 chars).';
			return false;
		}

		if(setting('core.account_mail_block_plus_sign')) {
			$explode = explode('@', $email);
			if(isset($explode[0]) && (strpos($explode[0],'+') !== false)) {
				self::$lastError = 'Please do not use plus (+) sign in your e-mail.';
				return false;
			}
		}

		if(!preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[A-z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/', $email)) {
			self::$lastError = 'Invalid e-mail format.';
			return false;
		}

		return true;
	}

	/**
	 * Validate account password
	 * Name lenght must be 3-32 chars
	 *
	 * @param string $password Password to check
	 * @return bool Is password valid?
	 */
	public static function password($password)
	{
		if (!isset($password[0])) {
			self::$lastError = 'Please enter the password.';
			return false;
		}

		if (strlen($password) < 8 || strlen($password) > 29) {
			self::$lastError = 'The password must have at least 8 and maximum 29 letters!';
			return false;
		}

		if(!preg_match('/[a-zA-Z]/', $password)) {
			self::$lastError = 'The password must contain at least one letter A-Z or a-z!';
			return false;
		}

		if(!preg_match('/[0-9]/', $password)) {
			self::$lastError = 'The password must contain at least one number!';
			return false;
		}

		return true;
	}

	/**
	 * Validate character name.
	 * Name lenght must be 3-25 chars
	 *
	 * @param  string $name Name to check
	 * @return bool Is name valid?
	 */
	public static function characterName($name)
	{
		if(empty($name)) {
			self::$lastError = 'Please enter character name.';
			return false;
		}

		// installer doesn't know config.php yet
		// that's why we need to ignore the nulls
		if(defined('MYAAC_INSTALL')) {
			$minLength = 4;
			$maxLength = 21;
		}
		else {
			$minLength = setting('core.create_character_name_min_length');
			$maxLength = setting('core.create_character_name_max_length');
		}

		$length = strlen($name);
		if($length < $minLength)
		{
			self::$lastError = "Character name is too short. Min. length <b>$minLength</b> characters.";
			return false;
		}

		if($length > $maxLength)
		{
			self::$lastError = "Character name is too long. Max. length <b>$maxLength</b> characters.";
			return false;
		}

		if(strspn($name, "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM- [ ] '") != $length)
		{
			self::$lastError = "Invalid name format. Use only A-Z, spaces and '.";
			return false;
		}

		if(preg_match('/ {2,}/', $name))
		{
			self::$lastError = 'Invalid character name format. Use only A-Z and no double spaces.';
			return false;
		}

		if(!preg_match("/[A-z ']/", $name))
		{
			self::$lastError = "Invalid name format. Use only A-Z, spaces and '.";
			return false;
		}

		return true;
	}

	/**
	 * Validate new character name.
	 * Name lenght must be 3-25 chars
	 *
	 * @param  string $name Name to check
	 * @return bool Is name valid?
	 */
	public static function newCharacterName($name)
	{
		global $db, $config;

		$name_lower = strtolower($name);

		$first_words_blocked = array_merge(["'", '-'], setting('core.create_character_name_blocked_prefix'));
		foreach($first_words_blocked as $word) {
			if($word == substr($name_lower, 0, strlen($word))) {
				self::$lastError = 'Your name contains blocked words.';
				return false;
			}
		}

		if(str_ends_with($name_lower, "'") || str_ends_with($name_lower, "-")) {
			self::$lastError = 'Your name contains illegal characters.';
			return false;
		}

		if(substr($name_lower, 1, 1) == ' ') {
			self::$lastError = 'Your name contains illegal space.';
			return false;
		}

		if(substr($name_lower, -2, 1) == " ") {
			self::$lastError = 'Your name contains illegal space.';
			return false;
		}

		if(preg_match('/ {2,}/', $name)) {
			self::$lastError = 'Invalid character name format. Use only A-Z and numbers 0-9 and no double spaces.';
			return false;
		}

		if(strtolower($config['lua']['serverName']) == $name_lower) {
			self::$lastError = 'Your name cannot be same as server name.';
			return false;
		}

		$names_blocked = setting('core.create_character_name_blocked_names');
		foreach($names_blocked as $word) {
			if($word == $name_lower) {
				self::$lastError = 'Your name contains blocked words.';
				return false;
			}
		}

		$words_blocked = array_merge(['--', "''","' ", " '", '- ', ' -', "-'", "'-"], setting('core.create_character_name_blocked_words'));
		foreach($words_blocked as $word) {
			if(str_contains($name_lower, $word)) {
				self::$lastError = 'Your name contains illegal words.';
				return false;
			}
		}

		$name_length = strlen($name_lower);
		for($i = 0; $i < $name_length; $i++)
		{
			if(isset($name_lower[$i]) && isset($name_lower[$i + 1]) && $name_lower[$i] == $name_lower[$i + 1] && isset($name_lower[$i + 2]) && $name_lower[$i] == $name_lower[$i + 2]) {
				self::$lastError = 'Your name is invalid.';
				return false;
			}
		}

		// check if was namelocked previously
		if($db->hasTable('player_namelocks') && $db->hasColumn('player_namelocks', 'name')) {
			$namelock = $db->query('SELECT `player_id` FROM `player_namelocks` WHERE `name` = ' . $db->quote($name));
			if($namelock->rowCount() > 0) {
				self::$lastError =  'Character with this name has been namelocked.';
				return false;
			}
		}

		$monstersCheck = setting('core.create_character_name_monsters_check');
		if ($monstersCheck) {
			if (Monster::where('name', 'like', $name_lower)->exists()) {
				self::$lastError = 'Your name cannot contains monster name.';
				return false;
			}
		}

		$spellsCheck = setting('core.create_character_name_spells_check');
		if ($spellsCheck) {
			if (Spell::where('name', 'like', $name_lower)->exists()) {
				self::$lastError = 'Your name cannot contains spell name.';
				return false;
			}

			if (Spell::where('words', $name_lower)->exists()) {
				self::$lastError = 'Your name cannot contains spell name.';
				return false;
			}
		}

		$npcCheck = setting('core.create_character_name_npc_check');
		if ($npcCheck) {
			NPCs::load();
			if(NPCs::$npcs) {
				foreach (NPCs::$npcs as $npc) {
					if(str_contains($name_lower, $npc)) {
						self::$lastError = 'Your name cannot contains NPC name.';
						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * Validate guild name
	 * Name lenght must be 3-32 chars
	 *
	 * @param  string $name Name to check
	 * @return bool Is name valid?
	 */
	public static function guildName($name)
	{
		if(empty($name)) {
			self::$lastError = 'Please enter guild name.';
			return false;
		}

		if(strspn($name, "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789- ") != strlen($name)) {
			self::$lastError = 'Invalid guild name format.';
			return false;
		}

		if(!preg_match("/[A-z ]{3,32}/", $name)) {
			self::$lastError = 'Invalid guild name format.';
			return false;
		}

		return true;
	}

	/**
	 * Validate guild nick
	 * Nick lenght must be 3-40 chars
	 *
	 * @param  string $name Name to check
	 * @return bool Is name valid?
	 */
	public static function guildNick($name)
	{
		if(empty($name)) {
			self::$lastError = 'Please enter guild nick.';
			return false;
		}

		if(strspn($name, "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789- ") != strlen($name)) {
			self::$lastError = 'Invalid guild nick format.';
			return false;
		}

		if(!preg_match("/[A-z ]{3,40}/", $name)) {
			self::$lastError = 'Invalid guild nick format.';
			return false;
		}

		return true;
	}

	/**
	 * Validate rank name
	 * Rank lenght must be 1-32 chars
	 *
	 * @param  string $name Name to check
	 * @return bool Is name valid?
	 */
	public static function rankName($name)
	{
		if(empty($name)) {
			self::$lastError = 'Please enter rank name.';
			return false;
		}

		if(strspn($name, "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789-[ ] ") != strlen($name)) {
			self::$lastError = 'Invalid rank name. Please use only a-Z, 0-9 and spaces.';
			return false;
		}

		if(!preg_match("/[A-z ]{1,32}/", $name)) {
			self::$lastError = 'Invalid rank name. Please use only a-Z, 0-9 and spaces.';
			return false;
		}

		return true;
	}
	/**
	 * Simple string validator, checks if string contains valid characters
	 *
	 * @param string $str String to validate
	 * @param boolean $numbers Numbers should be allowed?
	 */
	public static function str($str, $numbers = false) {
		return preg_match('/^[a-z0-9\ ]*$/i', $str);
	}

	public static function getLastError() {
		return self::$lastError;
	}
}
