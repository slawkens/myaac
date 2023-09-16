<?php
/**
 * Teleport Admin Tool
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @author    Lee
 * @copyright 2020 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\Player;
use MyAAC\Models\PlayerOnline;

defined('MYAAC') or die('Direct access not allowed!');

$title = 'Mass Teleport Actions';

csrfProtect();

function admin_teleport_position($x, $y, $z) {
	if (!Player::query()->update([
		'posx' => $x, 'posy' => $y, 'posz' => $z
	])) {
		displayMessage('Failed to execute query. Probably already updated.');
		return;
	}

	displayMessage('Player\'s position updated.', true);
}

function admin_teleport_town($town_id) {
	if (!Player::query()->update([
		'town_id' => $town_id,
	])) {
		displayMessage('Failed to execute query. Probably already updated.');
		return;
	}

	displayMessage('Player\'s town updated.', true);
}

if (isset($_POST['action']) && $_POST['action'])    {

	$action = $_POST['action'];

	if (preg_match("/[^A-z0-9_\-]/", $action)) {
		displayMessage('Invalid action.');
	} else {

		$playersOnline = 0;
		if($db->hasTable('players_online')) {// tfs 1.0
			$playersOnline = PlayerOnline::count();
		} else {
			$playersOnline = Player::online()->count();
		}

		if ($playersOnline > 0) {
			displayMessage('Please, close the server before execute this action otherwise players will not be affected.');
			return;
		}

		$town_id = isset($_POST['town_id']) ? intval($_POST['town_id']) : null;
		$posx = isset($_POST['posx']) ? intval($_POST['posx']) : null;
		$posy = isset($_POST['posy']) ? intval($_POST['posy']) : null;
		$posz = isset($_POST['posz']) ? intval($_POST['posz']) : null;
		$to_temple = $_POST['to_temple'] ?? null;

		switch ($action) {
			case 'set-town':
				if (!$town_id) {
					displayMessage('Please fill all inputs');
					return;
				}

				if (!isset($config['towns'][$town_id])) {
					displayMessage('Specified town does not exist');
					return;
				}

				admin_teleport_town($town_id);
				break;
			case 'set-position':
				if (!$to_temple &&  ($posx < 0 || $posx > 65535 || $posy < 0 || $posy > 65535|| $posz < 0 || $posz > 16)) {
					displayMessage('Invalid Position');
					return;
				}

				admin_teleport_position($posx, $posy, $posz);
				break;
			default:
				displayMessage('Action ' . $action . 'not found.');
		}
	}

}
else {
	$twig->display('admin.tools.teleport.html.twig', array());
}


function displayMessage($message, $success = false) {
	global $twig;

	$success ? success($message): error($message);
	$twig->display('admin.tools.teleport.html.twig', array());
}
