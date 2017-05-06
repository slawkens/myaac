<?php
/**
 * Useful functions
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.0.6
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
function success($message) {
	echo '<p class="success">' . $message . '</p>';
}
function warning($message) {
	echo '<p class="warning">' . $message . '</p>';
}
function error($message) {
	echo '<p class="error">' . $message . '</p>';
}

function longToIp($ip)
{
	$exp = explode(".", long2ip($ip));
	return $exp[3].".".$exp[2].".".$exp[1].".".$exp[0];
}

function generateLink($url, $name, $blank = false) {
	return '<a href="' . $url . '"' . ($blank ? ' target="_blank"' : '') . '>' . $name . '</a>';
}

function getLink($page, $name, $blank = false) {
	return generateLink(getPageLink($page), $name, $blank);
}

function getPageLink($page, $action = null)
{
	global $config;

	// TODO: tibiacom template is not working correctly with this
	if($config['friendly_urls'])
		return BASE_URL . $page . ($action ? '/' . $action : '');

	return BASE_URL . '?subtopic=' . $page . ($action ? '&action=' . $action : '');
}
function internalLayoutLink($page, $action = null) {return getPageLink($page, $action);}

function getForumThreadLink($thread_id, $page = NULL)
{
	global $config;

	$url = '';
	if($config['friendly_urls'])
		$url = BASE_URL . 'forum/thread/' . (int)$thread_id . (isset($page) ? '/' . $page : '');
	else
		$url = BASE_URL . '?subtopic=forum&action=show_thread&id=' . (int)$thread_id . (isset($page) ? '&page=' . $page : '');

	return $url;
}

function getForumBoardLink($board_id, $page = NULL)
{
	global $config;

	$url = '';
	if($config['friendly_urls'])
		$url = BASE_URL . 'forum/board/' . (int)$board_id . (isset($page) ? '/' . $page : '');
	else
		$url = BASE_URL . '?subtopic=forum&action=show_board&id=' . (int)$board_id . (isset($page) ? '&page=' . $page : '');

	return $url;
}

function getPlayerLink($name, $generate = true)
{
	global $ots, $config;

	if(is_numeric($name))
	{
		$player = $ots->createObject('Player');
		$player->load(intval($name));
		if($player->isLoaded())
			$name = $player->getName();
	}

	$url = '';
	if($config['friendly_urls'])
		$url = BASE_URL . 'characters/' . urlencode($name);
	else
		$url = BASE_URL . '?subtopic=characters&name=' . urlencode($name);

	if(!$generate) return $url;
	return generateLink($url, $name);
}

function getHouseLink($name, $generate = true)
{
	global $db, $config;

	if(is_numeric($name))
	{
		$house = $db->query(
			'SELECT ' . $db->fieldName('name') .
			' FROM ' . $db->tableName('houses') .
			' WHERE ' . $db->fieldName('id') . ' = ' . (int)$name);
		if($house->rowCount() > 0)
			$name = $house->fetchColumn();
	}

	$url = '';
	if($config['friendly_urls'])
		$url = BASE_URL . 'houses/' . urlencode($name);
	else
		$url = BASE_URL . '?subtopic=houses&page=view&house=' . urlencode($name);

	if(!$generate) return $url;
	return generateLink($url, $name);
}

function getGuildLink($name, $generate = true)
{
	global $db, $config;

	if(is_numeric($name))
	{
		$guild = $db->query(
			'SELECT ' . $db->fieldName('name') .
			' FROM ' . $db->tableName('guilds') .
			' WHERE ' . $db->fieldName('id') . ' = ' . (int)$name);
		if($guild->rowCount() > 0)
			$name = $guild->fetchColumn();
	}

	$url = '';
	if($config['friendly_urls'])
		$url = BASE_URL . 'guilds/' . urlencode($name);
	else
		$url = BASE_URL . '?subtopic=guilds&action=show&guild=' . urlencode($name);

	if(!$generate) return $url;
	return generateLink($url, $name);
}

function getItemImage($id, $count = 1)
{
	$file_name = $id;
	if($count > 1)
		$file_name .= '-' . $count;

	global $config;
	return '<img src="images/items/' . $file_name . '.gif" width="32" height="32" border="0" alt=" ' .$id . '" />';
}

function getFlagImage($country)
{
	if(!isset($country[0]))
		return '';

	global $config;
	if(!isset($config['countries']))
		require(SYSTEM . 'countries.conf.php');

	return '<img src="images/flags/' . $country . '.gif" title="' . $config['countries'][$country]. '"/>';
}

/**
 * Performs a boolean check on the value.
 *
 * @param mixed $v Variable to check.
 * @return bool Value boolean status.
 */
