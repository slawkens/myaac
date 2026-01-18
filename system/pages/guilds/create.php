<?php
/**
 * Create guild
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

use MyAAC\Models\GuildRank;

require __DIR__ . '/base.php';

$guild_name = isset($_POST['guild']) ? urldecode($_POST['guild']) : NULL;
$name = isset($_POST['name']) ? stripslashes($_POST['name']) : NULL;
$todo = isset($_POST['todo']) ? $_POST['todo'] : NULL;
if(!$logged) {
	$errors[] = 'You are not logged in. You can\'t create guild.';
}

$configLuaFreePremium = configLua('freePremium');
$freePremium = (isset($configLuaFreePremium) && getBoolean($configLuaFreePremium));

$array_of_player_nig = array();
if(empty($errors))
{
	$account_players = $account_logged->getPlayersList(false);
	foreach($account_players as $player)
	{
		$player_rank = $player->getRank();
		if(!$player_rank->isLoaded())
		{
			if($player->getLevel() >= setting('core.guild_need_level')) {
				if(!setting('core.guild_need_premium') || $account_logged->isPremium() || $freePremium) {
					$array_of_player_nig[] = $player->getName();
				}
			}
		}
	}
}

if(empty($todo)) {
	if(count($array_of_player_nig) == 0) {
		$errors[] = 'On your account all characters are in guilds, have too low level to create new guild' . (setting('core.guild_need_premium') ? ' or you don\' have a premium account' : '') . '.';
	}
}

if($todo == 'save')
{
	if(!Validator::guildName($guild_name)) {
		$errors[] = Validator::getLastError();
		$guild_name = '';
	}

	if(!Validator::characterName($name)) {
		$errors[] = 'Invalid character name format.';
		$name = '';
	}

	if(empty($errors)) {
		$player = new OTS_Player();
		$player->find($name);
		if(!$player->isLoaded()) {
			$errors[] = 'Character <b>'.$name.'</b> doesn\'t exist.';
		}
	}

	if(empty($errors))
	{
		$guild = new OTS_Guild();
		$guild->find($guild_name);
		if($guild->isLoaded()) {
			$errors[] = 'Guild <b>'.$guild_name.'</b> already exist. Select other name.';
		}
	}

	if(empty($errors) && $player->isDeleted()) {
		$errors[] = "Character <b>$name</b> has been deleted.";
	}

	if(empty($errors))
	{
		$bad_char = true;
		foreach($array_of_player_nig as $nick_from_list) {
			if($nick_from_list == $player->getName()) {
				$bad_char = false;
			}
		}
		if($bad_char) {
			$errors[] = 'Character <b>'.$name.'</b> isn\'t on your account or is already in guild.';
		}
	}

	if(empty($errors)) {
		if($player->getLevel() < setting('core.guild_need_level')) {
			$errors[] = 'Character <b>'.$name.'</b> has too low level. To create guild you need character with level <b>' . setting('core.guild_need_level') . '</b>.';
		}
		if(setting('core.guild_need_premium') && !$account_logged->isPremium() && !$freePremium) {
			$errors[] = 'Character <b>'.$name.'</b> is on FREE account. To create guild you need PREMIUM account.';
		}
	}
}

if(!empty($errors)) {
	$twig->display('error_box.html.twig', array('errors' => $errors));
	unset($todo);
}

if(isset($todo) && $todo == 'save')
{
	$new_guild = new OTS_Guild();
	$new_guild->setCreationData(time());
	$new_guild->setName($guild_name);
	$new_guild->setOwner($player);
	$new_guild->save();
	$new_guild->setCustomField('description', setting('core.guild_description_default'));

	if ($db->hasTable('guild_ranks')) {
		if (!GuildRank::where('guild_id', $new_guild->getId())->first()) {
			$ranks = [
				['level' => 3, 'name' => 'the Leader'],
				['level' => 2, 'name' => 'a Vice-Leader'],
				['level' => 1, 'name' => 'a Member'],
			];

			foreach ($ranks as $rank) {
				GuildRank::create([
					'guild_id' => $new_guild->getId(),
					'name' => $rank['name'],
					'level' => $rank['level'],
				]);
			}
		}
	}

	$ranks = $new_guild->getGuildRanksList();
	$ranks->orderBy('level', POT::ORDER_DESC);
	foreach($ranks as $rank) {
		/**
		 * @var OTS_GuildRank $rank
		 */
		if($rank->getLevel() == 3) {
			$player->setRank($rank);
		}
	}

	$twig->display('guilds.create.success.html.twig', array(
		'guild_name' => $guild_name,
		'leader_name' => $player->getName()
	));
}
else {
	sort($array_of_player_nig);
	$twig->display('guilds.create.html.twig', array(
		'players' => $array_of_player_nig
	));
}
