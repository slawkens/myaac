<?php
/**
 * Delete invite
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
if(!$logged)
	$guild_errors[] = 'You are not logged in. You can\'t delete invitations.';
if(!Validator::guildName($guild_name))
	$guild_errors[] = Validator::getLastError();
if(!Validator($name))
	$guild_errors[] = 'Invalid name format.';
if(empty($guild_errors))
{
	$guild = $ots->createObject('Guild');
	$guild->find($guild_name);
	if(!$guild->isLoaded())
		$guild_errors[] = 'Guild with name <b>'.$guild_name.'</b> doesn\'t exist.';
}
if(empty($guild_errors))
{
	$rank_list = $guild->getGuildRanksList();
	$rank_list->orderBy('level', POT::ORDER_DESC);
	$guild_leader = false;
	$guild_vice = false;
	$account_players = $account_logged->getPlayers();
	foreach($account_players as $player)
	{
		$player_rank = $player->getRank();
		if($player_rank->isLoaded())
		{
			foreach($rank_list as $rank_in_guild)
			{
				if($rank_in_guild->getId() == $player_rank->getId())
				{
					$players_from_account_in_guild[] = $player->getName();
					if($player_rank->getLevel() > 1)
					{
						$guild_vice = true;
						$level_in_guild = $player_rank->getLevel();
					}
					if($guild->getOwner()->getId() == $player->getId())
					{
						$guild_vice = true;
						$guild_leader = true;
					}
				}
			}
		}
	}
}
if(empty($guild_errors))
{
	$player = new OTS_Player();
	$player->find($name);
	if(!$player->isLoaded())
		$guild_errors[] = 'Player with name <b>'.$name.'</b> doesn\'t exist.';
}
if(!$guild_vice)
	$guild_errors[] = 'You are not a leader or vice leader of guild <b>'.$guild_name.'</b>.';
if(empty($guild_errors))
{
	include(SYSTEM . 'libs/pot/InvitesDriver.php');
	new InvitesDriver($guild);
	$invited_list = $guild->listInvites();
	if(count($invited_list) > 0)
	{
		$is_invited = false;
		foreach($invited_list as $invited)
			if($invited->getName() == $player->getName())
				$is_invited = true;
		if(!$is_invited)
			$guild_errors[] = '<b>'.$player->getName().'</b> isn\'t invited to your guild.';
	}
	else
		$guild_errors[] = 'No one is invited to your guild.';
}
if(!empty($guild_errors))
{
	echo $twig->render('error_box.html.twig', array('errors' => $guild_errors));
	echo '
<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TR><TD><center><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></center></TD></TR></FORM></TABLE>';
}
else
{
	if(isset($_REQUEST['todo']) && $_REQUEST['todo'] == 'save')
	{
		$guild->deleteInvite($player);
		echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD class="white"><B>Delete player invitation</B></TD></TR><TR BGCOLOR='.$config['darkborder'].'><TD WIDTH=100%>Player with name <b>'.$player->getName().'</b> has been deleted from "invites list".</TD></TR></TABLE><br/><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TR><TD><center><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></center></TD></TR></FORM></TABLE>';
	}
	else
		echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD class="white"><B>Delete player invitation</B></TD></TR><TR BGCOLOR='.$config['darkborder'].'><TD WIDTH=100%>Are you sure you want to delete player with name <b>'.$player->getName().'</b> from "invites list"?</TD></TR></TABLE><br/><center><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><TR><FORM ACTION="?subtopic=guilds&action=delete_invite&guild='.$guild->getName().'&name='.$player->getName().'&todo=save" METHOD=post><TD align="right" width="50%"><INPUT TYPE=image NAME="Submit" ALT="Submit" SRC="'.$template_path.'/images/buttons/sbutton_submit.gif" BORDER=0 WIDTH=120 HEIGHT=18>&nbsp;&nbsp;</TD></FORM><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TD>&nbsp;&nbsp;<INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></TD></TR></FORM></TABLE></center>';
}