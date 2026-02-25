<?php

use MyAAC\Models\BoostedCreature;
use MyAAC\Models\PlayerOnline;
use MyAAC\Models\Account;
use MyAAC\Models\Player;
use MyAAC\RateLimit;
use MyAAC\TwoFactorAuth\TwoFactorAuth;

require_once 'common.php';
require_once SYSTEM . 'functions.php';
require_once SYSTEM . 'init.php';
require_once SYSTEM . 'status.php';

# error function
function sendError($message, $code = 3) {
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
		$eventlist = [];
		$file_path = config('server_path') . 'data/XML/events.xml';
		if (!file_exists($file_path)) {
			die(json_encode([]));
		}
		$xml = new DOMDocument;
		$xml->load($file_path);
		$tmplist = [];
		$tableevent = $xml->getElementsByTagName('event');

		foreach ($tableevent as $event) {
			if ($event) { $tmplist = [
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
			$eventlist[] = $tmplist; } }
		die(json_encode(['eventlist' => $eventlist, 'lastupdatetimestamp' => time()]));

	case 'boostedcreature':
		$clientVersion = (int)setting('core.client');

		// 13.40 and up
		if ($clientVersion >= 1340) {
			$creatureBoost = $db->query("SELECT * FROM " . $db->tableName('boosted_creature'))->fetchAll();
			$bossBoost     = $db->query("SELECT * FROM " . $db->tableName('boosted_boss'))->fetchAll();
			die(json_encode([
				'boostedcreature' => true,
				'creatureraceid'  => intval($creatureBoost[0]['raceid']),
				'bossraceid'      => intval($bossBoost[0]['raceid'])
			]));
		}

		// lower clients
		$boostedCreature = BoostedCreature::first();
		die(json_encode([
			'boostedcreature' => true,
			'raceid' => $boostedCreature->raceid
		]));

	case 'login':

		$ip = configLua('ip');
		$port = configLua('gameProtocolPort');

		// default world info
		$world = [
			'id' => 0,
			'name' => $config['lua']['serverName'],
			'externaladdress' => $ip,
			'externalport' => $port,
			'externaladdressprotected' => $ip,
			'externalportprotected' => $port,
			'externaladdressunprotected' => $ip,
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

		$account = Account::query();
		if ($inputEmail) { // login by email
			$account->where('email', $inputEmail);
		}
		else if($inputAccountName) { // login by account name
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

			sendError(($inputEmail ? 'Email' : 'Account name') . ' or password is not correct.');
		}

		$current_password = encrypt((USE_ACCOUNT_SALT ? $account->salt : '') . $request->password);
		if (!$account || $account->password != $current_password) {
			$limiter->increment($ip);
			if ($limiter->exceeded($ip)) {
				sendError($ban_msg);
			}

			sendError(($inputEmail ? 'Email' : 'Account name') . ' or password is not correct.');
		}

		$twoFactorAuth = TwoFactorAuth::getInstance($account->id);

		$code = '';
		if ($twoFactorAuth->isActive()) {
			if ($twoFactorAuth->getAuthType() === TwoFactorAuth::TYPE_EMAIL) {
				$code = $request->emailcode ?? false;
			}
			else if ($twoFactorAuth->getAuthType() === TwoFactorAuth::TYPE_APP) {
				$code = $request->token ?? false;
			}
		}

		$error = '';
		$errorCode = 6;
		if (!$twoFactorAuth->processClientLogin($code, $error, $errorCode)) {
			$limiter->increment($ip);
			if ($limiter->exceeded($ip)) {
				sendError($ban_msg);
			}

			sendError($error, $errorCode);
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

		$worlds = [$world];
		$playdata = compact('worlds', 'characters');

		$sessionKey = ($inputEmail !== false) ? $inputEmail : $inputAccountName; // email or account name
		$sessionKey .= "\n" . $request->password; // password
		if (!fieldExist('istutorial', 'players')) {
			$sessionKey .= "\n";
		}
		$sessionKey .= ($twoFactorAuth->isActive() && strlen($account->{'2fa_secret'}) > 5) ? $account->{'2fa_secret'} : '';

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
