<?php
/**
 * CHANGELOG modifier
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @author    Lee
 * @copyright 2020 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\Changelog as ModelsChangelog;

defined('MYAAC') or die('Direct access not allowed!');

$title = 'Changelog';

csrfProtect();

if (!hasFlag(FLAG_CONTENT_PAGES) && !superAdmin()) {
	echo 'Access denied.';
	return;
}

$use_datatable = true;
const CL_LIMIT = 600; // maximum changelog body length

$id = $_GET['id'] ?? 0;
require_once LIBS . 'changelog.php';

if(!empty($action))
{
	$id = $_POST['id'] ?? null;
	$body = isset($_POST['body']) ? stripslashes($_POST['body']) : null;
	$create_date = isset($_POST['createdate']) ? (int)strtotime($_POST['createdate'] ): null;
	$player_id = isset($_POST['player_id']) ? (int)$_POST['player_id'] : null;
	$type = isset($_POST['type']) ? (int)$_POST['type'] : null;
	$where = isset($_POST['where']) ? (int)$_POST['where'] : null;

	$errors = array();

	if($action == 'new') {

		if(isset($body) && Changelog::add($body, $type, $where, $player_id, $create_date, $errors)) {
			$body = '';
			$type = $where = $player_id = $create_date = 0;

			success('Added successful.');
		}
	}
	else if($action == 'delete') {
		if (Changelog::delete($id, $errors)) {
			success('Deleted successful.');
		}
	}
	else if($action == 'edit')
	{
		if(isset($id) && !isset($body)) {
			$cl = Changelog::get($id);
			$body = $cl['body'];
			$type = $cl['type'];
			$where = $cl['where'];
			$create_date = $cl['date'];
			$player_id = $cl['player_id'];
		}
		else {
			if(Changelog::update($id, $body, $type, $where, $player_id, $create_date,$errors)) {
				$action = $body = '';
				$type = $where = $player_id = $create_date = 0;

				success('Updated successful.');
			}
		}
	}
	else if($action == 'hide') {
		if (Changelog::toggleHidden($id, $errors, $status)) {
			success(($status == 1 ? 'Hide' : 'Show') . ' successful.');
		}
	}

	if(!empty($errors))
		error(implode(", ", $errors));
}

$changelogs = ModelsChangelog::orderBy('id')->get()->toArray();

$i = 0;

$log_type = [
	['id' => 1, 'icon' => 'added'],
	['id' => 2, 'icon' => 'removed'],
	['id' => 3, 'icon' => 'changed'],
	['id' => 4, 'icon' => 'fixed'],
];

$log_where = [
	['id' => 1, 'icon' => 'server'],
	['id' => 2, 'icon' => 'website'],
];

foreach($changelogs as $key => &$log)
{
	$log['type'] = getChangelogType($log['type']);
	$log['where'] = getChangelogWhere($log['where']);
}

if($action == 'edit' || $action == 'new') {
	if($action == 'edit') {
		$player = new OTS_Player();
		$player->load($player_id);
	}

	$account_players = $account_logged->getPlayersList();
	$account_players->orderBy('group_id', POT::ORDER_DESC);
	$twig->display('admin.changelog.form.html.twig', array(
		'action' => $action,
		'cl_link_form' => constant('ADMIN_URL').'?p=changelog',
		'cl_id' => $id ?? null,
		'body' => isset($body) ? escapeHtml($body) : '',
		'create_date' => $create_date ?? '',
		'player_id' => $player_id ?? null,
		'account_players' => $account_players,
		'type' => $type ?? 0,
		'where' => $where ?? 0,
		'log_type' => $log_type,
		'log_where' => $log_where,
	));
}
$twig->display('admin.changelog.html.twig', array(
	'changelogs' => $changelogs,
));
