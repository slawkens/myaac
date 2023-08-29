<?php

/**
 * Account Admin Tool
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @author    Lee
 * @copyright 2020 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\Account;

defined('MYAAC') or die('Direct access not allowed!');

$title = 'Mass Account Actions';

$hasCoinsColumn = $db->hasColumn('accounts', 'coins');
$hasPointsColumn = $db->hasColumn('accounts', 'premium_points');
$freePremium = $config['lua']['freePremium'];

function admin_give_points($points)
{
	global $db, $hasPointsColumn;

	if (!$hasPointsColumn) {
		displayMessage('Points not supported.');
		return;
	}


	$statement = $db->prepare('UPDATE `accounts` SET `premium_points` = `premium_points` + :points');
	if (!$statement) {
		displayMessage('Failed to prepare query statement.');
		return;
	}

	if (!Account::query()->increment('premium_points', $points)) {
		displayMessage('Failed to add points.');
		return;
	}
	displayMessage($points . ' points added to all accounts.', true);
}

function admin_give_coins($coins)
{
	global $db, $hasCoinsColumn;

	if (!$hasCoinsColumn) {
		displayMessage('Coins not supported.');
		return;
	}

	if (!Account::query()->increment('coins', $coins)) {
		displayMessage('Failed to add coins.');
		return;
	}

	displayMessage($coins . ' coins added to all accounts.', true);
}

function query_add_premium($column, $value_query, $condition_query = '1=1', $params = [])
{
	global $db;

	$statement = $db->prepare("UPDATE `accounts` SET `{$column}` = $value_query WHERE $condition_query");
	if (!$statement) {
		displayMessage('Failed to prepare query statement.');
		return false;
	}

	if (!$statement->execute($params)) {
		displayMessage('Failed to add premium days.');
		return false;
	}

	return true;
}

function admin_give_premdays($days)
{
	global $db, $freePremium;

	if ($freePremium) {
		displayMessage('Premium days not supported. Free Premium enabled.');
		return;
	}

	$value = $days * 86400;
	$now = time();
	// othire
	if ($db->hasColumn('accounts', 'premend')) {
		// append premend
		if (query_add_premium('premend', '`premend` + :value', '`premend` > :now', ['value' => $value, 'now' => $now])) {
			// set premend
			if (query_add_premium('premend', ':value', '`premend` <= :now', ['value' => $now + $value, 'now' => $now])) {
				displayMessage($days . ' premium days added to all accounts.', true);
				return;
			} else {
				displayMessage('Failed to execute set query.');
				return;
			}
		} else {
			displayMessage('Failed to execute append query.');
			return;
		}

		return;
	}

	// tfs 0.x
	if ($db->hasColumn('accounts', 'premdays')) {
		// append premdays
		if (query_add_premium('premdays', '`premdays` + :value', '1=1', ['value' => $days])) {
			// append lastday
			if (query_add_premium('lastday', '`lastday` + :value', '`lastday` > :now', ['value' => $value, 'now' => $now])) {
				// set lastday
				if (query_add_premium('lastday', ':value', '`lastday` <= :now', ['value' => $now + $value, 'now' => $now])) {
					displayMessage($days . ' premium days added to all accounts.', true);
					return;
				} else {
					displayMessage('Failed to execute set query.');
					return;
				}

				return;
			} else {
				displayMessage('Failed to execute append query.');
				return;
			}
		} else {
			displayMessage('Failed to execute set days query.');
			return;
		}

		return;
	}

	// tfs 1.x
	if ($db->hasColumn('accounts', 'premium_ends_at')) {
		// append premium_ends_at
		if (query_add_premium('premium_ends_at', '`premium_ends_at` + :value', '`premium_ends_at` > :now', ['value' => $value, 'now' => $now])) {
			// set premium_ends_at
			if (query_add_premium('premium_ends_at', ':value', '`premium_ends_at` <= :now', ['value' => $now + $value, 'now' => $now])) {
				displayMessage($days . ' premium days added to all accounts.', true);
				return;
			} else {
				displayMessage('Failed to execute set query.');
				return;
			}
		} else {
			displayMessage('Failed to execute append query.');
			return;
		}

		return;
	}

	displayMessage('Premium Days not supported.');
}

if (isset($_POST['action']) && $_POST['action']) {

	$action = $_POST['action'];

	if (preg_match("/[^A-z0-9_\-]/", $action)) {
		displayMessage('Invalid action.');
	} else {
		$value = isset($_POST['value']) ? intval($_POST['value']) : 0;

		if (!$value) {
			displayMessage('Please fill all inputs');
		} else {
			switch ($action) {
				case 'give-points':
					admin_give_points($value);
					break;
				case 'give-coins':
					admin_give_coins($value);
					break;
				case 'give-premdays':
					admin_give_premdays($value);
					break;
				default:
					displayMessage('Action ' . $action . 'not found.');
			}
		}
	}
}
else {
	$twig->display('admin.tools.account.html.twig', array(
		'hasCoinsColumn' => $hasCoinsColumn,
		'hasPointsColumn' => $hasPointsColumn,
		'freePremium' => $freePremium,
	));
}

function displayMessage($message, $success = false) {
	global $twig, $hasCoinsColumn, $hasPointsColumn, $freePremium;

	$success ? success($message): error($message);

	$twig->display('admin.tools.account.html.twig', array(
		'hasCoinsColumn' => $hasCoinsColumn,
		'hasPointsColumn' => $hasPointsColumn,
		'freePremium' => $freePremium,
	));
}