function getBoolean($v)
{
	if(!$v || !isset($v[0])) return false;

	if(is_numeric($v))
		return intval($v) > 0;

	$v = strtolower($v);
	return $v == 'yes' || $v == 'true';
}

/**
 * Generates random string.
 *
 * @param int $length Length of the generated string.
 * @param bool $numeric Should numbers by used too?
 * @param bool $special Should special characters by used?
 * @return string Generated string.
 */
function generateRandomString($length, $lowCase = true, $upCase = false, $numeric = false, $special = false)
{
	$characters = '';
	if($lowCase)
		$characters .= 'abcdefghijklmnopqrstuxyvwz';

	if($upCase)
		$characters .= 'ABCDEFGHIJKLMNPQRSTUXYVWZ';

	if($numeric)
		$characters .= '123456789';

	if($special)
		$characters .= '+-*#&@!?';

	$characters_length = strlen($characters) - 1;
	if($characters_length <= 0) return '';

	$ret = '';
	for($i = 0; $i < $length; $i++)
		$ret .= $characters[mt_rand(0, $characters_length)];

    return $ret;
}

/**
 * Get forum sections
 *
 * @return array Forum sections.
 */
function getForumSections()
{
	global $db;
	$sections = $db->query('SELECT `id`, `name`, `description`, `closed` FROM ' . TABLE_PREFIX . 'forum_sections WHERE hidden != 1 ORDER BY `ordering`;');
	if($sections)
		return $sections->fetchAll();
	
	return array();
}

/**
 * Retrieves data from myaac database config.
 *
 * @param string $name Key.
 * @param string &$value Reference where requested data will be set to.
 * @return bool False if value was not found in table, otherwise true.
 */
function fetchDatabaseConfig($name, &$value)
{
	global $db;

	$query = $db->query('SELECT ' . $db->fieldName('value') . ' FROM ' . $db->tableName(TABLE_PREFIX . 'config') . ' WHERE ' . $db->fieldName('name') . ' = ' . $db->quote($name));
	if($query->rowCount() <= 0)
		return false;

	$value = $query->fetchColumn();
	return true;
}

/**
 * Retrieves data from database config.
 *
 * $param string $name Key.
 * @return string Requested data.
 */
function getDatabaseConfig($name)
{
	$value = '';
	fetchDatabaseConfig($name, $value);
	return $value;
}

/**
 * Register a new key pair in myaac database config.
 *
 * @param string $name Key name.
 * @param string $value Data to be associated with key.
 */
function registerDatabaseConfig($name, $value)
{
	global $db;
	$db->insert(TABLE_PREFIX . 'config', array('name' => $name, 'value' => $value));
}

/**
 * Updates a value in myaac database config.
 *
 * @param string $name Key name.
 * @param string $value New data.
 */
function updateDatabaseConfig($name, $value)
{
	global $db;
	$db->update(TABLE_PREFIX . 'config', array('value' => $value), array('name' => $name));
}

/**
 * Encrypt text using method specified in config.lua (encryptionType or passwordType)
 */
function encrypt($str)
{
	global $config;
	if(isset($config['database_salt'])) // otserv
		$str .= $config['database_salt'];

	$encryptionType = $config['database_encryption'];
	if(isset($encryptionType) && strtolower($encryptionType) != 'plain')
	{
		if($encryptionType == 'vahash')
			return base64_encode(hash('sha256', $str));

		return hash($encryptionType, $str);
	}

	return $str;
}

function tableExist($table)
{
	global $db, $config;
	$query = $db->query("SELECT `TABLE_NAME` FROM `information_schema`.`tables` WHERE `TABLE_SCHEMA` = " . $db->quote($config['database_name']) . " AND `TABLE_NAME` = " . $db->quote($table) . ";");
	return $query->rowCount() > 0;
}

function fieldExist($field, $table)
{
	global $db;
	if(count($db->query("SHOW COLUMNS FROM `" . $table . "` LIKE '" . $field . "'")->fetchAll()))
		return true;

	return false;
}

