<?php
/**
 * Change characters name
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

$title = 'Change Name';
require PAGES . 'account/base.php';

if(!$logged) {
	return;
}

$player_id = isset($_POST['player_id']) ? (int)$_POST['player_id'] : NULL;
$name = isset($_POST['name']) ? stripslashes(ucwords(strtolower($_POST['name']))) : NULL;
if((!setting('core.account_change_character_name')))
	echo 'Changing character name for premium points is disabled on this server.';
else
{
	$points = $account_logged->getCustomField(setting('core.donate_column'));
	if(isset($_POST['changenamesave']) && $_POST['changenamesave'] == 1) {
		if($points < setting('core.account_change_character_name_price'))
			$errors[] = 'You need ' . setting('core.account_change_character_name_price') . ' premium points to change name. You have <b>'.$points.'<b> premium points.';

		$minLength = setting('core.create_character_name_min_length');
		$maxLength = setting('core.create_character_name_max_length');

		if(empty($errors) && empty($name))
			$errors[] = 'Please enter a new name for your character!';
		else if(strlen($name) > $maxLength)
			$errors['name'] = 'Name is too long. Max. length <b>'.$maxLength.'</b> letters.';
		else if(strlen($name) < $minLength)
			$errors['name'] = 'Name is too short. Min. length <b>'.$minLength.'</b> letters.';

		if(empty($errors))
		{
			if(!Validator::characterName($name)) {
				$errors[] = Validator::getLastError();
			}

			if(!admin() && !Validator::newCharacterName($name)) {
				$errors[] = Validator::getLastError();
			}
		}

		if(empty($errors)) {
			$player = new OTS_Player();
			$player->load($player_id);
			if($player->isLoaded()) {
				$player_account = $player->getAccount();
				if($account_logged->getId() == $player_account->getId()) {
					if ($player->isDeleted()) {
						$errors[] = 'This character is deleted.';
					}

					if($player->isOnline()) {
						$errors[] = 'This character is online.';
					}

					if(empty($errors)) {
						$show_form = false;
						$old_name = $player->getName();
						$player->setName($name);
						$player->save();

						if ($db->hasTable('player_deaths') &&
							$db->hasColumn('player_deaths', 'mostdamage_is_player') &&
							$db->hasColumn('player_deaths', 'killed_by')) {

							$namesToChange = $db->query('SELECT `player_id`, `time`, `is_player`, `killed_by`, `mostdamage_is_player`, `mostdamage_by` FROM `player_deaths` WHERE (`is_player` = 1 AND `killed_by` = ' . $db->quote($old_name) . ') OR (`mostdamage_is_player` = 1 AND `mostdamage_by` = ' . $db->quote($old_name) . ');');

							if ($namesToChange->rowCount() > 0) {
								foreach ($namesToChange->fetchAll(PDO::FETCH_ASSOC) as $row) {
									$changeKey = '';
									if ($row['is_player'] == '1' && $row['killed_by'] == $old_name) {
										$changeKey = 'killed_by';
									} else if ($row['mostdamage_is_player'] == '1' && $row['mostdamage_by'] == $old_name) {
										$changeKey = 'mostdamage_by';
									}

									if (!empty($changeKey)) {
										$db->update('player_deaths', [$changeKey => $name], ['player_id' => $row['player_id'], 'time' => $row['time']]);
									}
								}
							}
						}

						$account_logged->setCustomField(setting('core.donate_column'), $points - setting('core.account_change_character_name_price'));
						$account_logged->logAction('Changed name from <b>' . $old_name . '</b> to <b>' . $player->getName() . '</b>.');
						$twig->display('success.html.twig', array(
							'title' => 'Character Name Changed',
							'description' => 'The character <b>'.$old_name.'</b> name has been changed to <b>' . $player->getName() . '</b>.'
						));
					}
				}
				else {
					$errors[] = 'Character is not on your account.';
				}
			}
			else {
				$errors[] = "Character with this name doesn't exist.";
			}
		}
	}

	if($show_form) {
		if(!empty($errors)) {
			$twig->display('error_box.html.twig', array('errors' => $errors));
		}

		$twig->display('account.characters.change-name.html.twig', array(
			'points' => $points,
			'errors' => $errors
			//'account_players' => $account_logged->getPlayersList()
		));
	}
}
