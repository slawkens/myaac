<?php

namespace MyAAC\Services;

use MyAAC\Cache\Cache;
use MyAAC\Models\Config;
use MyAAC\Models\Player;
use MyAAC\Models\PlayerOnline;

class StatusService
{
	public function checkStatus(): array
	{
		$status = [];
		$status['online'] = false;
		$status['players'] = 0;
		$status['playersMax'] = 0;
		$status['lastCheck'] = 0;
		$status['uptime'] = '0h 0m';
		$status['monsters'] = 0;

		if(setting('core.status_enabled') === false) {
			return $status;
		}

		$fetch_from_db = true;
		/**
		 * @var Cache $cache
		 */
		$cache = app()->get('cache');
		if($cache->enabled()) {
			$tmp = '';
			if($cache->fetch('status', $tmp)) {
				return unserialize($tmp);
			}
		}

		$status_query = Config::where('name', 'LIKE', '%status%')->get();
		if (!$status_query || !$status_query->count()) {
			foreach($status as $key => $value) {
				registerDatabaseConfig('status_' . $key, $value);
			}
		} else {
			foreach($status_query as $tmp) {
				$status[str_replace('status_', '', $tmp->name)] = $tmp->value;
			}
		}

		$configStatustimeout = configLua('statustimeout');
		if(isset($configStatustimeout)) {
			configLua(['statusTimeout', $configStatustimeout]);
		}

		// get status timeout from server config
		$status_timeout = eval('return ' . configLua('statusTimeout') . ';') / 1000 + 1;
		$status_interval = setting('core.status_interval');
		if($status_interval && $status_timeout < $status_interval) {
			$status_timeout = $status_interval;
		}

		if($status['lastCheck'] + $status_timeout < time()) {
			return $this->updateStatus();
		}
	}

	public function updateStatus(): array
	{
		$db = app()->get('db');
		$cache = app()->get('cache');

		// get server status and save it to database
		$serverInfo = new \OTS_ServerInfo(setting('core.status_ip'), setting('core.status_port'));
		$serverStatus = $serverInfo->status();
		if(!$serverStatus) {
			$status['online'] = false;
			$status['players'] = 0;
			$status['playersMax'] = 0;
		}
		else {
			$status['lastCheck'] = time(); // this should be set only if server respond

			$status['online'] = true;
			$status['players'] = $serverStatus->getOnlinePlayers(); // counts all players logged in-game, or only connected clients (if enabled on server side)
			$status['playersMax'] = $serverStatus->getMaxPlayers();

			// for status afk thing
			if (setting('core.online_afk')) {
				// get amount of players that are currently logged in-game, including disconnected clients (exited)
				if($db->hasTable('players_online')) { // tfs 1.x
					$status['playersTotal'] = PlayerOnline::count();
				}
				else {
					$status['playersTotal'] = Player::online()->count();
				}
			}

			$uptime = $status['uptime'] = $serverStatus->getUptime();
			$m = date('m', $uptime);
			$m = $m > 1 ? "$m months, " : ($m == 1 ? 'month, ' : '');
			$d = date('d', $uptime);
			$d = $d > 1 ? "$d days, " : ($d == 1 ? 'day, ' : '');
			$h = date('H', $uptime);
			$min = date('i', $uptime);
			$status['uptimeReadable'] = "{$m}{$d}{$h}h {$min}m";

			$status['monsters'] = $serverStatus->getMonstersCount();
			$status['motd'] = $serverStatus->getMOTD();

			$status['mapAuthor'] = $serverStatus->getMapAuthor();
			$status['mapName'] = $serverStatus->getMapName();
			$status['mapWidth'] = $serverStatus->getMapWidth();
			$status['mapHeight'] = $serverStatus->getMapHeight();

			$status['server'] = $serverStatus->getServer();
			$status['serverVersion'] = $serverStatus->getServerVersion();
			$status['clientVersion'] = $serverStatus->getClientVersion();
		}

		if($cache->enabled()) {
			$cache->set('status', serialize($status), 120);
		}

		$tmpVal = null;
		foreach($status as $key => $value) {
			if(fetchDatabaseConfig('status_' . $key, $tmpVal)) {
				updateDatabaseConfig('status_' . $key, $value);
			}
			else {
				registerDatabaseConfig('status_' . $key, $value);
			}
		}

		return $status;
	}
}
