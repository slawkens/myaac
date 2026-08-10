<?php

/**
 * Account Admin Tool
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @author    Lee
 * @author    gpedro
 * @copyright 2020 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\Account;

defined('MYAAC') or die('Direct access not allowed!');

$title = 'Mass Account Actions';

csrfProtect();

$hasPointsColumn = $db->hasColumn('accounts', 'premium_points');
$freePremium = getBoolean(configLua('freePremium'));

function admin_give_points(int $points): void
{
	global $hasPointsColumn;

	if (!$hasPointsColumn) {
		displayMessage('Points not supported.');
		return;
	}

	if (Account::query()->increment('premium_points', $points) === false) {
		displayMessage('Failed to add points.');
		return;
	}

	displayMessage($points . ' points added to all accounts.', true);
}

function admin_give_coins(int $coins): void
{
	if (!HAS_ACCOUNT_COINS) {
		displayMessage('Coins not supported.');
		return;
	}

	if (Account::query()->increment('coins', $coins) === false) {
		displayMessage('Failed to add coins.');
		return;
	}

	displayMessage($coins . ' coins added to all accounts.', true);
}

function admin_give_premdays(int $days): void
{
	global $db, $freePremium;

	if ($freePremium) {
		displayMessage('Premium days not supported. Free Premium enabled.');
		return;
	}

	$value = $days * 86400;
	$now = time();

	// tfs 1.x & othire
	if ($db->hasColumn('accounts', 'premium_ends_at') || $db->hasColumn('accounts', 'premend')) {
		$column = $db->hasColumn('accounts', 'premium_ends_at') ? 'premium_ends_at' : 'premend';
		// append column
		if (Account::where($column, '>', $now)->increment($column, $value) !== false) {
			// set column
			if (Account::where($column, '<=', $now)->update([$column => $now + $value]) !== false) {
				displayMessage($days . ' premium days added to all accounts.', true);
			} else {
				displayMessage("Failed to execute update $column query.");
			}
		} else {
			displayMessage("Failed to execute increment $column query.");
		}

		return;
	}

	if ($db->hasColumn('accounts', 'premdays')) {
		if (hasLastDayPremiumEndColumn()) {
			// canary
			if (Account::where('lastday', '>', $now)->increment('lastday', $value) !== false) {
				if (Account::where('lastday', '<=', $now)->update(['lastday' => $now + $value]) !== false) {
					if (Account::query()->increment('premdays', $days) !== false) {
						displayMessage($days . ' premium days added to all accounts.', true);
					}
					else {
						displayMessage('Failed to execute increment premdays query.');
					}
				} else {
					displayMessage('Failed to execute update lastday query.');
				}
			} else {
				displayMessage('Failed to execute increment lastday query.');
			}

			return;
		}
		else {
			// tfs 0.x
			if (Account::query()->increment('premdays', $days) !== false) {
				if (Account::query()->update(['lastday' => $now]) !== false) {
					displayMessage($days . ' premium days added to all accounts.', true);
				} else {
					displayMessage('Failed to execute update lastday query.');
				}
			} else {
				displayMessage('Failed to execute increment premdays query.');
			}
		}

		return;
	}

	displayMessage('Premium Days adding not supported for your OTS engine.');
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
		'hasCoinsColumn' => HAS_ACCOUNT_COINS,
		'hasPointsColumn' => $hasPointsColumn,
		'freePremium' => $freePremium,
	));
}

function displayMessage(string $message, bool $success = false): void
{
	global $twig, $hasPointsColumn, $freePremium;

	$success ? success($message): error($message);

	$twig->display('admin.tools.account.html.twig', array(
		'hasCoinsColumn' => HAS_ACCOUNT_COINS,
		'hasPointsColumn' => $hasPointsColumn,
		'freePremium' => $freePremium,
	));
}
