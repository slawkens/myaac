<?php
require_once 'common.php';
require_once 'config.php';
require_once 'config.local.php';
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
		$playersonline = $db->query("select count(*) from `players_online`")->fetchAll();
		die(json_encode([
			'playersonline' => (intval($playersonline[0][0])),
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
		$boostDB = $db->query("select * from " . $db->tableName('boosted_creature'))->fetchAll();
		foreach ($boostDB as $Tableboost) {
		die(json_encode([
			'boostedcreature' => true,
			'raceid' => intval($Tableboost['raceid'])
		]));
		}
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
		$account = new OTS_Account();

		$inputEmail = $request->email ?? false;
		$inputAccountName = $request->accountname ?? false;
		$inputToken = $request->token ?? false;

		if ($inputEmail != false) { // login by email
			$account->findByEmail($request->email);
		}
		else if($inputAccountName != false) { // login by account name
			$account->find($inputAccountName);
		}

		$config_salt_enabled = fieldExist('salt', 'accounts');
		$current_password = encrypt(($config_salt_enabled ? $account->getCustomField('salt') : '') . $request->password);

		if (!$account->isLoaded() || $account->getPassword() != $current_password) {
			sendError(($inputEmail != false ? 'Email' : 'Account name') . ' or password is not correct.');
		}

		//log_append('test.log', var_export($account->getCustomField('secret'), true));
		$accountHasSecret = false;
		if (fieldExist('secret', 'accounts')) {
			$accountSecret = $account->getCustomField('secret');
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

		$players = $db->query("select {$columns} from players where account_id = " . $account->getId() . " AND deletion = 0");
		if($players && $players->rowCount() > 0) {
			$players = $players->fetchAll();

			$highestLevelId = 0;
			$highestLevel = 0;
			foreach ($players as $player) {
				if ($player['level'] >= $highestLevel) {
					$highestLevel = $player['level'];
					$highestLevelId = $player['id'];
				}
			}

			foreach ($players as $player) {
				$characters[] = create_char($player, $highestLevelId);
			}
		}

		if (fieldExist('premdays', 'accounts') && fieldExist('lastday', 'accounts')) {
			$save = false;
			$timeNow = time();
			$query = $db->query("select `premdays`, `lastday` from `accounts` where `id` = " . $account->getId());
			if ($query->rowCount() > 0) {
				$query = $query->fetch();
				$premDays = (int)$query['premdays'];
				$lastDay = (int)$query['lastday'];
				$lastLogin = $lastDay;
			} else {
				sendError("Error while fetching your account data. Please contact admin.");
			}
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
				$db->query("update `accounts` set `premdays` = " . $premDays . ", `lastday` = " . $lastDay . " where `id` = " . $account->getId());
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

		//log_append('slaw.log', $sessionKey);

		$session = [
			'sessionkey' => $sessionKey,
			'lastlogintime' => 0,
			'ispremium' => $config['lua']['freePremium'] || $account->isPremium(),
			'premiumuntil' => ($account->getPremDays()) > 0 ? (time() + ($account->getPremDays() * 86400)) : 0,
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
	global $config;
	return [
		'worldid' => 0,
		'name' => $player['name'],
		'ismale' => intval($player['sex']) === 1,
		'tutorial' => isset($player['istutorial']) && $player['istutorial'],
		'level' => intval($player['level']),
		'vocation' => $config['vocations'][$player['vocation']],
		'outfitid' => intval($player['looktype']),
		'headcolor' => intval($player['lookhead']),
		'torsocolor' => intval($player['lookbody']),
		'legscolor' => intval($player['looklegs']),
		'detailcolor' => intval($player['lookfeet']),
		'addonsflags' => intval($player['lookaddons']),
		'ishidden' => isset($player['deletion']) && (int)$player['deletion'] === 1,
		'istournamentparticipant' => false,
		'ismaincharacter' => $highestLevelId == $player['id'],
		'dailyrewardstate' => isset($player['isreward']) ? intval($player['isreward']) : 0,
		'remainingdailytournamentplaytime' => 0
	];
}
