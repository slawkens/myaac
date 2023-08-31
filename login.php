<?php

use MyAAC\Models\BoostedCreature;
use MyAAC\Models\PlayerOnline;
use MyAAC\Models\Account;
use MyAAC\Models\Player;

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
		$boostedCreature = BoostedCreature::latest();
		die(json_encode([
			'boostedcreature' => true,
			'raceid' => $boostedCreature->raceid
		]));
	break;

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

		$account = Account::query();
		if ($inputEmail != false) { // login by email
			$account->where('email', $inputEmail);
		}
		else if($inputAccountName != false) { // login by account name
			$account->where('name', $inputAccountName);
		}

		$account = $account->first();
		if (!$account) {
			sendError(($inputEmail != false ? 'Email' : 'Account name') . ' or password is not correct.');
		}

		$current_password = encrypt((USE_ACCOUNT_SALT ? $account->salt : '') . $request->password);
		if (!$account || $account->password != $current_password) {
			sendError(($inputEmail != false ? 'Email' : 'Account name') . ' or password is not correct.');
		}

		$accountHasSecret = false;
		if (fieldExist('secret', 'accounts')) {
			$accountSecret = $account->secret;
			if ($accountSecret != null && $accountSecret != '') {
				$accountHasSecret = true;
				if ($inputToken === false) {
					sendError('Submit a valid two-factor authentication token.', 6);
				} else {
					require_once LIBS . 'rfc6238.php';
					if (TokenAuth6238::verify($accountSecret, $inputToken) !== true) {
						sendError('Two-factor authentication failed, token is wrong.', 6);
					}
				}
			}
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
