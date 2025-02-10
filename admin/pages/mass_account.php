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

csrfProtect();

$hasCoinsColumn = $db->hasColumn('accounts', 'coins');
$hasPointsColumn = $db->hasColumn('accounts', 'premium_points');
$freePremium = $config['lua']['freePremium'];

function admin_give_points($points)
{
	global $hasPointsColumn;

	if (!$hasPointsColumn) {
		displayMessage('Points not supported.');
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
	global $hasCoinsColumn;

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
		if (Account::where('premend', '>', $now)->increment('premend', $value)) {
			// set premend
			if (Account::where('premend', '<=', $now)->update(['premend' => $now + $value])) {
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
		if (Account::query()->update(['premdays' => $days])) {
			// append lastday
			if (Account::where('lastday', '>', $now)->increment('lastday', $value)) {
				// set lastday
				if (Account::where('lastday', '<=', $now)->update(['lastday' => $now + $value])) {
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
		if (Account::where('premium_ends_at', '>', $now)->increment('premium_ends_at', $value)) {
			// set premium_ends_at
			if (Account::where('premium_ends_at', '<=', $now)->update(['premium_ends_at' => $now + $value])) {
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

if (!empty(ACTION) && isRequestMethod('post')) {

	$action = ACTION;

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
