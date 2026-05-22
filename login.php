<?php

use MyAAC\Models\BoostedCreature;
use MyAAC\Models\PlayerOnline;
use MyAAC\Models\Account;
use MyAAC\Models\Player;
use MyAAC\Cache\Cache;
use MyAAC\RateLimit;

require_once 'common.php';
require_once SYSTEM . 'functions.php';
require_once SYSTEM . 'init.php';
require_once SYSTEM . 'status.php';

# error function
function sendError($message, $code = 3){
	$ret = [];
	$ret['errorCode'] = $code;
	$ret['errorMessage'] = $message;
	die(json_encode($ret));
}

# event schedule function
function parseEvent($table1, $date, $table2)
{
	if ($table1) {
		if ($date) {
			if ($table2) {
				$date = $table1->getAttribute('startdate');
				return date_create("{$date}")->format('U');
			} else {
				$date = $table1->getAttribute('enddate');
				return date_create("{$date}")->format('U');
			}
		} else {
			foreach($table1 as $attr) {
				if ($attr) {
					return $attr->getAttribute($table2);
				}
			}
		}
	}
	return 'error';
}

function parseEventSchedulerJsonDate($date, $hour, $endDate = false)
{
	$date = trim((string)$date);
	$hour = trim((string)$hour);
	if ($date === '') {
		return null;
	}

	if ($hour === '') {
		$hour = $endDate ? '23:59:59' : '00:00:00';
	}
	else if (preg_match('/^\d{1,2}:\d{2}$/', $hour)) {
		$hour .= ':00';
	}

	foreach (['!m/d/Y H:i:s', '!n/j/Y H:i:s', '!Y-m-d H:i:s'] as $format) {
		$dateTime = DateTimeImmutable::createFromFormat($format, "{$date} {$hour}");
		$errors = DateTimeImmutable::getLastErrors();
		if ($dateTime !== false && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
			return $dateTime->getTimestamp();
		}
	}

	$timestamp = strtotime("{$date} {$hour}");
	return $timestamp !== false ? $timestamp : null;
}

function loadEventScheduleFromJson($filePath)
{
	if (!file_exists($filePath)) {
		return null;
	}

	$json = json_decode(file_get_contents($filePath), true);
	if (!is_array($json) || !isset($json['events']) || !is_array($json['events'])) {
		return [];
	}

	$eventlist = [];
	foreach ($json['events'] as $event) {
		if (!is_array($event)) {
			continue;
		}

		$defaultHour = isset($event['hour']) ? (string)$event['hour'] : '';
		$startHour = isset($event['starthour']) ? (string)$event['starthour'] : $defaultHour;
		$endHour = isset($event['endhour']) ? (string)$event['endhour'] : $defaultHour;
		$startDate = parseEventSchedulerJsonDate($event['startdate'] ?? '', $startHour);
		$endDate = parseEventSchedulerJsonDate($event['enddate'] ?? '', $endHour, true);
		if ($startDate === null || $endDate === null) {
			continue;
		}

		$colors = isset($event['colors']) && is_array($event['colors']) ? $event['colors'] : [];
		$details = isset($event['details']) && is_array($event['details']) ? $event['details'] : [];

		$eventlist[] = [
			'colorlight' => (string)($colors['colorlight'] ?? ''),
			'colordark' => (string)($colors['colordark'] ?? ''),
			'description' => (string)($event['description'] ?? ''),
			'displaypriority' => intval($details['displaypriority'] ?? 0),
			'enddate' => intval($endDate),
			'isseasonal' => getBoolean($details['isseasonal'] ?? false),
			'name' => (string)($event['name'] ?? ''),
			'startdate' => intval($startDate),
			'specialevent' => intval($details['specialevent'] ?? 0)
		];
	}

	return $eventlist;
}

function loadEventScheduleFromXml($filePath)
{
	if (!file_exists($filePath)) {
		return null;
	}

	$xml = new DOMDocument;
	if (@$xml->load($filePath) === false) {
		return [];
	}

	$eventlist = [];
	$tableevent = $xml->getElementsByTagName('event');
	foreach ($tableevent as $event) {
		if ($event) {
			$eventlist[] = [
				'colorlight' => parseEvent($event->getElementsByTagName('colors'), false, 'colorlight'),
				'colordark' => parseEvent($event->getElementsByTagName('colors'), false, 'colordark'),
				'description' => parseEvent($event->getElementsByTagName('description'), false, 'description'),
				'displaypriority' => intval(parseEvent($event->getElementsByTagName('details'), false, 'displaypriority')),
				'enddate' => intval(parseEvent($event, true, false)),
				'isseasonal' => getBoolean(intval(parseEvent($event->getElementsByTagName('details'), false, 'isseasonal'))),
				'name' => $event->getAttribute('name'),
				'startdate' => intval(parseEvent($event, true, true)),
				'specialevent' => intval(parseEvent($event->getElementsByTagName('details'), false, 'specialevent'))
			];
		}
	}

	return $eventlist;
}