//delete player with name
function delete_player($name)
{
	global $db;
	$player = new OTS_Player();
	$player->find($name);
	if($player->isLoaded()) {
		try { $db->query("DELETE FROM player_skills WHERE player_id = '".$player->getId()."';"); } catch(PDOException $error) {}
		try { $db->query("DELETE FROM guild_invites WHERE player_id = '".$player->getId()."';"); } catch(PDOException $error) {}
		try { $db->query("DELETE FROM player_items WHERE player_id = '".$player->getId()."';"); } catch(PDOException $error) {}
		try { $db->query("DELETE FROM player_depotitems WHERE player_id = '".$player->getId()."';"); } catch(PDOException $error) {}
		try { $db->query("DELETE FROM player_spells WHERE player_id = '".$player->getId()."';"); } catch(PDOException $error) {}
		try { $db->query("DELETE FROM player_storage WHERE player_id = '".$player->getId()."';"); } catch(PDOException $error) {}
		try { $db->query("DELETE FROM player_viplist WHERE player_id = '".$player->getId()."';"); } catch(PDOException $error) {}
		try { $db->query("DELETE FROM player_deaths WHERE player_id = '".$player->getId()."';"); } catch(PDOException $error) {}
		try { $db->query("DELETE FROM player_deaths WHERE killed_by = '".$player->getId()."';"); } catch(PDOException $error) {}
		$rank = $player->getRank();
		if($rank->isLoaded()) {
			$guild = $rank->getGuild();
			if($guild->getOwner()->getId() == $player->getId()) {
				$rank_list = $guild->getGuildRanksList();
				if(count($rank_list) > 0) {
					$rank_list->orderBy('level');
					foreach($rank_list as $rank_in_guild) {
						$players_with_rank = $rank_in_guild->getPlayersList();
						$players_with_rank->orderBy('name');
						$players_with_rank_number = count($players_with_rank);
						if($players_with_rank_number > 0) {
							foreach($players_with_rank as $player_in_guild) {
								$player_in_guild->setRank();
								$player_in_guild->save();
							}
						}
						$rank_in_guild->delete();
					}
					$guild->delete();
				}
			}
		}
		$player->delete();
		return true;
	}
}

//delete guild with id
function delete_guild($id)
{
	$guild = new OTS_Guild();
	$guild->load($id);
	if(!$guild->isLoaded())
		return false;

	$rank_list = $guild->getGuildRanksList();
	if(count($rank_list) > 0) {
		$rank_list->orderBy('level');
		
		global $db, $ots;
		foreach($rank_list as $rank_in_guild) {
			if(fieldExist('rank_id', 'players'))
				$players_with_rank = $db->query('SELECT `id`, `rank_id` FROM `players` WHERE `rank_id` = ' . $rank->getId() . ' AND `deleted` = 0;');
			else if(tableExist('guild_members'))
				$players_with_rank = $db->query('SELECT `players`.`id` as `id`, `guild_members`.`rank_id` as `rank_id` FROM `players`, `guild_members` WHERE `guild_members`.`rank_id` = ' . $rank_in_guild->getId() . ' AND `players`.`id` = `guild_members`.`player_id` ORDER BY `name`;');
			else
				$players_with_rank = $db->query('SELECT `players`.`id` as `id`, `guild_membership`.`rank_id` as `rank_id` FROM `players`, `guild_membership` WHERE `guild_membership`.`rank_id` = ' . $rank_in_guild->getId() . ' AND `players`.`id` = `guild_membership`.`player_id` ORDER BY `name`;');

			$players_with_rank_number = $players_with_rank->rowCount();
			if($players_with_rank_number > 0) {
				foreach($players_with_rank as $result) {
					$player = $ots->createObject('Player');
					$player->load($result['id']);
					if(!$player->isLoaded())
						continue;
						
					$player->setRank();
					$player->save();
				}
			}
			$rank_in_guild->delete();
		}
	}

	$guild->delete();
	return true;
}

/**
 * Validate character name.
 * Name lenght must be 3-25 chars
 *
 * @param  string $name Name to check
 * @param  string $error Error description will be placed here
 * @return bool Is name valid?
 */
function check_name($name, &$error = '')
{
	if(!isset($name[0]))
	{
		$error = 'Please enter character name.';
		return false;
	}

	$length = strlen($name);
	if($length < 3)
	{
		$error = 'Character name is too short. Min. lenght <b>3</b> characters.';
		return false;
	}

	if($length > 25)
	{
		$error = 'Character name is too long. Max. lenght <b>25</b> characters.';
		return false;
	}

	if(strspn($name, "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM- [ ] '") != $length)
	{
		$error = 'Invalid name format. Use only A-Z.';
		return false;
	}

	return preg_match("/[A-z ']{1,25}/", $name);
}

/**
 * Validate account id
 * Id lenght must be 6-10 chars
 *
 * @param string $name Account name to check
 * @param string $error Error description will be placed here
 * @return bool Is account name valid?
 */
