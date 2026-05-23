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

const LOGIN_ERROR_INVALID_CREDENTIALS = 3;
const LOGIN_ERROR_TWO_FACTOR_REQUIRED = 6;
const LOGIN_ERROR_DATABASE_UNAVAILABLE = 2001;
const LOGIN_ERROR_ACCOUNT_DATA_UNAVAILABLE = 2002;
const LOGIN_ERROR_CHARACTER_LIST_LOAD_FAILED = 2003;
const LOGIN_ERROR_LOGIN_SERVICE_UNAVAILABLE = 3001;
const LOGIN_ERROR_UNSUPPORTED_REQUEST_TYPE = 3002;
const LOGIN_ERROR_MALFORMED_REQUEST = 3003;
const LOGIN_ERROR_JSON_RESPONSE_FAILED = 3004;
const LOGIN_ERROR_EVENT_SCHEDULE_UNAVAILABLE = 4001;
const LOGIN_ERROR_BOOSTED_DATA_UNAVAILABLE = 4002;
const LOGIN_ERROR_LIVESTREAM_UNAVAILABLE = 4003;
const LOGIN_ERROR_LIVESTREAM_DATA_UNAVAILABLE = 4004;
const LOGIN_ACCOUNT_TYPE_GAMEMASTER = 4;
const LOGIN_ACCOUNT_GROUP_GAMEMASTER = 4;

# error function
function sendError($message, $code = 3){
	sendJsonResponse([
		'errorCode' => $code,
		'errorMessage' => $message
	]);
}

function sendJsonResponse($data)
{
	if (!headers_sent()) {
		header('Content-Type: application/json');
		http_response_code(200);
	}

	$response = json_encode($data);
	if ($response === false) {
		$response = '{"errorCode":' . LOGIN_ERROR_JSON_RESPONSE_FAILED . ',"errorMessage":"Failed to encode JSON response. Error: JSON_RESPONSE_FAILED (LS-' . LOGIN_ERROR_JSON_RESPONSE_FAILED . ')."}';
	}

	die($response);
}

function loginPublicErrorMessage($message, $name, $code)
{
	return $message . ' Error: ' . $name . ' (LS-' . $code . ').';
}

function loginAdminHint($name)
{
	switch ($name) {
		case 'ACCOUNT_DATA_UNAVAILABLE':
			return 'Check the accounts table schema and database connectivity; MyAAC needs the login identifier and password columns, and may use type, group_id or web_flags to identify admin accounts when those columns exist.';
		case 'CHARACTER_LIST_LOAD_FAILED':
			return 'Check the players table schema for the configured server base, especially account_id, name, level, sex, vocation, outfit and deletion columns.';
		case 'ACCOUNT_EMAIL_NOT_VERIFIED':
			return 'Verify the account email in the website database, or disable account_mail_verify if this server does not use website email verification.';
		case 'TWO_FACTOR_TOKEN_REQUIRED':
		case 'TWO_FACTOR_TOKEN_INVALID':
			return 'Check the account secret column and the authenticator clock; clear the account secret only if you intentionally want to disable 2FA for this account.';
		case 'LOGIN_RATE_LIMITED':
			return 'Clear the failed_logins rate limit cache or wait for the configured account_login_ban_time before testing this account again.';
		case 'LOGIN_SERVICE_UNAVAILABLE':
			return 'Check system/logs/login-errors.log and the web server PHP error log for the root cause.';
		default:
			return '';
	}
}

function appendLoginAdminHint($message, $name, $includeAdminHint)
{
	if (!$includeAdminHint) {
		return $message;
	}

	$hint = loginAdminHint($name);
	if ($hint === '') {
		return $message;
	}

	return $message . ' Admin hint: ' . $hint;
}

function logLoginDiagnostic($name, $code, $cause = null, array $context = [])
{
	$details = [
		'name' => $name,
		'code' => $code
	];

	if ($cause instanceof Throwable) {
		$details['cause'] = get_class($cause) . ': ' . $cause->getMessage();
		$details['file'] = $cause->getFile();
		$details['line'] = $cause->getLine();
	} else if ($cause !== null) {
		$details['cause'] = (string)$cause;
	}

	if (!empty($context)) {
		$details['context'] = $context;
	}

	try {
		log_append('login-errors.log', '[' . $name . '] ' . json_encode($details));
	} catch (Throwable $error) {
		error_log('[login.php][' . $name . '] ' . print_r($details, true));
	}
}