function isAbsoluteServerPath($path)
{
	return preg_match('/^(?:[A-Za-z]:[\/\\\\]|[\/\\\\])/', (string)$path) === 1;
}

function buildServerDirectoryPath($directory)
{
	$directory = trim((string)$directory);
	if ($directory === '') {
		$directory = 'data';
	}

	if (!isAbsoluteServerPath($directory)) {
		$directory = config('server_path') . $directory;
	}

	$lastChar = $directory[strlen($directory) - 1] ?? '';
	if ($lastChar !== '/' && $lastChar !== '\\') {
		$directory .= '/';
	}

	return $directory;
}

function getEventScheduleDirectoryPaths()
{
	$directories = [];
	$addDirectory = static function ($directory) use (&$directories) {
		if (!is_string($directory) || trim($directory) === '') {
			return;
		}

		$directory = buildServerDirectoryPath($directory);
		if (!in_array($directory, $directories, true)) {
			$directories[] = $directory;
		}
	};

	foreach (['coreDirectory', 'dataDirectory', 'data_directory', 'datadir'] as $key) {
		$addDirectory(configLua($key));
	}

	$addDirectory(config('data_path'));
	$addDirectory('data');

	return $directories;
}

function getEventScheduleCacheKey($filePath, $format)
{
	return 'login_eventschedule_' . sha1($format . ':' . $filePath);
}

function getEventScheduleFileSignature($filePath)
{
	return [
		'mtime' => @filemtime($filePath) ?: 0,
		'size' => @filesize($filePath) ?: 0
	];
}

function getCachedEventScheduleResponse($filePath, $format)
{
	if (!is_file($filePath)) {
		return null;
	}

	$cache = Cache::getInstance();
	if (!$cache->enabled()) {
		return null;
	}

	$payload = null;
	if (!$cache->fetch(getEventScheduleCacheKey($filePath, $format), $payload) || !is_string($payload)) {
		return null;
	}

	$cached = @unserialize($payload);
	$signature = getEventScheduleFileSignature($filePath);
	if (
		is_array($cached)
		&& ($cached['mtime'] ?? null) === $signature['mtime']
		&& ($cached['size'] ?? null) === $signature['size']
		&& is_string($cached['response'] ?? null)
	) {
		return $cached['response'];
	}

	return null;
}

function cacheEventScheduleResponse($filePath, $format, $eventlist)
{
	$response = json_encode(['eventlist' => $eventlist, 'lastupdatetimestamp' => time()]);
	$cache = Cache::getInstance();
	if ($cache->enabled()) {
		$cache->set(
			getEventScheduleCacheKey($filePath, $format),
			serialize(getEventScheduleFileSignature($filePath) + ['response' => $response]),
			10 * 365 * 24 * 60 * 60
		);
	}

	return $response;
}

function getBoostedCreatureCacheSignature()
{
	global $status;

	$signature = [
		'clientVersion' => (int)setting('core.client')
	];

	if (
		isset($status['online'], $status['lastCheck'], $status['uptime'])
		&& getBoolean($status['online'])
		&& is_numeric($status['lastCheck'])
		&& is_numeric($status['uptime'])
		&& intval($status['uptime']) > 0
		&& intval($status['lastCheck']) > intval($status['uptime'])
	) {
		$serverStartedAt = intval($status['lastCheck']) - intval($status['uptime']);
		$signature['serverStartedAtMinute'] = intdiv($serverStartedAt, 60);
		return $signature;
	}

	$signature['date'] = date('Y-m-d');
	return $signature;
}

function getBoostedCreatureCacheTtl($signature)
{
	return isset($signature['serverStartedAtMinute']) ? 24 * 60 * 60 : 5 * 60;
}

function getBoostedCreatureCacheKey($signature)
{
	return 'login_boostedcreature_' . sha1(json_encode($signature));
}

function getCachedBoostedCreatureResponse($signature)
{
	$cache = Cache::getInstance();
	if (!$cache->enabled()) {
		return null;
	}

	$response = null;
	if ($cache->fetch(getBoostedCreatureCacheKey($signature), $response) && is_string($response)) {
		return $response;
	}

	return null;
}