function check_account_id($id, &$error = '')
{
	if(!isset($id[0]))
	{
		$error = 'Please enter an account.';
		return false;
	}

	if(!check_number($id)) {
		$error = 'Invalid account name format. Use only numbers 0-9.';
		return false;
	}

	$length = strlen($id);
	if($length < 6)
	{
		$error = 'Account is too short (min. 6 chars).';
		return false;
	}

	if($length > 10)
	{
		$error = 'Account is too long (max. 10 chars).';
		return false;
	}

	return true;
}

/**
 * Validate account name
 * Name lenght must be 3-32 chars
 *
 * @param string $name Account name to check
 * @param string $error Error description will be placed here
 * @return bool Is account name valid?
 */
function check_account_name($name, &$error = '')
{
	if(!isset($name[0]))
	{
		$error = 'Please enter an account name.';
		return false;
	}

	$length = strlen($name);
	if($length < 3)
	{
		$error = 'Account name is too short (min. 3 chars).';
		return false;
	}

	if($length > 32)
	{
		$error = 'Account name is too long (max. 32 chars).';
		return false;
	}

	if(strspn($name, "QWERTYUIOPASDFGHJKLZXCVBNM0123456789") != $length)
	{
		$error = 'Invalid account name format. Use only A-Z and numbers 0-9.';
		return false;
	}

	return preg_match("/[A-Z0-9]/", $name);
}

//is it valid nick for new char?
function check_name_new_char($name, &$error = '')
{
	global $db, $config;

	$name_lower = strtolower($name);

	$first_words_blocked = array('admin ', 'administrator ', 'gm ', 'cm ', 'god ','tutor ', "'", '-');
	foreach($first_words_blocked as $word)
	{
		if($word == substr($name_lower, 0, strlen($word))) {
			$error = 'Your name contains blocked words.';
			return false;
		}
	}

	if(substr($name_lower, -1) == "'" || substr($name_lower, -1) == "-") {
		$error = 'Your name contains illegal characters.';
		return false;
	}

	if(substr($name_lower, 1, 1) == ' ') {
		$error = 'Your name contains illegal space.';
		return false;
	}

	if(substr($name_lower, -2, 1) == " ") {
		$error = 'Your name contains illegal space.';
		return false;
	}

	if(strtolower($config['lua']['serverName']) == $name_lower) {
		$error = 'Your name cannot be same as server name.';
		return false;
	}

	$names_blocked = array('admin', 'administrator', 'gm', 'cm', 'god', 'tutor');
	foreach($names_blocked as $word)
	{
		if($word == $name_lower) {
			$error = 'Your name contains blocked words.';
			return false;
		}
	}

	$words_blocked = array('admin', 'administrator', 'gamemaster', 'game master', 'game-master', "game'master", '--', "''","' ", " '", '- ', ' -', "-'", "'-", 'fuck', 'sux', 'suck', 'noob', 'tutor');
	foreach($words_blocked as $word)
	{
		if(!(strpos($name_lower, $word) === false)) {
			$error = 'Your name contains illegal words.';
			return false;
		}
	}

	$name_length = strlen($name_lower);
	for($i = 0; $i < $name_length; $i++)
	{
		if(isset($name_lower[$i]) && isset($name_lower[$i + 1]) && $name_lower[$i] == $name_lower[$i + 1] && isset($name_lower[$i + 2]) && $name_lower[$i] == $name_lower[$i + 2]) {
			$error = 'Your name is invalid.';
			return false;
		}
	}

	for($i = 0; $i < $name_length; $i++)
	{
		if(isset($name_lower[$i - 1]) && $name_lower[$i - 1] == ' ' && isset($name_lower[$i + 1]) && $name_lower[$i + 1] == ' ') {
			$error = 'Your name contains too many spaces.';
			return false;
		}
	}

	if(isset($config['monsters']))
	{
		if(in_array($name_lower, $config['monsters'])) {
			$error = 'Your name cannot contains monster name.';
			return false;
		}
	}

	$monsters = $db->query(
			'SELECT ' . $db->fieldName('name') .
			' FROM ' . $db->tableName(TABLE_PREFIX . 'monsters') .
			' WHERE ' . $db->fieldName('name') . ' LIKE ' . $db->quote($name_lower));
	if($monsters->rowCount() > 0) {
		$error = 'Your name cannot contains monster name.';
		return false;
	}

	$spells_name = $db->query(
			'SELECT ' . $db->fieldName('name') .
			' FROM ' . $db->tableName(TABLE_PREFIX . 'spells') .
			' WHERE ' . $db->fieldName('name') . ' LIKE ' . $db->quote($name_lower));
	if($spells_name->rowCount() > 0) {
		$error = 'Your name cannot contains spell name.';
		return false;
	}
	
	$spells_words = $db->query(
			'SELECT ' . $db->fieldName('words') .
			' FROM ' . $db->tableName(TABLE_PREFIX . 'spells') .
			' WHERE ' . $db->fieldName('words') . ' = ' . $db->quote($name_lower));
	if($spells_words->rowCount() > 0) {
		$error = 'Your name cannot contains spell name.';
		return false;
	}

	if(isset($config['npc']))
	{
		if(in_array($name_lower, $config['npc'])) {
			$error = 'Your name cannot contains NPC name.';
			return false;
		}
	}

	if(strspn($name, "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM- '") != $name_length) {
		$error = 'This name contains invalid letters, words or format. Please use only a-Z, - , \' and space.';
		return false;
	}

	if($name_length < 3 || $name_length  > 28) {
		$error = 'Your name cannot be shorter than 3 characters and longer than 28 characters.';
		return false;
	}
	
	
	if(!preg_match("/[A-z ']{3,28}/", $name)) {
		$error = 'Your name containst illegal characters.';
		return false;
	}

	return true;
}