function sendPublicError($code, $name, $message, $cause = null, array $context = [], $includeAdminHint = false)
{
	if ($cause !== null || !empty($context)) {
		logLoginDiagnostic($name, $code, $cause, $context);
	}

	$message = appendLoginAdminHint($message, $name, $includeAdminHint);
	sendError(loginPublicErrorMessage($message, $name, $code), $code);
}

function loginAccountReceivesAdminHints($account)
{
	if (!$account) {
		return false;
	}

	$webFlags = isset($account->web_flags) ? intval($account->web_flags) : 0;
	$adminFlag = defined('FLAG_ADMIN') ? FLAG_ADMIN : 1;
	$superAdminFlag = defined('FLAG_SUPER_ADMIN') ? FLAG_SUPER_ADMIN : 2;
	if (($webFlags & $adminFlag) === $adminFlag || ($webFlags & $superAdminFlag) === $superAdminFlag) {
		return true;
	}

	if (isset($account->type) && intval($account->type) >= LOGIN_ACCOUNT_TYPE_GAMEMASTER) {
		return true;
	}

	if (isset($account->group_id) && intval($account->group_id) >= LOGIN_ACCOUNT_GROUP_GAMEMASTER) {
		return true;
	}

	return false;
}

function encodeJsonResponse($data)
{
	$response = json_encode($data);
	if ($response === false) {
		sendPublicError(
			LOGIN_ERROR_JSON_RESPONSE_FAILED,
			'JSON_RESPONSE_FAILED',
			'Failed to encode JSON response.',
			json_last_error_msg()
		);
	}

	return $response;
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

function parseEventSchedulerXmlDate($event, $attribute)
{
	$date = trim((string)$event->getAttribute($attribute));
	if ($date === '') {
		return null;
	}

	$dateTime = date_create($date);
	return $dateTime !== false ? intval($dateTime->format('U')) : null;
}

function parseEventSchedulerXmlValue($table, $attribute, $default = '')
{
	$value = parseEvent($table, false, $attribute);
	return $value === 'error' ? $default : $value;
}

function getEventScheduleJsonField($event, $field, $default = '')
{
	return array_key_exists($field, $event) ? (string)$event[$field] : $default;
}

function getEventScheduleJsonSection($event, $field)
{
	if (!isset($event[$field]) || !is_array($event[$field])) {
		return [];
	}

	return $event[$field];
}

function createEventScheduleJsonEntry($event)
{
	if (!is_array($event)) {
		return null;
	}

	$defaultHour = getEventScheduleJsonField($event, 'hour');
	$startHour = getEventScheduleJsonField($event, 'starthour', $defaultHour);
	$endHour = getEventScheduleJsonField($event, 'endhour', $defaultHour);
	$startDate = parseEventSchedulerJsonDate($event['startdate'] ?? '', $startHour);
	$endDate = parseEventSchedulerJsonDate($event['enddate'] ?? '', $endHour, true);
	if ($startDate === null || $endDate === null) {
		return null;
	}

	$colors = getEventScheduleJsonSection($event, 'colors');
	$details = getEventScheduleJsonSection($event, 'details');

	return [
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

function loadEventScheduleFromJson($filePath, &$error = null)
{
	if (!file_exists($filePath)) {
		return null;
	}

	$content = file_get_contents($filePath);
	if ($content === false) {
		$error = "Cannot read event scheduler JSON file: {$filePath}";
		return null;
	}

	$json = json_decode($content, true);
	if (json_last_error() !== JSON_ERROR_NONE) {
		$error = "Invalid event scheduler JSON file {$filePath}: " . json_last_error_msg();
		return null;
	}

	if (!is_array($json) || !isset($json['events']) || !is_array($json['events'])) {
		$error = "Invalid event scheduler JSON structure in {$filePath}: missing events array.";
		return null;
	}

	$eventlist = [];
	foreach ($json['events'] as $event) {
		$eventEntry = createEventScheduleJsonEntry($event);
		if ($eventEntry === null) {
			continue;
		}

		$eventlist[] = $eventEntry;
	}

	return $eventlist;
}

function loadEventScheduleFromXml($filePath, &$error = null)
{
	if (!file_exists($filePath)) {
		return null;
	}

	$xml = new DOMDocument;
	$previousUseInternalErrors = libxml_use_internal_errors(true);
	libxml_clear_errors();
	$loaded = $xml->load($filePath);
	libxml_clear_errors();
	libxml_use_internal_errors($previousUseInternalErrors);
	if ($loaded === false) {
		$error = "Invalid event scheduler XML file: {$filePath}";
		return null;
	}

	$eventlist = [];
	$tableevent = $xml->getElementsByTagName('event');
	foreach ($tableevent as $event) {
		if ($event) {
			$startDate = parseEventSchedulerXmlDate($event, 'startdate');
			$endDate = parseEventSchedulerXmlDate($event, 'enddate');
			if ($startDate === null || $endDate === null) {
				continue;
			}

			$eventlist[] = [
				'colorlight' => parseEventSchedulerXmlValue($event->getElementsByTagName('colors'), 'colorlight'),
				'colordark' => parseEventSchedulerXmlValue($event->getElementsByTagName('colors'), 'colordark'),
				'description' => parseEventSchedulerXmlValue($event->getElementsByTagName('description'), 'description'),
				'displaypriority' => intval(parseEventSchedulerXmlValue($event->getElementsByTagName('details'), 'displaypriority', 0)),
				'enddate' => $endDate,
				'isseasonal' => getBoolean(intval(parseEventSchedulerXmlValue($event->getElementsByTagName('details'), 'isseasonal', 0))),
				'name' => $event->getAttribute('name'),
				'startdate' => $startDate,
				'specialevent' => intval(parseEventSchedulerXmlValue($event->getElementsByTagName('details'), 'specialevent', 0))
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

	$cached = json_decode($payload, true);
	if (json_last_error() !== JSON_ERROR_NONE) {
		return null;
	}

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
	$response = encodeJsonResponse(['eventlist' => $eventlist, 'lastupdatetimestamp' => time()]);
	$cache = Cache::getInstance();
	if ($cache->enabled()) {
		$payload = encodeJsonResponse(array_merge(getEventScheduleFileSignature($filePath), ['response' => $response]));
		$cache->set(
			getEventScheduleCacheKey($filePath, $format),
			$payload,
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
		try {
			$creatureBoostQuery = $db->query("SELECT `raceid` FROM " . $db->tableName('boosted_creature') . " LIMIT 1");
			$bossBoostQuery = $db->query("SELECT `raceid` FROM " . $db->tableName('boosted_boss') . " LIMIT 1");
		} catch (Throwable $error) {
			sendPublicError(
				LOGIN_ERROR_BOOSTED_DATA_UNAVAILABLE,
				'BOOSTED_DATA_UNAVAILABLE',
				'Boosted creature data is unavailable. Please contact support.',
				$error
			);
		}

		if ($creatureBoostQuery === false || $bossBoostQuery === false) {
			sendPublicError(
				LOGIN_ERROR_BOOSTED_DATA_UNAVAILABLE,
				'BOOSTED_DATA_UNAVAILABLE',
				'Boosted creature data is unavailable. Please contact support.',
				'boosted_creature or boosted_boss query failed'
			);
		}

		$creatureBoost = $creatureBoostQuery !== false ? $creatureBoostQuery->fetch(PDO::FETCH_ASSOC) : false;
		$bossBoost = $bossBoostQuery !== false ? $bossBoostQuery->fetch(PDO::FETCH_ASSOC) : false;
		return cacheBoostedCreatureResponse($signature, encodeJsonResponse([
			//'boostedcreature' => true,
			'bossraceid' => intval($bossBoost['raceid'] ?? 0),
			'creatureraceid' => intval($creatureBoost['raceid'] ?? 0),
		]));
	}

	try {
		$boostedCreature = BoostedCreature::first();
	} catch (Throwable $error) {
		sendPublicError(
			LOGIN_ERROR_BOOSTED_DATA_UNAVAILABLE,
			'BOOSTED_DATA_UNAVAILABLE',
			'Boosted creature data is unavailable. Please contact support.',
			$error
		);
	}

	return cacheBoostedCreatureResponse($signature, encodeJsonResponse([
		'boostedcreature' => true,
		'raceid' => $boostedCreature->raceid ?? 0
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

function sendLivestreamUnavailable($cause = null, array $context = [])
{
	sendPublicError(
		LOGIN_ERROR_LIVESTREAM_UNAVAILABLE,
		'LIVESTREAM_UNAVAILABLE',
		getLivestreamUnavailableMessage(),
		$cause,
		$context
	);
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
			sendPublicError(
				LOGIN_ERROR_LIVESTREAM_DATA_UNAVAILABLE,
				'LIVESTREAM_DATA_UNAVAILABLE',
				'Livestream data is unavailable. Please contact support.',
				'active_livestream_casters query failed'
			);
		}

		$casters = $liveCastersQuery->fetchAll(PDO::FETCH_ASSOC);
	}
	catch (Throwable $error) {
		if (stripos($error->getMessage(), 'active_livestream_casters') !== false) {
			sendLivestreamUnavailable($error);
		}

		sendPublicError(
			LOGIN_ERROR_LIVESTREAM_DATA_UNAVAILABLE,
			'LIVESTREAM_DATA_UNAVAILABLE',
			'Livestream data is unavailable. Please contact support.',
			$error
		);
	}

	$characters = [];
	foreach ($casters as $caster) {
		$characters[] = create_livestream_char($caster);
	}

	if (empty($characters)) {
		sendLivestreamUnavailable(null, ['reason' => 'no active casters']);
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

$requestBody = file_get_contents('php://input');
$request = json_decode($requestBody);
if (!is_object($request) || json_last_error() !== JSON_ERROR_NONE) {
	sendPublicError(
		LOGIN_ERROR_MALFORMED_REQUEST,
		'MALFORMED_REQUEST',
		'Malformed login request. Please restart the client and try again.',
		json_last_error_msg(),
		['bodyLength' => is_string($requestBody) ? strlen($requestBody) : 0]
	);
}

$action = trim((string)($request->type ?? ''));

/** @var OTS_Base_DB $db */
/** @var array $config */

$includeAdminHint = false;

try {
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
		$eventScheduleErrors = [];
		foreach ($eventScheduleDirectories as $eventScheduleDirectory) {
			$jsonFilePath = $eventScheduleDirectory . 'json/eventscheduler/events.json';
			$cachedResponse = getCachedEventScheduleResponse($jsonFilePath, 'json');
			if ($cachedResponse !== null) {
				die($cachedResponse);
			}

			$error = null;
			$eventlist = loadEventScheduleFromJson($jsonFilePath, $error);
			if ($eventlist !== null) {
				die(cacheEventScheduleResponse($jsonFilePath, 'json', $eventlist));
			}
			if ($error !== null) {
				$eventScheduleErrors[] = $error;
			}
		}

		foreach ($eventScheduleDirectories as $eventScheduleDirectory) {
			$xmlFilePath = $eventScheduleDirectory . 'XML/events.xml';
			$cachedResponse = getCachedEventScheduleResponse($xmlFilePath, 'xml');
			if ($cachedResponse !== null) {
				die($cachedResponse);
			}

			$error = null;
			$eventlist = loadEventScheduleFromXml($xmlFilePath, $error);
			if ($eventlist !== null) {
				die(cacheEventScheduleResponse($xmlFilePath, 'xml', $eventlist));
			}
			if ($error !== null) {
				$eventScheduleErrors[] = $error;
			}
		}

		if (!empty($eventScheduleErrors)) {
			sendPublicError(
				LOGIN_ERROR_EVENT_SCHEDULE_UNAVAILABLE,
				'EVENT_SCHEDULE_UNAVAILABLE',
				'Event schedule data is unavailable. Please contact support.',
				implode(' | ', $eventScheduleErrors)
			);
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

		if ($inputEmail === false && $inputAccountName === false) {
			sendPublicError(
				LOGIN_ERROR_MALFORMED_REQUEST,
				'MALFORMED_REQUEST',
				'Malformed login request. Please restart the client and try again.',
				'missing email/accountname'
			);
		}

		if (!isset($request->password)) {
			sendPublicError(
				LOGIN_ERROR_MALFORMED_REQUEST,
				'MALFORMED_REQUEST',
				'Malformed login request. Please restart the client and try again.',
				'missing password'
			);
		}

		try {
			$account = Account::query();
			if ($inputEmail != false) { // login by email
				$account->where('email', $inputEmail);
			}
			else if($inputAccountName != false) { // login by account name
				$account->where('name', $inputAccountName);
			}

			$account = $account->first();
		} catch (Throwable $error) {
			sendPublicError(
				LOGIN_ERROR_ACCOUNT_DATA_UNAVAILABLE,
				'ACCOUNT_DATA_UNAVAILABLE',
				'Account data is unavailable. Please contact support.',
				$error
			);
		}

		$ip = get_browser_real_ip();
		$limiter = new RateLimit('failed_logins', setting('core.account_login_attempts_limit'), setting('core.account_login_ban_time'));
		$limiter->enabled = setting('core.account_login_ipban_protection');
		$limiter->load();

		$ban_msg = 'A wrong account, password or secret has been entered ' . setting('core.account_login_attempts_limit') . ' times in a row. You are unable to log into your account for the next ' . setting('core.account_login_ban_time') . ' minutes. Please wait.';
		if (!$account) {
			$limiter->increment($ip);
			if ($limiter->exceeded($ip)) {
				sendPublicError(
					LOGIN_ERROR_INVALID_CREDENTIALS,
					'LOGIN_RATE_LIMITED',
					$ban_msg
				);
			}

			sendPublicError(
				LOGIN_ERROR_INVALID_CREDENTIALS,
				'INVALID_CREDENTIALS',
				($inputEmail != false ? 'Email' : 'Account name') . ' or password is not correct.'
			);
		}

		$current_password = encrypt((USE_ACCOUNT_SALT ? $account->salt : '') . $request->password);
		if (!$account || $account->password != $current_password) {
			$limiter->increment($ip);
			if ($limiter->exceeded($ip)) {
				sendPublicError(
					LOGIN_ERROR_INVALID_CREDENTIALS,
					'LOGIN_RATE_LIMITED',
					$ban_msg
				);
			}

			sendPublicError(
				LOGIN_ERROR_INVALID_CREDENTIALS,
				'INVALID_CREDENTIALS',
				($inputEmail != false ? 'Email' : 'Account name') . ' or password is not correct.'
			);
		}

		$includeAdminHint = loginAccountReceivesAdminHints($account);
		$accountHasSecret = false;
		if (fieldExist('secret', 'accounts')) {
			$accountSecret = $account->secret;
			if ($accountSecret != null && $accountSecret != '') {
				$accountHasSecret = true;
				if ($inputToken === false) {
					$limiter->increment($ip);
					if ($limiter->exceeded($ip)) {
						sendPublicError(
							LOGIN_ERROR_INVALID_CREDENTIALS,
							'LOGIN_RATE_LIMITED',
							$ban_msg,
							null,
							[],
							$includeAdminHint
						);
					}
					sendPublicError(
						LOGIN_ERROR_TWO_FACTOR_REQUIRED,
						'TWO_FACTOR_TOKEN_REQUIRED',
						'Submit a valid two-factor authentication token.',
						null,
						[],
						$includeAdminHint
					);
				} else {
					require_once LIBS . 'rfc6238.php';
					if (TokenAuth6238::verify($accountSecret, $inputToken) !== true) {
						$limiter->increment($ip);
						if ($limiter->exceeded($ip)) {
							sendPublicError(
								LOGIN_ERROR_INVALID_CREDENTIALS,
								'LOGIN_RATE_LIMITED',
								$ban_msg,
								null,
								[],
								$includeAdminHint
							);
						}

						sendPublicError(
							LOGIN_ERROR_TWO_FACTOR_REQUIRED,
							'TWO_FACTOR_TOKEN_INVALID',
							'Two-factor authentication failed, token is wrong.',
							null,
							[],
							$includeAdminHint
						);
					}
				}
			}
		}

		$limiter->reset($ip);
		if (setting('core.account_mail_verify') && $account->email_verified !== 1) {
			sendPublicError(
				LOGIN_ERROR_INVALID_CREDENTIALS,
				'ACCOUNT_EMAIL_NOT_VERIFIED',
				'You need to verify your account, enter in our site and resend verify e-mail.',
				null,
				[],
				$includeAdminHint
			);
		}

		// common columns
		$columns = 'id, name, level, sex, vocation, looktype, lookhead, lookbody, looklegs, lookfeet, lookaddons';

		if (fieldExist('isreward', 'accounts')) {
			$columns .= ', isreward';
		}

		if (fieldExist('istutorial', 'accounts')) {
			$columns .= ', istutorial';
		}

		try {
			$players = Player::where('account_id', $account->id)->notDeleted()->selectRaw($columns)->get();
		} catch (Throwable $error) {
			sendPublicError(
				LOGIN_ERROR_CHARACTER_LIST_LOAD_FAILED,
				'CHARACTER_LIST_LOAD_FAILED',
				'Character list is unavailable. Please contact support.',
				$error,
				[],
				$includeAdminHint
			);
		}
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
		sendPublicError(
			LOGIN_ERROR_UNSUPPORTED_REQUEST_TYPE,
			'UNSUPPORTED_REQUEST_TYPE',
			'Unsupported login request type. Please restart the client and try again.',
			null,
			['type' => $action]
		);
	break;
}
} catch (Throwable $error) {
	sendPublicError(
		LOGIN_ERROR_LOGIN_SERVICE_UNAVAILABLE,
		'LOGIN_SERVICE_UNAVAILABLE',
		'Login service error. Please contact support.',
		$error,
		['type' => $action],
		$includeAdminHint
	);
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