function cacheBoostedCreatureResponse($signature, $response)
{
	$cache = Cache::getInstance();
	if ($cache->enabled()) {
		$cache->set(getBoostedCreatureCacheKey($signature), $response, getBoostedCreatureCacheTtl($signature));
	}

	return $response;
}

function getBoostedCreatureResponse($db)
{
	$signature = getBoostedCreatureCacheSignature();
	$cachedResponse = getCachedBoostedCreatureResponse($signature);
	if ($cachedResponse !== null) {
		return $cachedResponse;
	}

	$clientVersion = (int)setting('core.client');
	if ($clientVersion >= 1340) {
		$creatureBoost = $db->query("SELECT * FROM " . $db->tableName('boosted_creature'))->fetchAll();
		$bossBoost = $db->query("SELECT * FROM " . $db->tableName('boosted_boss'))->fetchAll();
		return cacheBoostedCreatureResponse($signature, json_encode([
			//'boostedcreature' => true,
			'bossraceid' => intval($bossBoost[0]['raceid']),
			'creatureraceid' => intval($creatureBoost[0]['raceid']),
		]));
	}

	$boostedCreature = BoostedCreature::first();
	return cacheBoostedCreatureResponse($signature, json_encode([
		'boostedcreature' => true,
		'raceid' => $boostedCreature->raceid
	]));
}

function isLivestreamLogin($value)
{
	return strtolower(trim((string)$value)) === '@livestream';
}

function getLivestreamUnavailableMessage()
{
	return 'No active livestream casters found, or livestream login is disabled.';
}

