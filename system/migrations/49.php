<?php
/**
 * @var OTS_DB_MySQL $db
 */

use MyAAC\Models\Account as AccountModel;

$time = time();

$accountId = getSession('account') ?? 1;
if (!defined('MYAAC_INSTALL')) {
	$accountModel = AccountModel::where('web_flags', 3)->first();
	if ($accountModel) {
		$accountId = $accountModel->id;
	}
}

function insert_sample_if_not_exist($p): void
{
	global $time, $accountId;

	$player = new OTS_Player();
	$player->find($p['name']);

	if (!$player->isLoaded()) {

		$player->setData([
			'name' => $p['name'],
			'group_id' => 1,
			'account_id' => $accountId,
			'level' => $p['level'],
			'vocation' => $p['vocation_id'],
			'health' => $p['health'],
			'healthmax' => $p['healthmax'],
			'experience' => $p['experience'],
			'lookbody' => 118,
			'lookfeet' => 114,
			'lookhead' => 38,
			'looklegs' => 57,
			'looktype' => $p['looktype'],
			'maglevel' => 0,
			'mana' => $p['mana'],
			'manamax' => $p['manamax'],
			'manaspent' => 0,
			'soul' => $p['soul'],
			'town_id' => 1,
			'posx' => 1000,
			'posy' => 1000,
			'posz' => 7,
			'conditions' => '',
			'cap' => $p['cap'],
			'sex' => 1,
			'lastlogin' => $time,
			'lastip' => 2130706433,
			'save' => 1,
			'lastlogout' => $time,
			'balance' => 0,
			'created' => $time,
			'hide' => 1,
			'comment' => '',
		]);

		$player->save();
	}
}

$up = function () use ($db) {
	if (!$db->hasTable('players')) {
		return;
	}

	insert_sample_if_not_exist(['name' => 'Rook Sample', 'level' => 1, 'vocation_id' => 0, 'health' => 150, 'healthmax' => 150, 'experience' => 0, 'looktype' => 130, 'mana' => 0, 'manamax' => 0, 'soul' => 100, 'cap' => 400]);
	insert_sample_if_not_exist(['name' => 'Sorcerer Sample', 'level' => 8, 'vocation_id' => 1, 'health' => 185, 'healthmax' => 185, 'experience' => 4200, 'looktype' => 130, 'mana' => 90, 'manamax' => 90, 'soul' => 100, 'cap' => 470]);
	insert_sample_if_not_exist(['name' => 'Druid Sample', 'level' => 8, 'vocation_id' => 2, 'health' => 185, 'healthmax' => 185, 'experience' => 4200, 'looktype' => 130, 'mana' => 90, 'manamax' => 90, 'soul' => 100, 'cap' => 470]);
	insert_sample_if_not_exist(['name' => 'Paladin Sample', 'level' => 8, 'vocation_id' => 3, 'health' => 185, 'healthmax' => 185, 'experience' => 4200, 'looktype' => 129, 'mana' => 90, 'manamax' => 90, 'soul' => 100, 'cap' => 470]);
	insert_sample_if_not_exist(['name' => 'Knight Sample', 'level' => 8, 'vocation_id' => 4, 'health' => 185, 'healthmax' => 185, 'experience' => 4200, 'looktype' => 131, 'mana' => 90, 'manamax' => 90, 'soul' => 100, 'cap' => 470]);
	insert_sample_if_not_exist(['name' => 'Monk Sample', 'level' => 8, 'vocation_id' => 9, 'health' => 185, 'healthmax' => 185, 'experience' => 4200, 'looktype' => 128, 'mana' => 90, 'manamax' => 90, 'soul' => 100, 'cap' => 470]);

	if (defined('MYAAC_INSTALL')) {
		global $locale;

		success($locale['step_database_imported_players']);
	}

	require_once __DIR__ . '/20.php';
	updateHighscoresIdsHidden();
};

$down = function () {
	// nothing
};
