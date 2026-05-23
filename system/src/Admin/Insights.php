<?php

namespace MyAAC\Admin;

use MyAAC\Cache\Cache;

class Insights
{
	private $db;

	public function __construct($db){
		$this->db = $db;
	}

	public function getLastLoggedPlayers(int $year, $month): array
	{
		return Cache::remember("admin_dashboard_insights_players_lastlogin_{$year}_{$month}", 5 * 60, function() use ($year, $month) {
			$lastLoggedPlayers = [];

			$getFromTo = $this->getFromTo($year, $month);
			$whereLastLogin = 'AND lastlogin >= ' . $getFromTo[0] . ' AND lastlogin <= ' . $getFromTo[1];

			if ($month === 'all') {
				$query = $this->db->query('SELECT count(id) as how_much, DATE_FORMAT(FROM_UNIXTIME(lastlogin), "%m.%Y") as lastdate FROM players WHERE lastlogin > 0 ' . $whereLastLogin . ' GROUP BY lastdate LIMIT 14');

				foreach ($query as $item) {
					$date = explode('.', $item['lastdate']);
					$monthName = date('F', mktime(0, 0, 0, $date[0], 10));;

					$lastLoggedPlayers[] = ['date' => $monthName, 'how_much' => $item['how_much']];
				}
			}
			else {
				$query = $this->db->query('SELECT count(id) as how_much, DATE_FORMAT(FROM_UNIXTIME(lastlogin), "%d.%m.%Y") as lastdate FROM players WHERE lastlogin > 0 ' . $whereLastLogin . ' GROUP BY lastdate LIMIT 14');

				foreach ($query as $item) {
					$lastLoggedPlayers[] = ['date' => $item['lastdate'], 'how_much' => $item['how_much']];
				}
			}

			return $lastLoggedPlayers;
		});
	}

	public function getLastCreatedAccounts(int $year, $month): array
	{
		return Cache::remember("admin_dashboard_insights_accounts_created_{$year}_{$month}", 10 * 60, function() use ($year, $month) {
			$lastCreatedAccounts = [];

			$getFromTo = $this->getFromTo($year, $month);
			$whereCreated = 'AND created >= ' . $getFromTo[0] . ' AND created <= ' . $getFromTo[1];

			if ($month == 'all') {
				$query = $this->db->query('SELECT count(id) as how_much, DATE_FORMAT(FROM_UNIXTIME(created), "%m.%Y") as createdDate FROM accounts WHERE created > 0 ' . $whereCreated . ' GROUP BY createdDate LIMIT 31');

				foreach ($query as $item) {
					$date = explode('.', $item['createdDate']);
					$monthName = date('F', mktime(0, 0, 0, $date[0], 10));;
					$lastCreatedAccounts[] = ['date' => $monthName, 'how_much' => $item['how_much']];
				}
			}
			else {
				$query = $this->db->query('SELECT count(id) as how_much, DATE_FORMAT(FROM_UNIXTIME(created), "%d.%m.%Y") as createdDate FROM accounts WHERE created > 0 ' . $whereCreated . ' GROUP BY createdDate LIMIT 31');

				foreach ($query as $item) {
					$lastCreatedAccounts[] = ['date' => $item['createdDate'], 'how_much' => $item['how_much']];
				}
			}

			return $lastCreatedAccounts;
		});
	}

	public function getFirstYear(): int
	{
		$query = $this->db->query('SELECT created FROM accounts WHERE created > 0 ORDER BY created LIMIT 1');
		if ($query->rowCount()) {
			$firstAccountCreated = $query->fetch()['created'];
		}
		else {
			$firstAccountCreated = time();
		}

		return (int)date('Y', $firstAccountCreated);
	}

	public function getMonths(): array
	{
		$months = [];

		$months['all'] = 'All';

		for ($i = 1; $i <= 12; $i++) {
			$months[$i] = date('F', mktime(0, 0, 0, $i, 10));
		}

		return $months;
	}

	private function getFromTo(int $year, $month): array
	{
		if ($month == 'all') {
			$firstMonth = 1;
			$lastMonth = 12;
		}
		else {
			$firstMonth = $month;
			$lastMonth = $month;
		}

		$from = date('U', mktime(0, 0, 0, $firstMonth, 1, $year));
		$to = date('U', mktime(0, 0, 0, $lastMonth, 31, $year));

		return [$from, $to];
	}
}
