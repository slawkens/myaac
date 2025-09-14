<?php
/**
 * Pass leadership
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

require __DIR__ . '/base.php';

$guild_name = isset($_REQUEST['guild']) ? urldecode($_REQUEST['guild']) : NULL;
$pass_to = isset($_REQUEST['player']) ? stripslashes($_REQUEST['player']) : NULL;
if(!Validator::guildName($guild_name)) {
	$errors[] = Validator::getLastError();
}

if(empty($errors)) {
	$guild = new OTS_Guild();
	$guild->find($guild_name);
	if(!$guild->isLoaded()) {
		$errors[] = "Guild with name <b>" . $guild_name . "</b> doesn't exist.";
	}
}

if(empty($errors)) {
	if(isset($_POST['todo']) && $_POST['todo'] == 'save') {
		if(!Validator::characterName($pass_to)) {
			$errors2[] = 'Invalid player name format.';
		}

		if(empty($errors2)) {
			$to_player = new OTS_Player();
			$to_player->find($pass_to);
			if(!$to_player->isLoaded()) {
				$errors2[] = 'Player with name <b>'.$pass_to.'</b> doesn\'t exist.';
			} else if ($to_player->isDeleted()) {
				$errors2[] = "Character with name <b>$pass_to</b> has been deleted.";
			}

			if(empty($errors2)) {
				$to_player_rank = $to_player->getRank();
				if($to_player_rank->isLoaded()) {
					$to_player_guild = $to_player_rank->getGuild();
					if($to_player_guild->getId() != $guild->getId()) {
						$errors2[] = 'Player with name <b>'.$to_player->getName().'</b> isn\'t from your guild.';
					}
				}
				else {
					$errors2[] = 'Player with name <b>'.$to_player->getName().'</b> isn\'t from your guild.';
				}
			}
		}
	}
}
if(empty($errors) && empty($errors2)) {
	if($logged) {
		$guild_leader_char = $guild->getOwner();
		$guild_leader = false;
		$account_players = $account_logged->getPlayersList();
		foreach($account_players as $player) {
			if($guild_leader_char->getId() == $player->getId()) {
				$guild_vice = true;
				$guild_leader = true;
				$level_in_guild = 3;
			}
		}

		$saved = false;
		if($guild_leader) {
			if(isset($_POST['todo']) && $_POST['todo'] == 'save') {
				$query = $db->query('SELECT `id` FROM `guild_ranks` WHERE `guild_id` = ' . $guild->getId() . ' ORDER BY `level` ASC LIMIT 1')->fetch();
				if($query) {
					$guild_leader_char->setRankId($query['id'], $guild->getId());
				}

				$query = $db->query('SELECT `id` FROM `guild_ranks` WHERE `guild_id` = ' . $guild->getId() . ' ORDER BY `level` DESC LIMIT 1')->fetch();
				if($query) {
					$to_player->setRankId($query['id'], $guild->getId());
				}

				$guild->setOwner($to_player);
				$guild->save();
				$saved = true;
			}
			if($saved) {
				$twig->display('success.html.twig', array(
					'title' => 'Leadership passed',
					'description' => '<b>'.$to_player->getName().'</b> is now a Leader of <b>'.$guild_name.'</b>.',
					'custom_buttons' => '<div style="text-align:center"><form action="' . getLink('guilds') . '/' . $guild->getName().'" METHOD=post>' . $twig->render('buttons.back.html.twig') . '</form></div>'
				));
			}
			else {
				$twig->display('guilds.pass_leadership.html.twig', array(
					'guild' => $guild
				));
			}
		}
		else {
			$errors[] = 'You are not a leader of guild!';
		}
	}
	else {
		$errors[] = "You are not logged. You can't manage guild.";
	}
}
if(empty($errors) && !empty($errors2)) {
	$twig->display('error_box.html.twig', array('errors' => $errors2));

	echo '<br/><div style="text-align:center"><form action="' . getLink('guilds') . '?guild='.$guild->getName().'&action=pass_leadership" method="post">' . $twig->render('buttons.back.html.twig') . '</form></div>';
}
if(!empty($errors)) {
	if(!empty($errors2)) {
		$errors = array_merge($errors, $errors2);
	}
	$twig->display('error_box.html.twig', array('errors' => $errors));

	echo '<br/><div style="text-align:center"><form action="' . getLink('guilds') . '" method="post">' . $twig->render('buttons.back.html.twig') . '</form></div>';
}