function check_rank_name($name)
{
	if(strspn($name, "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789-[ ] ") != strlen($name))
		return false;

	return preg_match("/[A-z ]{1,32}/", $name);
}

function check_guild_name($name)
{
	if(strspn($name, "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789- ") != strlen($name))
		return false;

	return preg_match("/[A-z ]{3,32}/", $name);
}

function check_password($pass)
{
	if(strspn($pass, "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890") != strlen($pass))
		return false;

	return preg_match("/[A-z0-9]{7,32}/", $pass);
}

function check_mail($email)
{
	return preg_match('/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[A-z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/', $email);
	//return preg_match("/[A-z0-9._-]+@[A-z0-9-]+\.[A-z]{2,4}/", $email);
}

function check_number($number)
{
	return preg_match ("/^([0-9]+)$/", $number);
}

//################### DISPLAY FUNCTIONS #####################
//return shorter text (news ticker)
function short_text($text, $limit)
{
	if(strlen($text) > $limit)
		return substr($text, 0, strrpos(substr($text, 0, $limit), " ")).'...';

	return $text;
}

function news_place()
{
	global $template_path, $news_content;

	$news = '';
	if(PAGE == 'news')
	{
		//add tickers to site - without it tickers will not be showed
		if(isset($news_content))
			$news .= $news_content;

		//featured article
/*		$news .= '  <div id="featuredarticle" class="Box">
			<div class="Corner-tl" style="background-image:url('.$template_path.'/images/content/corner-tl.gif);"></div>
			<div class="Corner-tr" style="background-image:url('.$template_path.'/images/content/corner-tr.gif);"></div>
			<div class="Border_1" style="background-image:url('.$template_path.'/images/content/border-1.gif);"></div>
			<div class="BorderTitleText" style="background-image:url('.$template_path.'/images/content/title-background-green.gif);"></div>
			<img class="Title" src="'.$template_path.'/images/strings/headline-featuredarticle.gif" alt="Contentbox headline" />
			<div class="Border_2">
			  <div class="Border_3">
				<div class="BoxContent" style="background-image:url('.$template_path.'/images/content/scroll.gif);">
		<div id=\'TeaserThumbnail\'><img src="'.$template_path.'/images/news/features.jpg" width=150 height=100 border=0 alt="" /></div><div id=\'TeaserText\'><div style="position: relative; top: -2px; margin-bottom: 2px;" >
		<b>Tutaj wpisz tytul</b></div>
		tutaj wpisz tresc newsa<br>
		zdjecie laduje sie w <i>tibiacom/images/news/features.jpg</i><br>
		skad sie laduje mozesz zmienic linijke ponad komentarzem
		</div>        </div>
			  </div>
			</div>
			<div class="Border_1" style="background-image:url('.$template_path.'/images/content/border-1.gif);"></div>
			<div class="CornerWrapper-b"><div class="Corner-bl" style="background-image:url('.$template_path.'/images/content/corner-bl.gif);"></div></div>
			<div class="CornerWrapper-b"><div class="Corner-br" style="background-image:url('.$template_path.'/images/content/corner-br.gif);"></div></div>
		  </div>';*/
	}

	return $news;
}

