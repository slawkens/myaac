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

function tableExist($table)
{
	global $db;
	return $db->hasTable($table);
}

function fieldExist($field, $table)
{
	global $db;
	return $db->hasColumn($table, $field);
}

function Outfits_loadfromXML(): ?array
{
	global $config;
	$file_path = $config['data_path'] . 'XML/outfits.xml';
	if (!file_exists($file_path)) {	return null; }

	$xml = new DOMDocument;
	$xml->load($file_path);

	$outfits = null;
	foreach ($xml->getElementsByTagName('outfit') as $outfit) {
		$outfits[] = Outfit_parseNode($outfit);
	}
	return $outfits;
}

function Outfit_parseNode($node): array
{
	$looktype = (int)$node->getAttribute('looktype');
	$type = (int)$node->getAttribute('type');
	$lookname = $node->getAttribute('name');
	$premium = $node->getAttribute('premium');
	$unlocked = $node->getAttribute('unlocked');
	$enabled = $node->getAttribute('enabled');
	return array('id' => $looktype, 'type' => $type, 'name' => $lookname, 'premium' => $premium, 'unlocked' => $unlocked, 'enabled' => $enabled);
}

function Mounts_loadfromXML(): ?array
{
	global $config;
	$file_path = $config['data_path'] . 'XML/mounts.xml';
	if (!file_exists($file_path)) {	return null; }

	$xml = new DOMDocument;
	$xml->load($file_path);

	$mounts = null;
	foreach ($xml->getElementsByTagName('mount') as $mount) {
		$mounts[] = Mount_parseNode($mount);
	}
	return $mounts;
}

function Mount_parseNode($node): array
{
	$id = (int)$node->getAttribute('id');
	$clientid = (int)$node->getAttribute('clientid');
	$name = $node->getAttribute('name');
	$speed = (int)$node->getAttribute('speed');
	$premium = $node->getAttribute('premium');
	$type = $node->getAttribute('type');
	return array('id' => $id, 'clientid' => $clientid, 'name' => $name, 'speed' => $speed, 'premium' => $premium, 'type' => $type);
}
