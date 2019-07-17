<?php
/**
 * Change rank
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

if(!$logged) {
	$errors[] = 'You are not logged in. You can\'t change rank.';
}
else {
	$guild_name = isset($_REQUEST['guild']) ? urldecode($_REQUEST['guild']) : null;
	if(!Validator::guildName($guild_name))
		$errors[] = Validator::getLastError();
}

if(empty($errors))
{
	$guild = new OTS_Guild();
	$guild->find($guild_name);
	if(!$guild->isLoaded())
		$errors[] = 'Guild with name <b>' . $guild_name . '</b> doesn\'t exist.';
}

if(!empty($errors))
{
	$twig->display('error_box.html.twig', array('errors' => $errors));
	$twig->display('guilds.back_button.html.twig');

	return;
}

//check is it vice or/and leader account (leader has vice + leader rights)
$rank_list = $guild->getGuildRanksList();
$rank_list->orderBy('level', POT::ORDER_DESC);
$guild_leader = false;
$guild_vice = false;
$account_players = $account_logged->getPlayers();
foreach($account_players as $player)
{
	$player_rank = $player->getRank();
	if($player_rank->isLoaded()) {
		foreach($rank_list as $rank_in_guild)
		{
			if($rank_in_guild->getId() == $player_rank->getId())
			{
				$players_from_account_in_guild[] = $player->getName();
				if($player_rank->getLevel() > 1) {
					$guild_vice = true;
					$level_in_guild = $player_rank->getLevel();
				}
				if($guild->getOwner()->getId() == $player->getId()) {
					$guild_vice = true;
					$guild_leader = true;
				}
			}
		}
	}
}

if($guild_vice)
{
	$rid = 0;
	$sid = 0;
	foreach($rank_list as $rank)
	{
		if($guild_leader || $rank->getLevel() < $level_in_guild)
		{
			$ranks[$rid]['0'] = $rank->getId();
			$ranks[$rid]['1'] = $rank->getName();
			$rid++;

			if($db->hasColumn('players', 'rank_id'))
				$players_with_rank = $db->query('SELECT `id`, `rank_id` FROM `players` WHERE `rank_id` = ' . $rank->getId() . ' AND `deleted` = 0;');
			else
				$players_with_rank = $db->query('SELECT `players`.`id` as `id`, `' . GUILD_MEMBERS_TABLE . '`.`rank_id` as `rank_id` FROM `players`, `' . GUILD_MEMBERS_TABLE . '` WHERE `' . GUILD_MEMBERS_TABLE . '`.`rank_id` = ' . $rank->getId() . ' AND `players`.`id` = `' . GUILD_MEMBERS_TABLE . '`.`player_id` ORDER BY `name`;');

			$players_with_rank_number = $players_with_rank->rowCount();
			if(count($players_with_rank) > 0)
			{

				foreach($players_with_rank as $result)
				{
					$player = new OTS_Player();
					$player->load($result['id']);
					if(!$player->isLoaded())
						continue;

					if($guild->getOwner()->getId() != $player->getId() || $guild_leader)
					{
						$players_with_lower_rank[$sid][0] = $player->getName();
						$players_with_lower_rank[$sid][1] = $player->getName().' ('.$rank->getName().')';
						$sid++;
					}
				}
			}
		}
	}
	if(isset($_REQUEST['todo']) && $_REQUEST['todo'] == 'save')
	{
		$player_name = stripslashes($_REQUEST['name']);
		$new_rank = (int) $_REQUEST['rankid'];
		if(!Validator::characterName($player_name))
			$change_errors[] = 'Invalid player name format.';
		$rank = new OTS_GuildRank();
		$rank->load($new_rank);
		if(!$rank->isLoaded())
			$change_errors[] = 'Rank with this ID doesn\'t exist.';
		if($level_in_guild <= $rank->getLevel() && !$guild_leader)
			$change_errors[] = 'You can\'t set ranks with equal or higher level than your.';
		if(empty($change_errors))
		{
			$player_to_change = new OTS_Player();
			$player_to_change->find($player_name);
			if(!$player_to_change->isLoaded())
				$change_errors[] = 'Player with name '.$player_name.'</b> doesn\'t exist.';
			else
			{
				$player_in_guild = false;
				if($guild->getName() == $player_to_change->getRank()->getGuild()->getName() || $guild_leader)
				{
					$player_in_guild = true;
					$player_has_lower_rank = false;
					if($player_to_change->getRank()->getLevel() < $level_in_guild || $guild_leader)
						$player_has_lower_rank = true;
				}
			}
			$rank_in_guild = false;
			foreach($rank_list as $rank_from_guild)
				if($rank_from_guild->getId() == $rank->getId())
					$rank_in_guild = true;
			if(!$player_in_guild)
				$change_errors[] = 'This player isn\'t in your guild.';
			if(!$rank_in_guild)
				$change_errors[] = 'This rank isn\'t in your guild.';
			if(!$player_has_lower_rank)
				$change_errors[] = 'This player has higher rank in guild than you. You can\'t change his/her rank.';
		}

		if(empty($change_errors))
		{
			$player_to_change->setRank($rank);
			$twig->display('success.html.twig', array(
				'title' => 'Rank Changed',
				'description' => 'Rank of player <b>'.$player_to_change->getName().'</b> has been changed to <b>'.$rank->getName().'</b>.',
				'custom_buttons' => ''
			));

			unset($players_with_lower_rank);
			unset($ranks);
			$rid = 0;
			$sid= 0;
			foreach($rank_list as $rank)
			{
				if($guild_leader || $rank->getLevel() < $level_in_guild)
				{
					$ranks[$rid]['0'] = $rank->getId();
					$ranks[$rid]['1'] = $rank->getName();
					$rid++;

					if($db->hasColumn('players', 'rank_id'))
						$players_with_rank = $db->query('SELECT `id`, `rank_id` FROM `players` WHERE `rank_id` = ' . $rank->getId() . ' AND `deleted` = 0;');
					else
						$players_with_rank = $db->query('SELECT `players`.`id` as `id`, `' . GUILD_MEMBERS_TABLE . '`.`rank_id` as `rank_id` FROM `players`, `' . GUILD_MEMBERS_TABLE . '` WHERE `' . GUILD_MEMBERS_TABLE . '`.`rank_id` = ' . $rank->getId() . ' AND `players`.`id` = `' . GUILD_MEMBERS_TABLE . '`.`player_id` ORDER BY `name`;');

					$players_with_rank_number = $players_with_rank->rowCount();
					if(count($players_with_rank) > 0)
					{
						foreach($players_with_rank as $result)
						{
							$player = new OTS_Player();
							$player->load($result['id']);
							if(!$player->isLoaded())
								continue;

							if($guild->getOwner()->getId() != $player->getId() || $guild_leader)
							{
								$players_with_lower_rank[$sid][0] = $player->getName();
								$players_with_lower_rank[$sid][1] = $player->getName().' ('.$rank->getName().')';
								$sid++;
							}
						}
					}
				}
			}
		}
		else
		{
			$twig->display('error_box.html.twig', array('errors' => $change_errors));
		}
	}
	$twig->display('guilds.change_rank.html.twig', array(
		'players' => $players_with_lower_rank,
		'guild_name' => $guild->getName(),
		'ranks' => $ranks
	));
}
else {
	echo 'Error. You are not a leader or vice leader in guild ' . $guild->getName();
	$twig->display('guilds.back_button.html.twig', array(
		'new_line' => true,
		'action' => getLink('guilds') . '/' . $guild->getName()
	));
}