function output_errors($errors)
{
	global $template_path;
?>
	<div class="SmallBox" >
		<div class="MessageContainer" >
		<div class="BoxFrameHorizontal" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-horizontal.gif);" /></div>
		<div class="BoxFrameEdgeLeftTop" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-edge.gif);" /></div>
		<div class="BoxFrameEdgeRightTop" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-edge.gif);" /></div>
		<div class="ErrorMessage" >
			<div class="BoxFrameVerticalLeft" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-vertical.gif);" />
		</div>
			<div class="BoxFrameVerticalRight" style="background-image:url(<?php echo $template_path; ?>/images/content/box-frame-vertical.gif);" /></div>
			<div class="AttentionSign" style="background-image:url(<?php echo $template_path; ?>/images/content/attentionsign.gif);" />
			</div><b>The Following Errors Have Occurred:</b><br/>
	<?php
	foreach($errors as $field => $message)
		echo $message . '<br/>';

	echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br/>';
}

/**
 * Template place holder
 *
 * Types: head_start, head_end, body_start, body_end, center_top
 *
 */
function template_place_holder($type)
{
	global $template_place_holders;
	$ret = '';

	if(array_key_exists($type, $template_place_holders) && is_array($template_place_holders[$type]))
		$ret = implode($template_place_holders[$type]);

	if($type == 'head_start')
		$ret .= template_header();
	elseif($type == 'body_end')
		$ret .= template_ga_code();

	return $ret;
}

/**
 * Returns <head> content to be used by templates.
 */
function template_header($is_admin = false)
{
	global $title_full, $config;
	$charset = isset($config['charset']) ? $config['charset'] : 'utf-8';

	$ret = '
	<meta charset="' . $charset . '">
	<meta http-equiv="content-language" content="' . $config['language'] . '" />
	<meta http-equiv="content-type" content="text/html; charset=' . $charset . '" />';
	if(!$is_admin)
		$ret .= '
	<title>' . $title_full . '</title>
	<base href="' . BASE_URL . '" />';

	$ret .= '
	<meta name="description" content="' . $config['meta_description'] . '" />
	<meta name="keywords" content="' . $config['meta_keywords'] . ', myaac, wodzaac" />
	<meta name="generator" content="MyAAC ' . MYAAC_VERSION . '" />
	<link rel="stylesheet" type="text/css" href="' . BASE_URL . 'tools/messages.css" />
	<script type="text/javascript" src="' . BASE_URL . 'tools/jquery.js"></script>
	<noscript>
		<div class="warning" style="text-align: center; font-size: 14px;">Your browser does not support JavaScript or its disabled!<br/>
			Please turn it on, or be aware that some features on this website will not work correctly.</div>
	</noscript>
';
	if(admin())
		$ret .= '<script type="text/javascript" src="' . BASE_URL . 'tools/tiny_mce/tiny_mce.js"></script>
	<!--script type="text/javascript" src="' . BASE_URL . 'tools/jquery.qtip.js" ></script>
	<script type="text/javascript" src="' . BASE_URL . 'tools/admin.js"></script-->
';
	if($config['recaptcha_enabled'])
		$ret .= "<script src='https://www.google.com/recaptcha/api.js'></script>";
	return $ret;
}

/**
 * Returns footer content to be used by templates.
 */
function template_footer()
{
	global $visitors, $config, $views_counter;
	$ret = '';
	if(admin())
		$ret .= generateLink(ADMIN_URL, 'Admin Panel', true);

	if($config['visitors_counter'])
	{
		$amount = $visitors->getAmountVisitors();
		$ret .= '<br/>Currently there ' . ($amount > 1 ? 'are' : 'is') . ' ' . $amount . ' visitor' . ($amount > 1 ? 's' : '') . '.';
	}

	if($config['views_counter'])
		$ret .= '<br/>Page has been viewed ' . $views_counter . ' times.';

	if(admin())
		$ret .= '<br/>Load time: ' . round(microtime(true) - START_TIME, 4) . ' seconds.';

	if(isset($config['footer'][0]))
		$ret .= '<br/>' . $config['footer'];

	// please respect my work and help spreading the word, thanks!
	return $ret . '<br/>' . base64_decode('UG93ZXJlZCBieSA8YSBocmVmPSJodHRwOi8vbXktYWFjLm9yZyIgdGFyZ2V0PSJfYmxhbmsiPk15QUFDLjwvYT4=');
}

