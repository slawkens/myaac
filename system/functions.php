<?php
/**
 * Useful functions
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

use MyAAC\CsrfToken;
use MyAAC\Models\Config;
use MyAAC\Models\Guild;
use MyAAC\Models\House;
use MyAAC\Models\Pages;
use MyAAC\Models\Player;
use PHPMailer\PHPMailer\PHPMailer;
use Twig\Loader\ArrayLoader as Twig_ArrayLoader;

function message($message, $type, $return)
{
	if(IS_CLI) {
		if($return) {
			return $message;
		}

		echo $message;
		return true;
	}

	if($return) {
		// for install and admin pages use bootstrap classes
		return '<div class="' . ((defined('MYAAC_INSTALL') || defined('MYAAC_ADMIN')) ? 'alert alert-' : '') . $type . '" style="margin-bottom:10px;">' . $message . '</div>';
	}

	echo '<div class="' . ((defined('MYAAC_INSTALL') || defined('MYAAC_ADMIN')) ? 'alert alert-' : '') . $type . '" style="margin-bottom:10px;">' . $message . '</div>';
	return true;
}
function success($message, $return = false) {
	return message($message, 'success', $return);
}
function warning($message, $return = false) {
	return message($message, 'warning', $return);
}
function note($message, $return = false) {
	return info($message, $return);
}
function info($message, $return = false) {
	return message($message, 'info', $return);
}
function error($message, $return = false) {
	return message($message, ((defined('MYAAC_INSTALL') || defined('MYAAC_ADMIN')) ? 'danger' : 'error'), $return);
}

function longToIp($ip): string
{
	$exp = explode(".", long2ip($ip));
	return $exp[3].".".$exp[2].".".$exp[1].".".$exp[0];
}

function generateLink($url, $name, $blank = false): string {
	return '<a href="' . $url . '"' . ($blank ? ' target="_blank"' : '') . '>' . $name . '</a>';
}

function getFullLink($page, $name, $blank = false): string {
	return generateLink(getLink($page), $name, $blank);
}

function getLink($page, $action = null): string {
	return BASE_URL . (setting('core.friendly_urls') ? '' : 'index.php/') . $page . ($action ? '/' . $action : '');
}
function internalLayoutLink($page, $action = null): string {
	return getLink($page, $action);
}

function getForumThreadLink($thread_id, $page = NULL): string {
	return BASE_URL . (setting('core.friendly_urls') ? '' : 'index.php/') . 'forum/thread/' . (int)$thread_id . (isset($page) ? '/' . $page : '');
}

function getForumBoardLink($board_id, $page = NULL): string {
	return BASE_URL . (setting('core.friendly_urls') ? '' : 'index.php/') . 'forum/board/' . (int)$board_id . (isset($page) ? '/' . $page : '');
}

function getPlayerLink($name, $generate = true): string
{
	if(is_numeric($name))
	{
		$player = new OTS_Player();
		$player->load((int)$name);
		if($player->isLoaded())
			$name = $player->getName();
	}

	$url = BASE_URL . (setting('core.friendly_urls') ? '' : 'index.php/') . 'characters/' . urlencode($name);

	if(!$generate) return $url;
	return generateLink($url, $name);
}

function getMonsterLink($name, $generate = true): string
{
	$url = BASE_URL . (setting('core.friendly_urls') ? '' : 'index.php/') . 'creatures/' . urlencode($name);

	if(!$generate) return $url;
	return generateLink($url, $name);
}

function getHouseLink($name, $generate = true): string
{
	if(is_numeric($name))
	{
		$house = House::find(intval($name), ['name']);
		if ($house) {
			$name = $house->name;
		}
	}


	$url = BASE_URL . (setting('core.friendly_urls') ? '' : 'index.php/') . 'houses/' . urlencode($name);

	if(!$generate) return $url;
	return generateLink($url, $name);
}

function getGuildLink($name, $generate = true): string
{
	if(is_numeric($name)) {
		$guild = Guild::find(intval($name), ['name']);
		$name = $guild->name ?? 'Unknown';
	}

	$url = BASE_URL . (setting('core.friendly_urls') ? '' : 'index.php/') . 'guilds/' . urlencode($name);

	if(!$generate) return $url;
	return generateLink($url, $name);
}

function getItemNameById($id) {
	require_once LIBS . 'items.php';
	$item = Items::get($id);
	return !empty($item['name']) ? $item['name'] : '';
}

function getItemImage($id, $count = 1)
{
	$tooltip = '';

	$name = getItemNameById($id);
	if(!empty($name)) {
		$tooltip = ' class="item_image" title="' . $name . '"';
	}

	$file_name = $id;
	if($count > 1)
		$file_name .= '-' . $count;

	return '<img src="' . setting('core.item_images_url') . $file_name . setting('core.item_images_extension') . '"' . $tooltip . ' width="32" height="32" border="0" alt="' .$id . '" />';
}

function getItemRarity($chance) {
	if ($chance >= 21) {
		return "common";
	} elseif (between($chance, 8, 21)) {
		return "uncommon";
	} elseif (between($chance, 1.1, 8)) {
		return "semi rare";
	} elseif (between($chance, 0.4, 1.1)) {
		return "rare";
	} elseif (between($chance, 0.8, 0.4)) {
		return "very rare";
	} elseif ($chance <= 0.8) {
		return "extremely rare";
	}
	return '';
}

function getFlagImage($country): string
{
	if(!isset($country[0]))
		return '';

	global $config;
	if(!isset($config['countries']))
		require SYSTEM . 'countries.conf.php';

	if(!isset($config['countries'][$country])) {
		return '';
	}

	return '<img src="images/flags/' . $country . '.gif" title="' . $config['countries'][$country]. '"/>';
}

/**
 * Performs a boolean check on the value.
 *
 * @param mixed $v Variable to check.
 * @return bool Value boolean status.
 */
