<?php
/**
 * Kick player from guild
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.1
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

//set rights in guild
$guild_name = $_REQUEST['guild'];
$name = stripslashes($_REQUEST['name']);
if(!$logged) {
	$errors[] = 'You are not logged in. You can\'t kick characters.';
}

if(!Validator::guildName($guild_name)) {
	$errors[] = Validator::getLastError();
}

if(!Validator::characterName($name)) {
	$errors[] = 'Invalid name format.';
}

if(empty($errors)) {
	$guild = $ots->createObject('Guild');
	$guild->find($guild_name);
	if(!$guild->isLoaded()) {
		$errors[] = 'Guild with name <b>'.$guild_name.'</b> doesn\'t exist.';
	}
}

if(empty($errors)) {
	$rank_list = $guild->getGuildRanksList();
	$rank_list->orderBy('level', POT::ORDER_DESC);
	$guild_leader = false;
	$guild_vice = false;
	$account_players = $account_logged->getPlayers();
	foreach($account_players as $player) {
		$player_rank = $player->getRank();
		if($player_rank->isLoaded()) {
			foreach($rank_list as $rank_in_guild) {
				if($rank_in_guild->getId() == $player_rank->getId()) {
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
}

if(empty($errors)) {
	if(!$guild_leader && $level_in_guild < 3) {
		$errors[] = 'You are not a leader of guild <b>'.$guild_name.'</b>. You can\'t kick players.';
	}
}

if(empty($errors)) {
	$player = new OTS_Player();
	$player->find($name);
	if(!$player->isLoaded()) {
		$errors[] = 'Character <b>'.$name.'</b> doesn\'t exist.';
	}
	else
	{
		if($player->getRank()->isLoaded() && $player->getRank()->getGuild()->isLoaded() && $player->getRank()->getGuild()->getName() != $guild->getName()) {
			$errors[] = 'Character <b>'.$name.'</b> isn\'t from your guild.';
		}
	}
}

if(empty($errors)) {
	if($player->getRank()->isLoaded() && $player->getRank()->getLevel() >= $level_in_guild && !$guild_leader) {
		$errors[] = 'You can\'t kick character <b>'.$name.'</b>. Too high access level.';
	}
}

if(empty($errors)) {
	if($guild->getOwner()->getName() == $player->getName()) {
		$errors[] = 'It\'s not possible to kick guild owner!';
	}
}

if(!empty($errors)) {
	echo $twig->render('error_box.html.twig', array('errors' => $errors));
	echo '
<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TR><TD><center><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></center></TD></TR></FORM></TABLE>';
}
else
{
	if(isset($_REQUEST['todo']) && $_REQUEST['todo'] == 'save') {
		$player->setRank();
		echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD class="white"><B>Kick player</B></TD></TR><TR BGCOLOR='.$config['darkborder'].'><TD WIDTH=100%>Player with name <b>'.$player->getName().'</b> has been kicked from your guild.</TD></TR></TABLE><br/><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TR><TD><center><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></center></TD></TR></FORM></TABLE>';
	}
	else
	{
		echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD class="white"><B>Kick player</B></TD></TR><TR BGCOLOR='.$config['darkborder'].'><TD WIDTH=100%>Are you sure you want to kick player with name <b>'.$player->getName().'</b> from your guild?</TD></TR></TABLE><br/><center><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><TR><FORM ACTION="?subtopic=guilds&action=kick_player&guild='.$guild->getName().'&name='.$player->getName().'&todo=save" METHOD=post><TD align="right" width="50%"><INPUT TYPE=image NAME="Submit" ALT="Submit" SRC="'.$template_path.'/images/buttons/sbutton_submit.gif" BORDER=0 WIDTH=120 HEIGHT=18>&nbsp;&nbsp;</TD></FORM><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TD>&nbsp;&nbsp;<INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></TD></TR></FORM></TABLE></center>';
	}
}

?>