function template_ga_code()
{
	global $config;
	if(!isset($config['google_analytics_id'][0]))
		return '';

	return '
<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push([\'_setAccount\', \'' . $config['google_analytics_id'] . '\']);
	_gaq.push([\'_trackPageview\']);

	(function() {
	var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
	ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
	var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
	})();
</script>';
}

function template_form()
{
	global $cache, $template_name;
	if($cache->enabled())
	{
		$tmp = '';
		if($cache->fetch('templates', $tmp)) {
			$templates = unserialize($tmp);
		}
		else
		{
			$templates = get_templates();
			$cache->set('templates', serialize($templates), 30);
		}
	}
	else
		$templates = get_templates();

	$options = '';
	foreach($templates as $key => $value)
		$options .= '<option ' . ($template_name == $value ? 'SELECTED' : '') . '>' . $value . '</option>';

	return 	'<form method="get" action="' . BASE_URL . '">
				<hidden name="subtopic" value="' . PAGE . '"/>
				<select name="template" onchange="this.form.submit()">' . $options . '</select>
			</form>';
}

function getStyle($i)
{
	global $config;
	return is_int($i / 2) ? $config['darkborder'] : $config['lightborder'];
}

$vowels = array("e", "y", "u", "i", "o", "a");
function getCreatureName($killer, $showStatus = false, $extendedInfo = false)
{
	global $vowels, $ots, $config;
	$str = "";
	if(is_numeric($killer))
	{
		$player = $ots->createObject('Player');
		$player->load($killer);
		if($player->isLoaded())
		{
			$str .= '<a href="' . getPlayerLink($player->getName(), false) . '">';
			if(!$showStatus)
				return $str.'<b>'.$player->getName().'</b></a>';

			$str .= '<font color="'.($player->isOnline() ? 'green' : 'red').'">' . $player->getName() . '</font></b></a>';
			if($extendedInfo) {
				$str .= '<br><small>'.$player->getLevel().' '.$config['vocations'][$player->getVocation()].'</small>';
			}
			return $str;
		}
	}
	else
	{
		if($killer == "-1")
			$players_rows .= "item or field";
		else
		{
			if(in_array(substr(strtolower($killer), 0, 1), $vowels))
				$players_rows .= "an ";
			else
				$players_rows .= "a ";
			$players_rows .= $killer;
		}
	}

	return $players_rows;
}

/**
 * Find skill name using skill id.
 *
 * @param int $skillId Skill id.
 * @param bool $suffix Should suffix also be added?
 * @return string Skill name or 'unknown' if not found.
 */
function getSkillName($skillId, $suffix = true)
{
	switch($skillId)
	{
		case POT::SKILL_FIST:
		{
			$tmp = 'fist';
			if($suffix)
				$tmp .= ' fighting';

			return $tmp;
		}
		case POT::SKILL_CLUB:
		{
			$tmp = 'club';
			if($suffix)
				$tmp .= ' fighting';

			return $tmp;
		}
		case POT::SKILL_SWORD:
		{
			$tmp = 'sword';
			if($suffix)
				$tmp .= ' fighting';

			return $tmp;
		}
		case POT::SKILL_AXE:
		{
			$tmp = 'axe';
			if($suffix)
				$tmp .= ' fighting';

			return $tmp;
		}
		case POT::SKILL_DIST:
		{
			$tmp = 'distance';
			if($suffix)
				$tmp .= ' fighting';

			return $tmp;
		}
		case POT::SKILL_SHIELD:
			return 'shielding';
		case POT::SKILL_FISH:
			return 'fishing';
		case POT::SKILL__MAGLEVEL:
			return 'magic level';
		case POT::SKILL__LEVEL:
			return 'level';
		default:
			break;
	}

	return 'unknown';
}

/**
 * Performs flag check on the current logged in user.
 * Table in database: accounts, field: website_flags
 *
 * @param int @flag Flag to be verified.
 * @return bool If user got flag.
 */
function hasFlag($flag) {
	global $logged, $logged_flags;
	return $logged && ($logged_flags & $flag) == $flag;
}
/**
 * Check if current logged user have got admin flag set.
 */
function admin() {
	return hasFlag(FLAG_ADMIN) || superAdmin();
}
/**
 * Check if current logged user have got super admin flag set.
 */
function superAdmin() {
	return hasFlag(FLAG_SUPER_ADMIN);
}

/**
 * Format experience according to its amount (natural/negative number).
 *
 * @param int $exp Experience amount.
 * @param bool $color Should result be colorized?
 * @return string Resulted message attached in <font> tag.
 */
function formatExperience($exp, $color = true)
{
	$ret = '';
	if($color)
	{
		$ret .= '<font';
		if($exp > 0)
			$ret .= ' color="green">';
		elseif($exp < 0)
			$ret .= ' color="red">';
		else
			$ret .= '>';
	}

	$ret .= '<b>' . ($exp > 0 ? '+' : '') . number_format($exp) . '</b>';
	if($color)
		$ret .= '</font>';

	return $ret;
}

function get_locales()
{
	$ret = array();

	$path = LOCALE;
	foreach(scandir($path) as $file)
	{
		if($file[0] != '.' && $file != '..' && is_dir($path . $file))
			$ret[] = $file;
	}

	return $ret;
}

function get_browser_languages()
{
	$ret = array();

	$acceptLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
	if(!isset($acceptLang[0]))
		return $ret;

	$languages = strtolower($acceptLang);
	// $languages = 'pl,en-us;q=0.7,en;q=0.3 ';
	// need to remove spaces from strings to avoid error
	$languages = str_replace(' ', '', $languages);

	foreach(explode(',', $languages) as $language_list)
		$ret[] .= substr($language_list, 0, 2);

	return $ret;
}

/**
 * Generates list of templates, according to templates/ dir.
 */
function get_templates()
{
	$ret = array();

	$path = TEMPLATES;
	foreach(scandir($path) as $file)
	{
		if($file[0] != '.' && $file != '..' && is_dir($path . $file))
			$ret[] = $file;
	}

	return $ret;
}

function getWorldName($id)
{
	global $config;
	foreach($config['worlds'] as $_id => $details)
	{
		if($id == $_id)
			return $details['name'];
	}

	return $config['lua']['serverName'];
}

/**
 * Mailing users.
 * $config['mail_enabled'] have to be enabled.
 *
 * @param string $to Recipient email address.
 * @param string $subject Subject of the message.
 * @param string $body Message body in html format.
 * @param string $altBody Alternative message body, plain text.
 * @return bool PHPMailer status returned (success/failure).
 */
function _mail($to, $subject, $body, $altBody = '', $add_html_tags = true)
{
	global $mailer, $config;
	if(!$mailer)
	{
		require(SYSTEM . 'libs/phpmailer/class.phpmailer.php');
		$mailer = new PHPMailer();
	}

	$signature_html = '';
	if(isset($config['mail_signature']['html']))
		$signature_html = $config['mail_signature']['html'];

	if($add_html_tags && isset($body[0]))
		$body = '<html><head></head><body>' . $body . '<br/><br/>' . $signature_html . '</body></html>';
	else
		$body .= '<br/><br/>' . $signature_html;

	if($config['smtp_enabled'])
	{
		$mailer->IsSMTP();
		$mailer->Host = $config['smtp_host'];
		$mailer->Port = (int)$config['smtp_port'];
		$mailer->SMTPAuth = $config['smtp_auth'];
		$mailer->Username = $config['smtp_user'];
		$mailer->Password = $config['smtp_pass'];
	}
	else
		$mailer->IsMail();

	$mailer->IsHTML(isset($body[0]) > 0);
	$mailer->From = $config['mail_address'];
	$mailer->Sender = $config['mail_address'];
	$mailer->CharSet = 'utf-8';
	$mailer->FromName = $config['lua']['serverName'];
	$mailer->Subject = $subject;
	$mailer->AddAddress($to);
	$mailer->Body = $body;

	$signature_plain = '';
	if(isset($config['mail_signature']['plain']))
		$signature_plain = $config['mail_signature']['plain'];

	if(isset($altBody[0]))
		$mailer->AltBody = $altBody . $signature_plain;

	return $mailer->Send();
}

function convert_bytes($size)
{
	$unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
	return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}

function log_append($file, $str)
{
	$f = fopen(LOGS . $file, 'a');
	fwrite($f, $str . PHP_EOL);
	fclose($f);
}

function load_config_lua($filename)
{
	global $config;
	
	$config_file = $filename;
	if(!@file_exists($config_file))
		die('ERROR: Cannot find ' . $filename . ' file.');

	$tempFile = @tempnam('/tmp', 'lua');
	$file = fopen($tempFile, 'w');
	if(!$file) die('Cannot load server config!');

	// TODO: new parser that will also load dofile() includes

	// strip lua comments to prevent parsing errors
	fwrite($file, preg_replace('/(-)(-)(.*)/', '', file_get_contents($config_file)));
	fclose($file);

	$result = array_merge(parse_ini_file($tempFile, true), isset($config['lua']) ? $config['lua'] : array());
	@unlink($tempFile);
	return $result;
}
?>