function getBoolean($v): bool
{
	if(is_bool($v)) {
		return $v;
	}

	if(is_numeric($v))
		return (int)$v > 0;

	$v = strtolower($v);
	return $v === 'yes' || $v === 'true';
}

/**
 * Generates random string.
 *
 * @param int $length Length of the generated string.
 * @param bool $lowCase Should lower case characters be used?
 * @param bool $upCase Should upper case characters be used?
 * @param bool $numeric Should numbers by used too?
 * @param bool $special Should special characters by used?
 * @return string Generated string.
 */
function generateRandomString($length, $lowCase = true, $upCase = false, $numeric = false, $special = false): string
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
function getForumBoards()
{
	global $db, $canEdit;
	$sections = $db->query('SELECT `id`, `name`, `description`, `closed`, `guild`, `access`' . ($canEdit ? ', `hidden`, `ordering`' : '') . ' FROM `' . TABLE_PREFIX . 'forum_boards` ' . (!$canEdit ? ' WHERE `hidden` != 1' : '') .
		' ORDER BY `ordering`;');
	if($sections)
		return $sections->fetchAll();

	return array();
}

// TODO:
// convert forum threads links from just forum/ID
// INTO: forum/thread-name-id, like in XenForo
//function convertForumThreadTitle($title) {
//	return str_replace(' ', '-', strtolower($title));
//}

/**
 * Retrieves data from myaac database config.
 *
 * @param string $name Key.
 * @param string &$value Reference where requested data will be set to.
 * @return bool False if value was not found in table, otherwise true.
 */
function fetchDatabaseConfig($name, &$value)
{
	$config = Config::select('value')->where('name', '=', $name)->first();
	if (!$config) {
		return false;
	}

	$value = $config->value;
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
	$value = null;
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
	Config::create(compact('name', 'value'));
}

/**
 * Updates a value in myaac database config.
 *
 * @param string $name Key name.
 * @param string $value New data.
 */
function updateDatabaseConfig($name, $value)
{
	Config::where('name', '=', $name)->update([
		'value' => $value
	]);
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
	if(isset($encryptionType) && strtolower($encryptionType) !== 'plain')
	{
		if($encryptionType === 'vahash')
			return base64_encode(hash('sha256', $str));

		return hash($encryptionType, $str);
	}

	return $str;
}

