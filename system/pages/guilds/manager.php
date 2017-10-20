<?php
/**
 * Guild manager
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.2
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

$guild_name = $_REQUEST['guild'];
if(!Validator::guildName($guild_name)) {
	$guild_errors[] = Validator::getLastError();
}

if(empty($guild_errors)) {
	$guild = new OTS_Guild();
	$guild->find($guild_name);
	if(!$guild->isLoaded()) {
		$guild_errors[] = 'Guild with name <b>'.$guild_name.'</b> doesn\'t exist.';
	}
}

if(empty($guild_errors)) {
	if($logged) {
		$guild_leader_char = $guild->getOwner();
		$rank_list = $guild->getGuildRanksList();
		$rank_list->orderBy('level', POT::ORDER_DESC);
		$guild_leader = false;
		$account_players = $account_logged->getPlayers();
		foreach($account_players as $player) {
			if($guild_leader_char->getId() == $player->getId()) {
				$guild_vice = true;
				$guild_leader = true;
				$level_in_guild = 3;
			}
		}
		if($guild_leader) {
			echo $twig->render('guilds.manager.html.twig', array(
				'guild' => $guild,
				'rank_list' => $rank_list
			));
		}
		else
		{
			$guild_errors[] = 'You are not a leader of guild!';
		}
	}
	else
	{
		$guild_errors[] = 'You are not logged. You can\'t manage guild.';
	}
}
if(!empty($guild_errors)) {
	echo $twig->render('error_box.html.twig', array('errors' => $guild_errors));
}

?>