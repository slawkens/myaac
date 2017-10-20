<?php
/**
 * Add rank
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
$ranknew = $_REQUEST['rank_name'];
if(!Validator::guildName($guild_name)) {
	$guild_errors[] = Validator::getLastError();
}
if(empty($guild_errors)) {
	if(!Validator::rankName($ranknew)) {
		$guild_errors[] = 'Invalid rank name format.';
	}
	if(!$logged) {
		$guild_errors[] = 'You are not logged.';
	}
	$guild = $ots->createObject('Guild');
	$guild->find($guild_name);
	if(!$guild->isLoaded()) {
		$guild_errors[] = 'Guild with name <b>'.$guild_name.'</b> doesn\'t exist.';
	}
	if(empty($guild_errors)) {
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
			$new_rank = new OTS_GuildRank();
			$new_rank->setGuild($guild);
			$new_rank->setLevel(1);
			$new_rank->setName($ranknew);
			$new_rank->save();
			header("Location: ?subtopic=guilds&guild=".$guild->getName()."&action=manager");
			echo 'New rank added. Redirecting...';
		}
		else  {
			$guild_errors[] = 'You are not a leader of guild!';
		}
	}
	if(!empty($guild_errors)) {
		echo $twig->render('error_box.html.twig', array('errors' => $guild_errors));
		
		echo $twig->render('guilds.back_button.html.twig', array(
			'new_line' => true,
			'action' => '?subtopic=guilds&guild='.$guild_name.'&action=show'
		));
	}
}
else
{
	if(!empty($guild_errors)) {
		echo $twig->render('error_box.html.twig', array('errors' => $guild_errors));
		
		echo $twig->render('guilds.back_button.html.twig', array(
			'new_line' => true
		));
	}
}

?>