//delete player with name
function delete_player($name)
{
	// DB::beginTransaction();
	global $capsule;
	$player = Player::where(compact('name'))->first();
	if (!$player) {
		return false;
	}

	return false;
	// global $db;
	// $player = new OTS_Player();
	// $player->find($name);
	// if($player->isLoaded()) {
	// 	try { $db->exec("DELETE FROM player_skills WHERE player_id = '".$player->getId()."';"); } catch(PDOException $error) {}
	// 	try { $db->exec("DELETE FROM guild_invites WHERE player_id = '".$player->getId()."';"); } catch(PDOException $error) {}
	// 	try { $db->exec("DELETE FROM player_items WHERE player_id = '".$player->getId()."';"); } catch(PDOException $error) {}
	// 	try { $db->exec("DELETE FROM player_depotitems WHERE player_id = '".$player->getId()."';"); } catch(PDOException $error) {}
	// 	try { $db->exec("DELETE FROM player_spells WHERE player_id = '".$player->getId()."';"); } catch(PDOException $error) {}
	// 	try { $db->exec("DELETE FROM player_storage WHERE player_id = '".$player->getId()."';"); } catch(PDOException $error) {}
	// 	try { $db->exec("DELETE FROM player_viplist WHERE player_id = '".$player->getId()."';"); } catch(PDOException $error) {}
	// 	try { $db->exec("DELETE FROM player_deaths WHERE player_id = '".$player->getId()."';"); } catch(PDOException $error) {}
	// 	try { $db->exec("DELETE FROM player_deaths WHERE killed_by = '".$player->getId()."';"); } catch(PDOException $error) {}
	// 	$rank = $player->getRank();
	// 	if($rank->isLoaded()) {
	// 		$guild = $rank->getGuild();
	// 		if($guild->getOwner()->getId() == $player->getId()) {
	// 			$rank_list = $guild->getGuildRanksList();
	// 			if(count($rank_list) > 0) {
	// 				$rank_list->orderBy('level');
	// 				foreach($rank_list as $rank_in_guild) {
	// 					$players_with_rank = $rank_in_guild->getPlayersList();
	// 					$players_with_rank->orderBy('name');
	// 					$players_with_rank_number = count($players_with_rank);
	// 					if($players_with_rank_number > 0) {
	// 						foreach($players_with_rank as $player_in_guild) {
	// 							$player_in_guild->setRank();
	// 							$player_in_guild->save();
	// 						}
	// 					}
	// 					$rank_in_guild->delete();
	// 				}
	// 				$guild->delete();
	// 			}
	// 		}
	// 	}
	// 	$player->delete();
	// 	return true;
	// }

	// return false;
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
			if($db->hasTable('guild_members'))
				$players_with_rank = $db->query('SELECT `players`.`id` as `id`, `guild_members`.`rank_id` as `rank_id` FROM `players`, `guild_members` WHERE `guild_members`.`rank_id` = ' . $rank_in_guild->getId() . ' AND `players`.`id` = `guild_members`.`player_id` ORDER BY `name`;');
			else if($db->hasTable('guild_membership'))
				$players_with_rank = $db->query('SELECT `players`.`id` as `id`, `guild_membership`.`rank_id` as `rank_id` FROM `players`, `guild_membership` WHERE `guild_membership`.`rank_id` = ' . $rank_in_guild->getId() . ' AND `players`.`id` = `guild_membership`.`player_id` ORDER BY `name`;');
			else
				$players_with_rank = $db->query('SELECT `id`, `rank_id` FROM `players` WHERE `rank_id` = ' . $rank_in_guild->getId() . ' AND `deleted` = 0;');

			$players_with_rank_number = $players_with_rank->rowCount();
			if($players_with_rank_number > 0) {
				foreach($players_with_rank as $result) {
					$player = new OTS_Player();
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

//################### DISPLAY FUNCTIONS #####################
//return shorter text (news ticker)
function short_text($text, $limit)
{
	if(strlen($text) > $limit)
		return substr($text, 0, strrpos(substr($text, 0, $limit), " ")).'...';

	return $text;
}

function tickers()
{
	global $tickers_content, $featured_article;

	if(PAGE === 'news') {
		if(isset($tickers_content))
			return $tickers_content . $featured_article;
	}

	return '';
}

/**
 * Template place holder
 *
 * Types: head_start, head_end, body_start, body_end, center_top
 *
 */
function template_place_holder($type): string
{
	global $twig, $template_place_holders, $debugBar;
	$ret = '';

	if (isset($debugBar)) {
		$debugBarRenderer = $debugBar->getJavascriptRenderer();
	}

	if(array_key_exists($type, $template_place_holders) && is_array($template_place_holders[$type]))
		$ret = implode($template_place_holders[$type]);

	if($type === 'head_start') {
		$ret .= template_header();
		if (isset($debugBar)) {
			$ret .= $debugBarRenderer->renderHead();
		}
	}
	elseif ($type === 'body_start') {
		$ret .= $twig->render('browsehappy.html.twig');
	}
	elseif($type === 'body_end') {
		$ret .= template_ga_code();
		if (isset($debugBar)) {
			$ret .= $debugBarRenderer->render();
		}
	}

	return $ret;
}

/**
 * Returns <head> content to be used by templates.
 */
function template_header($is_admin = false): string
{
	global $title_full, $twig;
	$charset = setting('core.charset') ?? 'utf-8';

	return $twig->render('templates.header.html.twig',
		[
			'charset' => $charset,
			'title' => $title_full,
			'is_admin' => $is_admin
		]
	);
}

/**
 * Returns footer content to be used by templates.
 */
function template_footer(): string
{
	global $views_counter;
	$ret = '';
	if(admin()) {
		$ret .= generateLink(ADMIN_URL, 'Admin Panel', true);
	}

	if(setting('core.visitors_counter')) {
		global $visitors;
		$amount = $visitors->getAmountVisitors();
		$ret .= '<br/>Currently there ' . ($amount > 1 ? 'are' : 'is') . ' ' . $amount . ' visitor' . ($amount > 1 ? 's' : '') . '.';
	}

	if(setting('core.views_counter')) {
		$ret .= '<br/>Page has been viewed ' . $views_counter . ' times.';
	}

	if(setting('core.footer_load_time')) {
		$ret .= '<br/>Load time: ' . round(microtime(true) - START_TIME, 4) . ' seconds.';
	}

	$settingFooter = setting('core.footer');
	if(isset($settingFooter[0])) {
		$ret .= '<br/>' . $settingFooter;
	}

	// please respect my work and help spreading the word, thanks!
	return $ret . '<br/>' . base64_decode('UG93ZXJlZCBieSA8YSBocmVmPSJodHRwOi8vbXktYWFjLm9yZyIgdGFyZ2V0PSJfYmxhbmsiPk15QUFDLjwvYT4=');
}

function template_ga_code()
{
	global $twig;
	if(!isset(setting('core.google_analytics_id')[0]))
		return '';

	return $twig->render('google_analytics.html.twig');
}

function template_form()
{
	global $template_name;

	$cache = Cache::getInstance();
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

	global $twig;
	return $twig->render('forms.change_template.html.twig', ['options' => $options]);
}

function getStyle($i)
{
	global $config;
	return is_int($i / 2) ? $config['darkborder'] : $config['lightborder'];
}

$vowels = array('e', 'y', 'u', 'i', 'o', 'a');
function getCreatureName($killer, $showStatus = false, $extendedInfo = false)
{
	global $vowels, $ots, $config;
	$str = "";
	$players_rows = '';

	if(is_numeric($killer))
	{
		$player = new OTS_Player();
		$player->load($killer);
		if($player->isLoaded())
		{
			$str .= '<a href="' . getPlayerLink($player->getName(), false) . '">';
			if(!$showStatus)
				return $str.'<b>'.$player->getName().'</b></a>';

			$str .= '<span style="color: '.($player->isOnline() ? 'green' : 'red').'">' . $player->getName() . '</span></b></a>';
			if($extendedInfo) {
				$str .= '<br><small>'.$player->getLevel().' '.$player->getVocationName().'</small>';
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
			if(in_array(strtolower($killer[0]), $vowels))
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
	return ($logged && ($logged_flags & $flag) == $flag);
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
 * @return string Resulted message attached in <span> tag.
 */
function formatExperience($exp, $color = true)
{
	$ret = '';
	if($color)
	{
		$ret .= '<span';
		if($exp != 0) {
			$ret .= ' style="color: ' . ($exp > 0 ? 'green' : 'red') . '">';
		}
		else {
			$ret .= '>';
		}
	}

	$ret .= '<b>' . ($exp > 0 ? '+' : '') . number_format($exp) . '</b>';
	if($color)
		$ret .= '</span>';

	return $ret;
}

function get_locales()
{
	$ret = array();

	$path = LOCALE;
	foreach(scandir($path, 0) as $file)
	{
		if($file[0] != '.' && $file != '..' && is_dir($path . $file))
			$ret[] = $file;
	}

	return $ret;
}

function get_browser_languages()
{
	$ret = array();

	if(empty($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		return $ret;

	$acceptLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
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
	foreach(scandir($path, 0) as $file)
	{
		if($file[0] !== '.' && $file !== '..' && is_dir($path . $file))
			$ret[] = $file;
	}

	return $ret;
}

/**
 * Generates list of installed plugins
 * @return array $plugins
 */
function get_plugins($disabled = false): array
{
	$ret = [];

	$path = PLUGINS;
	foreach(scandir($path, SCANDIR_SORT_ASCENDING) as $file) {
		$file_ext = pathinfo($file, PATHINFO_EXTENSION);
		$file_name = pathinfo($file, PATHINFO_FILENAME);
		if ($file === '.' || $file === '..' || $file === 'example.json' || $file_ext !== 'json' || is_dir($path . $file)) {
			continue;
		}

		if (!$disabled && strpos($file, 'disabled.') !== false) {
			continue;
		}

		$ret[] = str_replace('.json', '', $file_name);
	}

	return $ret;
}
function getWorldName($id)
{
	global $config;
	if(isset($config['worlds'][$id]))
		return $config['worlds'][$id];

	return $config['lua']['serverName'];
}

/**
 * Mailing users.
 * Mailing has to be enabled in settings (in Admin Panel).
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

	if (!setting('core.mail_enabled')) {
		log_append('mailer-error.log', '_mail() function has been used, but Mail Support is disabled.');
		return false;
	}

	if(!$mailer)
	{
		$mailer = new PHPMailer();
		//$mailer->setLanguage('en', LIBS . 'phpmailer/language/');
	}
	else {
		$mailer->clearAllRecipients();
	}

	$signature_html = setting('core.mail_signature_html');
	if($add_html_tags && isset($body[0]))
		$tmp_body = '<html><head></head><body>' . $body . '<br/><br/>' . $signature_html . '</body></html>';
	else
		$tmp_body = $body . '<br/><br/>' . $signature_html;

	$mailOption = setting('core.mail_option');
	if($mailOption == MAIL_SMTP)
	{
		$mailer->isSMTP();
		$mailer->Host = setting('core.smtp_host');
		$mailer->Port = setting('core.smtp_port');
		$mailer->SMTPAuth = setting('core.smtp_auth');
		$mailer->Username = setting('core.smtp_user');
		$mailer->Password = setting('core.smtp_pass');

		$security = setting('core.smtp_security');

		$tmp = '';
		if ($security === SMTP_SECURITY_SSL) {
			$tmp = 'ssl';
		}
		else if ($security == SMTP_SECURITY_TLS) {
			$tmp = 'tls';
		}

		$mailer->SMTPSecure = $tmp;
	}
	else {
		$mailer->isMail();
	}

	$mailer->isHTML(isset($body[0]) > 0);
	$mailer->From = setting('core.mail_address');
	$mailer->Sender = setting('core.mail_address');
	$mailer->CharSet = 'utf-8';
	$mailer->FromName = $config['lua']['serverName'];
	$mailer->Subject = $subject;
	$mailer->addAddress($to);
	$mailer->Body = $tmp_body;

	if(setting('core.smtp_debug')) {
		$mailer->SMTPDebug = 2;
		$mailer->Debugoutput = 'echo';
	}

	$signature_plain = setting('core.mail_signature_plain');
	if(isset($altBody[0])) {
		$mailer->AltBody = $altBody . $signature_plain;
	}
	else { // automatically generate plain html
		$mailer->AltBody = strip_tags(preg_replace('/<a(.*)href="([^"]*)"(.*)>/','$2', $body)) . "\n" . $signature_plain;
	}

	ob_start();
	if(!$mailer->Send()) {
		log_append('mailer-error.log', PHP_EOL . $mailer->ErrorInfo . PHP_EOL . ob_get_clean());
		return false;
	}

	ob_end_clean();
	return true;
}

function convert_bytes($size)
{
	$unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
	return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}

function log_append($file, $str, array $params = [])
{
	if(count($params) > 0) {
		$str .= print_r($params, true);
	}

	$f = fopen(LOGS . $file, 'ab');
	fwrite($f, '[' . date(DateTime::RFC1123) . '] ' . $str . PHP_EOL);
	fclose($f);
}

function load_config_lua($filename)
{
	global $config;

	$config_file = $filename;
	if(!@file_exists($config_file))
	{
		log_append('error.log', '[load_config_file] Fatal error: Cannot load config.lua (' . $filename . ').');
		throw new RuntimeException('ERROR: Cannot find ' . $filename . ' file.');
	}

	$result = array();
	$config_string = str_replace(array("\r\n", "\r"), "\n", file_get_contents($filename));
	$lines = explode("\n", $config_string);
	if(count($lines) > 0) {
		foreach($lines as $ln => $line)
		{
			$line = trim($line);
			if(@$line[0] === '{' || @$line[0] === '}') {
				// arrays are not supported yet
				// just ignore the error
				continue;
			}
			$tmp_exp = explode('=', $line, 2);
			if(strpos($line, 'dofile') !== false)
			{
				$delimiter = '"';
				if(strpos($line, $delimiter) === false)
					$delimiter = "'";

				$tmp = explode($delimiter, $line);
				$result = array_merge($result, load_config_lua($config['server_path'] . $tmp[1]));
			}
			else if(count($tmp_exp) >= 2)
			{
				$key = trim($tmp_exp[0]);
				if(0 !== strpos($key, '--'))
				{
					$value = trim($tmp_exp[1]);
					if(strpos($value, '--') !== false) {// found some deep comment
						$value = preg_replace('/--.*$/i', '', $value);
					}

					if(is_numeric($value))
						$result[$key] = (float) $value;
					elseif(in_array(@$value[0], array("'", '"')) && in_array(@$value[strlen($value) - 1], array("'", '"')))
						$result[$key] = (string) substr(substr($value, 1), 0, -1);
					elseif(in_array($value, array('true', 'false')))
						$result[$key] = ($value === 'true') ? true : false;
					elseif(@$value[0] === '{') {
						// arrays are not supported yet
						// just ignore the error
						continue;
					}
					else
					{
						foreach($result as $tmp_key => $tmp_value) // load values definied by other keys, like: dailyFragsToBlackSkull = dailyFragsToRedSkull
							$value = str_replace($tmp_key, $tmp_value, $value);
						$ret = @eval("return $value;");
						if((string) $ret == '' && trim($value) !== '""') // = parser error
						{
							throw new RuntimeException('ERROR: Loading config.lua file. Line <b>' . ($ln + 1) . '</b> of LUA config file is not valid [key: <b>' . $key . '</b>]');
						}
						$result[$key] = $ret;
					}
				}
			}
		}
	}

	$result = array_merge($result, isset($config['lua']) ? $config['lua'] : array());
	return $result;
}

function str_replace_first($search, $replace, $subject) {
	$pos = strpos($subject, $search);
	if ($pos !== false) {
		return substr_replace($subject, $replace, $pos, strlen($search));
	}

	return $subject;
}

function get_browser_real_ip() {
	if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
		$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
	}

	if(isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']))
		return $_SERVER['REMOTE_ADDR'];
	else if(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']))
		return $_SERVER['HTTP_CLIENT_IP'];
	else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		return $_SERVER['HTTP_X_FORWARDED_FOR'];

	return '0';
}
function setSession($key, $data) {
	$_SESSION[setting('core.session_prefix') . $key] = $data;
}
function getSession($key) {
	$key = setting('core.session_prefix') . $key;
	return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
}
function unsetSession($key) {
	unset($_SESSION[setting('core.session_prefix') . $key]);
}

function csrf(): void {
	CsrfToken::create();
}

function csrfToken(): string {
	return CsrfToken::get();
}

function isValidToken(): bool {
	$token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
	return ($_SERVER['REQUEST_METHOD'] !== 'POST' || (isset($token) && CsrfToken::isValid($token)));
}

function csrfProtect(): void
{
	if (!isValidToken()) {
		$lastUri = BASE_URL . str_replace_first('/', '', getSession('last_uri'));
		echo 'Request has been cancelled due to security reasons - token is invalid. Go <a href="' . $lastUri . '">back</a>';
		exit();
	}
}

function getTopPlayers($limit = 5) {
	global $db;

	$cache = Cache::getInstance();
	if($cache->enabled()) {
		$tmp = '';
		if($cache->fetch('top_' . $limit . '_level', $tmp)) {
			$players = unserialize($tmp);
		}
	}

	if (!isset($players)) {
		$columns = [
			'id', 'name', 'level', 'vocation', 'experience',
			'looktype', 'lookhead', 'lookbody', 'looklegs', 'lookfeet'
		];

		if ($db->hasColumn('players', 'lookaddons')) {
			$columns[] = 'lookaddons';
		}

		if ($db->hasColumn('players', 'online')) {
			$columns[] = 'online';
		}

		$players = Player::query()
			->select($columns)
			->withOnlineStatus()
			->notDeleted()
			->where('group_id', '<', setting('core.highscores_groups_hidden'))
			->whereNotIn('id', setting('core.highscores_ids_hidden'))
			->where('account_id', '!=', 1)
			->orderByDesc('experience')
			->limit($limit)
			->get()
			->map(function ($e, $i) {
				$row = $e->toArray();
				$row['online'] = $e->online_status;
				$row['rank'] = $i + 1;

				unset($row['online_table']);

				return $row;
			})->toArray();

		if($cache->enabled()) {
			$cache->set('top_' . $limit . '_level', serialize($players), 120);
		}
	}

	return $players;
}

function deleteDirectory($dir, $ignore = array(), $contentOnly = false) {
	if(!file_exists($dir)) {
		return true;
	}

	if(!is_dir($dir)) {
		return unlink($dir);
	}

	foreach(scandir($dir, 0) as $item) {
		if($item === '.' || $item === '..' || in_array($item, $ignore, true)) {
			continue;
		}

		if(!in_array($item, $ignore, true) && !deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
			return false;
		}
	}

	if($contentOnly) {
		return true;
	}

	return rmdir($dir);
}

function config($key) {
	global $config;
	if (is_array($key)) {
		if (is_null($key[1])) {
			unset($config[$key[0]]);
		}
		return $config[$key[0]] = $key[1];
	}

	return @$config[$key];
}

function configLua($key) {
	global $config;
	if (is_array($key)) {
		return $config['lua'][$key[0]] = $key[1];
	}

	return @$config['lua'][$key];
}

function setting($key)
{
	$settings = Settings::getInstance();

	if (is_array($key)) {
		if (is_null($key[1])) {
			unset($settings[$key[0]]);
		}

		return $settings[$key[0]] = $key[1];
	}

	return $settings[$key]['value'];
}

function clearCache()
{
	require_once LIBS . 'news.php';
	News::clearCache();

	$cache = Cache::getInstance();

	if($cache->enabled()) {
		$tmp = '';

		if ($cache->fetch('status', $tmp))
			$cache->delete('status');

		if ($cache->fetch('templates', $tmp))
			$cache->delete('templates');

		if ($cache->fetch('config_lua', $tmp))
			$cache->delete('config_lua');

		if ($cache->fetch('vocations', $tmp))
			$cache->delete('vocations');

		if ($cache->fetch('towns', $tmp))
			$cache->delete('towns');

		if ($cache->fetch('groups', $tmp))
			$cache->delete('groups');

		if ($cache->fetch('visitors', $tmp))
			$cache->delete('visitors');

		if ($cache->fetch('views_counter', $tmp))
			$cache->delete('views_counter');

		if ($cache->fetch('failed_logins', $tmp))
			$cache->delete('failed_logins');

		foreach (get_templates() as $template) {
			if ($cache->fetch('template_ini_' . $template, $tmp)) {
				$cache->delete('template_ini_' . $template);
			}
		}

		if ($cache->fetch('template_menus', $tmp)) {
			$cache->delete('template_menus');
		}
		if ($cache->fetch('database_tables', $tmp)) {
			$cache->delete('database_tables');
		}
		if ($cache->fetch('database_columns', $tmp)) {
			$cache->delete('database_columns');
		}
		if ($cache->fetch('database_checksum', $tmp)) {
			$cache->delete('database_checksum');
		}
		if ($cache->fetch('last_kills', $tmp)) {
			$cache->delete('last_kills');
		}

		if ($cache->fetch('hooks', $tmp)) {
			$cache->delete('hooks');
		}
		if ($cache->fetch('plugins_hooks', $tmp)) {
			$cache->delete('plugins_hooks');
		}
		if ($cache->fetch('plugins_routes', $tmp)) {
			$cache->delete('plugins_routes');
		}
	}

	deleteDirectory(CACHE . 'signatures', ['index.html'], true);
	deleteDirectory(CACHE . 'twig', ['index.html'], true);
	deleteDirectory(CACHE . 'plugins', ['index.html'], true);
	deleteDirectory(CACHE, ['signatures', 'twig', 'plugins', 'index.html'], true);

	// routes cache
	$routeCacheFile = CACHE . 'route.cache';
	if (file_exists($routeCacheFile)) {
		unlink($routeCacheFile);
	}

	return true;
}

function getCustomPageInfo($name)
{
	global $logged_access;
	$page = Pages::isPublic()
		->where('name', 'LIKE', $name)
		->where('access', '<=', $logged_access)
		->first();

	if (!$page) {
		return null;
	}

	return $page->toArray();
}
function getCustomPage($name, &$success): string
{
	global $twig, $title, $ignore;

	$success = false;
	$content = '';
	$page = getCustomPageInfo($name);

	if($page) // found page
	{
		$success = $ignore = true;
		$title = $page['title'];

		if($page['php'] == '1') // execute it as php code
		{
			$tmp = substr($page['body'], 0, 10);
			if(($pos = strpos($tmp, '<?php')) !== false) {
				$tmp = preg_replace('/<\?php/', '', $page['body'], 1);
			}
			else if(($pos = strpos($tmp, '<?')) !== false) {
				$tmp = preg_replace('/<\?/', '', $page['body'], 1);
			}
			else
				$tmp = $page['body'];

			$php_errors = array();
			function error_handler($errno, $errstr) {
				global $php_errors;
				$php_errors[] = array('errno' => $errno, 'errstr' => $errstr);
			}
			set_error_handler('error_handler');

			global $config;
			if(setting('core.backward_support')) {
				global $SQL, $main_content, $subtopic;
			}

			ob_start();
			eval($tmp);
			$content .= ob_get_contents();
			ob_end_clean();

			restore_error_handler();
			if(isset($php_errors[0]) && superAdmin()) {
				var_dump($php_errors);
			}
		}
		else {
			$oldLoader = $twig->getLoader();

			$twig_loader_array = new Twig_ArrayLoader(array(
				'content.html' => $page['body']
			));

			$twig->setLoader($twig_loader_array);

			$content .= $twig->render('content.html');

			$twig->setLoader($oldLoader);
		}
	}

	return $content;
}

function getBanReason($reasonId)
{
	switch($reasonId)
	{
		case 0:
			return "Offensive Name";
		case 1:
			return "Invalid Name Format";
		case 2:
			return "Unsuitable Name";
		case 3:
			return "Name Inciting Rule Violation";
		case 4:
			return "Offensive Statement";
		case 5:
			return "Spamming";
		case 6:
			return "Illegal Advertising";
		case 7:
			return "Off-Topic Public Statement";
		case 8:
			return "Non-English Public Statement";
		case 9:
			return "Inciting Rule Violation";
		case 10:
			return "Bug Abuse";
		case 11:
			return "Game Weakness Abuse";
		case 12:
			return "Using Unofficial Software to Play";
		case 13:
			return "Hacking";
		case 14:
			return "Multi-Clienting";
		case 15:
			return "Account Trading or Sharing";
		case 16:
			return "Threatening Gamemaster";
		case 17:
			return "Pretending to Have Influence on Rule Enforcement";
		case 18:
			return "False Report to Gamemaster";
		case 19:
			return "Destructive Behaviour";
		case 20:
			return "Excessive Unjustified Player Killing";
		case 21:
			return "Invalid Payment";
		case 22:
			return "Spoiling Auction";
	}

	return "Unknown Reason";
}

function getBanType($typeId)
{
	switch($typeId)
	{
		case 1:
			return "IP Banishment";
		case 2:
			return "Namelock";
		case 3:
			return "Banishment";
		case 4:
			return "Notation";
		case 5:
			return "Deletion";
	}

	return "Unknown Type";
}

function getChangelogType($v)
{
	switch($v) {
		case 1:
			return 'added';
		case 2:
			return 'removed';
		case 3:
			return 'changed';
		case 4:
			return 'fixed';
	}

	return 'unknown';
}

function getChangelogWhere($v)
{
	switch($v) {
		case 1:
			return 'server';
		case 2:
			return 'website';
	}

	return 'unknown';
}

function getPlayerNameByAccountId($id)
{
	if (!is_numeric($id)) {
		return '';
	}

	$account = \MyAAC\Models\Account::find(intval($id), ['id']);
	if ($account) {
		$player = \MyAAC\Models\Player::where('account_id', $account->id)->orderByDesc('lastlogin')->select('name')->first();
		if (!$player) {
			return '';
		}
		return $player->name;
	}

	return '';
}

function getPlayerNameByAccount($account) {
	if (is_numeric($account)) {
		return getPlayerNameByAccountId($account);
	}

	return '';
}

function getPlayerNameById($id)
{
	if (!is_numeric($id)) {
		return '';
	}

	$player = \MyAAC\Models\Player::find((int)$id, ['name']);
	if ($player) {
		return $player->name;
	}

	return '';
}

function echo_success($message)
{
	echo '<div class="col-12 alert alert-success mb-2">' . $message . '</div>';
}

function echo_error($message)
{
	global $error;
	echo '<div class="col-12 alert alert-danger mb-2">' . $message . '</div>';
	$error = true;
}

function verify_number($number, $name, $max_length)
{
	if (!Validator::number($number))
		echo_error($name . ' can contain only numbers.');

	$number_length = strlen($number);
	if ($number_length <= 0 || $number_length > $max_length)
		echo_error($name . ' cannot be longer than ' . $max_length . ' digits.');
}

function Outfits_loadfromXML()
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

 function Outfit_parseNode($node) {
	$looktype = (int)$node->getAttribute('looktype');
	$type = (int)$node->getAttribute('type');
	$lookname = $node->getAttribute('name');
	$premium = $node->getAttribute('premium');
	$unlocked = $node->getAttribute('unlocked');
	$enabled = $node->getAttribute('enabled');
	return array('id' => $looktype, 'type' => $type, 'name' => $lookname, 'premium' => $premium, 'unlocked' => $unlocked, 'enabled' => $enabled);
}

function Mounts_loadfromXML()
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

 function Mount_parseNode($node) {
	$id = (int)$node->getAttribute('id');
	$clientid = (int)$node->getAttribute('clientid');
	$name = $node->getAttribute('name');
	$speed = (int)$node->getAttribute('speed');
	$premium = $node->getAttribute('premium');
	$type = $node->getAttribute('type');
	return array('id' => $id, 'clientid' => $clientid, 'name' => $name, 'speed' => $speed, 'premium' => $premium, 'type' => $type);
}

function left($str, $length) {
	return substr($str, 0, $length);
}

function right($str, $length) {
	return substr($str, -$length);
}

function getCreatureImgPath($creature){
	$creature_path = setting('core.monsters_images_url');
	$creature_gfx_name = trim(strtolower($creature)) . setting('core.monsters_images_extension');
	if (!file_exists($creature_path . $creature_gfx_name)) {
		$creature_gfx_name = str_replace(" ", "", $creature_gfx_name);
		if (file_exists($creature_path . $creature_gfx_name)) {
			return $creature_path . $creature_gfx_name;
		} else {
			return $creature_path . 'nophoto.png';
		}
	} else {
		return $creature_path . $creature_gfx_name;
	}
}

function between($x, $lim1, $lim2) {
	if ($lim1 < $lim2) {
		$lower = $lim1; $upper = $lim2;
	}
	else {
		$lower = $lim2; $upper = $lim1;
	}
	return (($x >= $lower) && ($x <= $upper));
}

function truncate($string, $length)
{
	if (strlen($string) > $length) {
		$string = substr($string, 0, $length) . '...';
	}
	return $string;
}

function getAccountLoginByLabel()
{
	$ret = '';
	if (config('account_login_by_email')) {
		$ret = 'Email Address';
		if (config('account_login_by_email_fallback')) {
			$ret .= ' or ';
		}
	}

	if (!config('account_login_by_email') || config('account_login_by_email_fallback')) {
		$ret .= 'Account ' . (USE_ACCOUNT_NAME ? 'Name' : 'Number');
	}

	return $ret;
}

function camelCaseToUnderscore($input)
{
	return ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $input)), '_');
}

function removeIfFirstSlash(&$text) {
	if(strpos($text, '/') === 0) {
		$text = str_replace_first('/', '', $text);
	}
};

function escapeHtml($html) {
	return htmlentities($html, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function getGuildNameById($id)
{
	$guild = Guild::where('id', intval($id))->select('name')->first();
	if ($guild) {
		return $guild->name;
	}

	return false;
}

function getGuildLogoById($id)
{
	$logo = 'default.gif';

	$guild = Guild::where('id', intval($id))->select('logo_name')->first();
	if ($guild) {
		$guildLogo = $guild->logo_name;

		if (!empty($guildLogo) && file_exists(GUILD_IMAGES_DIR . $guildLogo)) {
			$logo = $guildLogo;
		}
	}

	return BASE_URL . GUILD_IMAGES_DIR . $logo;
}

function displayErrorBoxWithBackButton($errors, $action = null) {
	global $twig;
	$twig->display('error_box.html.twig', ['errors' => $errors]);
	$twig->display('account.back_button.html.twig', [
		'action' => $action ?: getLink('')
	]);
}

// validator functions
require_once LIBS . 'validator.php';
require_once SYSTEM . 'compat/base.php';

// custom functions
require SYSTEM . 'functions_custom.php';
