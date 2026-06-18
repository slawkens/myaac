<?php
/**
 * Deprecated functions (compat)
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

class Validator extends \MyAAC\Validator {}

function check_name($name, &$errors = '') {
	if(Validator::characterName($name))
		return true;

	$errors = Validator::getLastError();
	return false;
}

function check_account_id($id, &$errors = '') {
	if(Validator::accountId($id))
		return true;

	$errors = Validator::getLastError();
	return false;
}

function check_account_name($name, &$errors = '') {
	if(Validator::accountName($name))
		return true;

	$errors = Validator::getLastError();
	return false;
}

function check_name_new_char($name, &$errors = '') {
	if(Validator::newCharacterName($name))
		return true;

	$errors = Validator::getLastError();
	return false;
}

function check_rank_name($name, &$errors = '') {
	if(Validator::rankName($name))
		return true;

	$errors = Validator::getLastError();
	return false;
}

function check_guild_name($name, &$errors = '') {
	if(Validator::guildName($name))
		return true;

	$errors = Validator::getLastError();
	return false;
}

function news_place() {
	return tickers();
}

function tableExist(string $table): bool
{
	global $db;
	return $db->hasTable($table);
}

function fieldExist(string $field, string $table): bool
{
	global $db;
	return $db->hasColumn($table, $field);
}

function get_browser_real_ip(): string {
	return $_SERVER['REMOTE_ADDR'] ?? '';
}