function sendLivestreamLogin($db, $world, $password)
{
	try {
		$playersTable = $db->tableName('players');
		$castersTable = $db->tableName('active_livestream_casters');
		$promotionColumn = $db->hasColumn('players', 'promotion') ? ', p.promotion' : '';
		$liveCastersQuery = $db->query("
			SELECT p.id, p.name, p.level, p.sex, p.vocation, p.looktype, p.lookhead, p.lookbody,
				p.looklegs, p.lookfeet, p.lookaddons{$promotionColumn}
			FROM {$playersTable} p
			INNER JOIN {$castersTable} lc ON p.id = lc.caster_id
			WHERE lc.livestream_status >= 1
			ORDER BY lc.livestream_viewers DESC, p.name ASC
		");
		if ($liveCastersQuery === false) {
			sendError('Database query failed', 500);
		}

		$casters = $liveCastersQuery->fetchAll(PDO::FETCH_ASSOC);
	}
	catch (PDOException $error) {
		if (stripos($error->getMessage(), 'active_livestream_casters') !== false) {
			sendError(getLivestreamUnavailableMessage());
		}

		sendError('Database query failed', 500);
	}

	$characters = [];
	foreach ($casters as $caster) {
		$characters[] = create_livestream_char($caster);
	}

	if (empty($characters)) {
		sendError(getLivestreamUnavailableMessage());
	}

	$worlds = [$world];
	$playdata = compact('worlds', 'characters');
	$session = [
		'sessionkey' => "@livestream\n" . (string)$password,
		'lastlogintime' => 0,
		'ispremium' => false,
		'premiumuntil' => 0,
		'status' => 'active',
		'returnernotification' => false,
		'showrewardnews' => false,
		'isreturner' => false,
		'fpstracking' => false,
		'optiontracking' => false,
		'tournamentticketpurchasestate' => 0,
		'emailcoderequest' => false
	];

	die(json_encode(compact('session', 'playdata')));
}

$request = json_decode(file_get_contents('php://input'));
$action = $request->type ?? '';

/** @var OTS_Base_DB $db */
/** @var array $config */

switch ($action) {
	case 'cacheinfo':
		$playersonline = PlayerOnline::count();
		die(json_encode([
			'playersonline' => $playersonline,
			'twitchstreams' => 0,
			'twitchviewer' => 0,
			'gamingyoutubestreams' => 0,
			'gamingyoutubeviewer' => 0
		]));

	case 'eventschedule':
		$eventScheduleDirectories = getEventScheduleDirectoryPaths();
		foreach ($eventScheduleDirectories as $eventScheduleDirectory) {
			$jsonFilePath = $eventScheduleDirectory . 'json/eventscheduler/events.json';
			$cachedResponse = getCachedEventScheduleResponse($jsonFilePath, 'json');
			if ($cachedResponse !== null) {
				die($cachedResponse);
			}

			$eventlist = loadEventScheduleFromJson($jsonFilePath);
			if ($eventlist !== null) {
				die(cacheEventScheduleResponse($jsonFilePath, 'json', $eventlist));
			}
		}

		foreach ($eventScheduleDirectories as $eventScheduleDirectory) {
			$xmlFilePath = $eventScheduleDirectory . 'XML/events.xml';
			$cachedResponse = getCachedEventScheduleResponse($xmlFilePath, 'xml');
			if ($cachedResponse !== null) {
				die($cachedResponse);
			}

			$eventlist = loadEventScheduleFromXml($xmlFilePath);
			if ($eventlist !== null) {
				die(cacheEventScheduleResponse($xmlFilePath, 'xml', $eventlist));
			}
		}

		die(json_encode([]));

	case 'boostedcreature':
		die(getBoostedCreatureResponse($db));

	case 'login':

		$port = $config['lua']['gameProtocolPort'];

		// default world info
		$world = [
			'id' => 0,
			'name' => $config['lua']['serverName'],
			'externaladdress' => $config['lua']['ip'],
			'externalport' => $port,
			'externaladdressprotected' => $config['lua']['ip'],
			'externalportprotected' => $port,
			'externaladdressunprotected' => $config['lua']['ip'],
			'externalportunprotected' => $port,
			'previewstate' => 0,
			'location' => 'BRA', // BRA, EUR, USA
			'anticheatprotection' => false,
			'pvptype' => array_search($config['lua']['worldType'], ['pvp', 'no-pvp', 'pvp-enforced']),
			'istournamentworld' => false,
			'restrictedstore' => false,
			'currenttournamentphase' => 2
		];

		$characters = [];

		$inputEmail = $request->email ?? false;
		$inputAccountName = $request->accountname ?? false;
		$inputToken = $request->token ?? false;

		if (isLivestreamLogin($inputEmail) || isLivestreamLogin($inputAccountName)) {
			sendLivestreamLogin($db, $world, $request->password ?? '');
		}

		$account = Account::query();
		if ($inputEmail != false) { // login by email
			$account->where('email', $inputEmail);
		}
		else if($inputAccountName != false) { // login by account name
			$account->where('name', $inputAccountName);
		}

		$account = $account->first();

		$ip = get_browser_real_ip();
		$limiter = new RateLimit('failed_logins', setting('core.account_login_attempts_limit'), setting('core.account_login_ban_time'));
		$limiter->enabled = setting('core.account_login_ipban_protection');
		$limiter->load();

		$ban_msg = 'A wrong account, password or secret has been entered ' . setting('core.account_login_attempts_limit') . ' times in a row. You are unable to log into your account for the next ' . setting('core.account_login_ban_time') . ' minutes. Please wait.';
		if (!$account) {
			$limiter->increment($ip);
			if ($limiter->exceeded($ip)) {
				sendError($ban_msg);
			}

			sendError(($inputEmail != false ? 'Email' : 'Account name') . ' or password is not correct.');
		}

		$current_password = encrypt((USE_ACCOUNT_SALT ? $account->salt : '') . $request->password);
		if (!$account || $account->password != $current_password) {
			$limiter->increment($ip);
			if ($limiter->exceeded($ip)) {
				sendError($ban_msg);
			}

			sendError(($inputEmail != false ? 'Email' : 'Account name') . ' or password is not correct.');
		}

		$accountHasSecret = false;
		if (fieldExist('secret', 'accounts')) {
			$accountSecret = $account->secret;
			if ($accountSecret != null && $accountSecret != '') {
				$accountHasSecret = true;
				if ($inputToken === false) {
					$limiter->increment($ip);
					if ($limiter->exceeded($ip)) {
						sendError($ban_msg);
					}
					sendError('Submit a valid two-factor authentication token.', 6);
				} else {
					require_once LIBS . 'rfc6238.php';
					if (TokenAuth6238::verify($accountSecret, $inputToken) !== true) {
						$limiter->increment($ip);
						if ($limiter->exceeded($ip)) {
							sendError($ban_msg);
						}

						sendError('Two-factor authentication failed, token is wrong.', 6);
					}
				}
			}
		}

		$limiter->reset($ip);
		if (setting('core.account_mail_verify') && $account->email_verified !== 1) {
			sendError('You need to verify your account, enter in our site and resend verify e-mail!');
		}

		// common columns
		$columns = 'id, name, level, sex, vocation, looktype, lookhead, lookbody, looklegs, lookfeet, lookaddons';

		if (fieldExist('isreward', 'accounts')) {
			$columns .= ', isreward';
		}

		if (fieldExist('istutorial', 'accounts')) {
			$columns .= ', istutorial';
		}

		$players = Player::where('account_id', $account->id)->notDeleted()->selectRaw($columns)->get();
		if($players && $players->count()) {
			$highestLevelId = $players->sortByDesc('experience')->first()->getKey();

			foreach ($players as $player) {
				$characters[] = create_char($player, $highestLevelId);
			}
		}

		/*
		 * not needed anymore?
		if (fieldExist('premdays', 'accounts') && fieldExist('lastday', 'accounts')) {
			$save = false;
			$timeNow = time();
			$premDays = $account->premdays;
			$lastDay = $account->lastday;
			$lastLogin = $lastDay;

			if ($premDays != 0 && $premDays != PHP_INT_MAX) {
				if ($lastDay == 0) {
					$lastDay = $timeNow;
					$save = true;
				} else {
					$days = (int)(($timeNow - $lastDay) / 86400);
					if ($days > 0) {
						if ($days >= $premDays) {
							$premDays = 0;
							$lastDay = 0;
						} else {
							$premDays -= $days;
							$reminder = ($timeNow - $lastDay) % 86400;
							$lastDay = $timeNow - $reminder;
						}

						$save = true;
					}
				}
			} else if ($lastDay != 0) {
				$lastDay = 0;
				$save = true;
			}
			if ($save) {
				$account->premdays = $premDays;
				$account->lastday = $lastDay;
				$account->save();
			}
		}
		*/

		$worlds = [$world];
		$playdata = compact('worlds', 'characters');

		$sessionKey = ($inputEmail !== false) ? $inputEmail : $inputAccountName; // email or account name
		$sessionKey .= "\n" . $request->password; // password
		if (!fieldExist('istutorial', 'players')) {
			$sessionKey .= "\n";
		}
		$sessionKey .= ($accountHasSecret && strlen($accountSecret) > 5) ? $inputToken : '';

		// this is workaround to distinguish between TFS 1.x and otservbr
		// TFS 1.x requires the number in session key
		// otservbr requires just login and password
		// so we check for istutorial field which is present in otservbr, and not in TFS
		if (!fieldExist('istutorial', 'players')) {
			$sessionKey .= "\n".floor(time() / 30);
		}

		$session = [
			'sessionkey' => $sessionKey,
			'lastlogintime' => 0,
			'ispremium' => $account->is_premium,
			'premiumuntil' => ($account->premium_days) > 0 ? (time() + ($account->premium_days * 86400)) : 0,
			'status' => 'active', // active, frozen or suspended
			'returnernotification' => false,
			'showrewardnews' => true,
			'isreturner' => true,
			'fpstracking' => false,
			'optiontracking' => false,
			'tournamentticketpurchasestate' => 0,
			'emailcoderequest' => false
		];
		die(json_encode(compact('session', 'playdata')));

	default:
		sendError("Unrecognized event {$action}.");
	break;
}

function create_char($player, $highestLevelId) {
	return [
		'worldid' => 0,
		'name' => $player->name,
		'ismale' => $player->sex === 1,
		'tutorial' => isset($player->istutorial) && $player->istutorial,
		'level' => $player->level,
		'vocation' => $player->vocation_name,
		'outfitid' => $player->looktype,
		'headcolor' => $player->lookhead,
		'torsocolor' => $player->lookbody,
		'legscolor' => $player->looklegs,
		'detailcolor' => $player->lookfeet,
		'addonsflags' => $player->lookaddons,
		'ishidden' => $player->is_deleted,
		'istournamentparticipant' => false,
		'ismaincharacter' => $highestLevelId === $player->getKey(),
		'dailyrewardstate' => $player->isreward ?? 0,
		'remainingdailytournamentplaytime' => 0
	];
}

function create_livestream_char($caster) {
	return [
		'worldid' => 0,
		'name' => (string)$caster['name'],
		'ismale' => intval($caster['sex']) === 1,
		'tutorial' => false,
		'level' => intval($caster['level']),
		'vocation' => get_livestream_vocation_name($caster),
		'outfitid' => intval($caster['looktype']),
		'headcolor' => intval($caster['lookhead']),
		'torsocolor' => intval($caster['lookbody']),
		'legscolor' => intval($caster['looklegs']),
		'detailcolor' => intval($caster['lookfeet']),
		'addonsflags' => intval($caster['lookaddons']),
		'ishidden' => false,
		'istournamentparticipant' => false,
		'ismaincharacter' => false,
		'dailyrewardstate' => 0,
		'remainingdailytournamentplaytime' => 0
	];
}

function get_livestream_vocation_name($caster) {
	$vocation = intval($caster['vocation'] ?? 0);
	if (isset($caster['promotion']) && intval($caster['promotion']) > 0) {
		$vocation += intval($caster['promotion']) * intval(setting('core.vocations_amount'));
	}

	return config('vocations')[$vocation] ?? 'Unknown';
}
