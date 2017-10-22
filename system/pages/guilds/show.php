<?php
/**
 * Show guild
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.6
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

$guild_name = $_REQUEST['guild'];
if(!Validator::guildName($guild_name))
	$errors[] = Validator::getLastError();

if(empty($errors))
{
	$guild = $ots->createObject('Guild');
	$guild->find($guild_name);
	if(!$guild->isLoaded())
		$errors[] = 'Guild with name <b>'.$guild_name.'</b> doesn\'t exist.';
}
if(!empty($errors))
{
	echo $twig->render('error_box.html.twig', array('errors' => $errors));
	
	echo $twig->render('guilds.back_button.html.twig');
}
else
{
	$title = $guild->getName() . ' - ' . $title;
	//check is it vice or/and leader account (leader has vice + leader rights)
	$guild_leader_char = $guild->getOwner();
	$rank_list = $guild->getGuildRanksList();
	$rank_list->orderBy('level', POT::ORDER_DESC);
	$guild_leader = false;
	$guild_vice = false;
	$level_in_guild = 0;
	$players_from_account_in_guild = array();
	if($logged)
	{
		$account_players = $account_logged->getPlayers();
		foreach($account_players as $player)
		{
			$players_from_account_ids[] = $player->getId();
			$player_rank = $player->getRank();
			if($player_rank->isLoaded())
			{
				foreach($rank_list as $rank_in_guild)
				{
					if($rank_in_guild->isLoaded() && $player_rank->isLoaded() &&
						$rank_in_guild->getId() == $player_rank->getId())
					{
						$players_from_account_in_guild[] = $player->getName();
						if($guild->getOwner()->getId() == $player->getId())
						{
							$guild_vice = true;
							$guild_leader = true;
						}
						else if($player_rank->getLevel() > 1)
						{
							$guild_vice = true;
							$level_in_guild = $player_rank->getLevel();
						}
					}
				}
			}
		}
	}
	//show guild page
	$guild_logo = $guild->getCustomField('logo_name');
	if(empty($guild_logo) || !file_exists('images/guilds/' . $guild_logo))
		$guild_logo = "default.gif";
	$description = $guild->getCustomField('description');
	$description_with_lines = str_replace(array("\r\n", "\n", "\r"), '<br />', $description, $count);
	if($count < $config['guild_description_lines_limit'])
		$description = wordwrap(nl2br($description), 60, "<br />", true);
	//$description = $description_with_lines;
	$guild_owner = $guild->getOwner();
	if($guild_owner->isLoaded())
		$guild_owner = $guild_owner->getName();
	echo '<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=100%><TR>
		<TD><IMG SRC="'.$template_path.'/images/general/blank.gif" WIDTH=10 HEIGHT=1 BORDER=0></TD><TD>
		<TABLE BORDER=0 WIDTH=100%>
		<TR><TD WIDTH=64><IMG SRC="images/guilds/' . $guild_logo.'" WIDTH=64 HEIGHT=64></TD>
		<TD ALIGN=center WIDTH=100%><H1>'.$guild->getName().'</H1></TD>
		<TD WIDTH=64><IMG SRC="images/guilds/' . $guild_logo.'" WIDTH=64 HEIGHT=64></TD></TR>
		</TABLE><BR>'.$description.'<BR><BR><a href="' . getPlayerLink($guild_owner, false).'"><b>'.$guild_owner.'</b></a> is guild leader of <b>'.$guild->getName().'</b>.<BR>The guild was founded on '.$config['lua']['serverName'].' on '.date("j F Y", $guild->getCreationData()).'.';
	if($guild_leader)
		echo '&nbsp;&nbsp;&nbsp;<a href="?subtopic=guilds&action=manager&guild='.$guild->getName().'"><IMG SRC="'.$template_path.'/images/global/buttons/sbutton_manageguild.png" BORDER=0 WIDTH=120 HEIGHT=18 alt="Manage Guild"></a>';
	echo '<BR><BR>

				<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%>
					<TR BGCOLOR='.$config['vdarkborder'].'>
						<TD COLSPAN=3 class="white"><B>Guild Members</B></TD>
					</TR>
					<TR BGCOLOR='.$config['darkborder'].'>
						<TD WIDTH=30%><B>Rank</B></TD>
						<TD WIDTH=30%><B>Name, title, level & status</B></TD>
					</TR>';
	
	//Slaw stats values
	//$s_total_members = 0;
	//$s_members_online = 0;
	//$s_total_level = 0;
	//End Slaw stats values
	
	$showed_players = 1;
	foreach($rank_list as $rank)
	{
		if(tableExist(GUILD_MEMBERS_TABLE))
			$players_with_rank = $db->query('SELECT `players`.`id` as `id`, `' . GUILD_MEMBERS_TABLE . '`.`rank_id` as `rank_id` FROM `players`, `' . GUILD_MEMBERS_TABLE . '` WHERE `' . GUILD_MEMBERS_TABLE . '`.`rank_id` = ' . $rank->getId() . ' AND `players`.`id` = `' . GUILD_MEMBERS_TABLE . '`.`player_id` ORDER BY `name`;');
		else if(fieldExist('rank_id', 'players'))
			$players_with_rank = $db->query('SELECT `id`, `rank_id` FROM `players` WHERE `rank_id` = ' . $rank->getId() . ' AND `deleted` = 0;');
		
		$players_with_rank_number = $players_with_rank->rowCount();
		if($players_with_rank_number > 0)
		{
			$bgcolor = getStyle($showed_players);
			$showed_players++;
			echo '
					<TR BGCOLOR="'.$bgcolor.'">
						<TD valign="top">'.$rank->getName().'</TD>
						<TD>
							<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%>';
			foreach($players_with_rank as $result)
			{
				$player = $ots->createObject('Player');
				$player->load($result['id']);
				if(!$player->isLoaded())
					continue;
				
				//$s_total_members++;
				//$s_total_level += $player->getLevel();
				echo '<TR><TD>' . getPlayerLink($player->getName()) . '<FORM ACTION="?subtopic=guilds&action=change_nick&name='.$player->getName().'" METHOD=post>';
				$guild_nick = $player->getGuildNick();
				if($logged)
				{
					if(in_array($player->getId(), $players_from_account_ids))
						echo ' (<input type="text" name="nick" value="'.htmlentities($player->getGuildNick()).'"><input type="submit" value="Change">)';
					else
					{
						if(!empty($guild_nick))
							echo ' ('.htmlentities($player->getGuildNick()).')';
					}
				}
				else
					if(!empty($guild_nick))
						echo ' ('.htmlentities($player->getGuildNick()).')';
				
				if($level_in_guild > $rank->getLevel() || $guild_leader)
					if($guild_leader_char->getName() != $player->getName())
						echo '&nbsp;<font size=1>{<a href="?subtopic=guilds&action=kick_player&guild='.urlencode($guild->getName()).'&name='.urlencode($player->getName()).'">KICK</a>}</font>';
				
				echo '</FORM></TD><TD align="right" width="10%">'.$player->getLevel().'</TD><TD align="right" width="20%"><font color="'.($player->isOnline() ? 'green"><b>Online' : 'red"><b>Offline').'</b></font></TD></TR>';
			}
			echo '</TABLE></TD></TR>';
		}
	}
	echo '</TABLE>';
	/*
	//Statistics ;)
	echo '<BR>
	<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%>
	<TR BGCOLOR='.$config['vdarkborder'].'><TD COLSPAN=2 class="white"><B>Statistics</B></TD></TR>
	<TR BGCOLOR='.$config['darkborder'].'>
		<TD WIDTH=30%>Total members:</TD>
		<TD WIDTH=50%><B>'.$s_total_members.'</B></TD>
	</TR>
	<TR BGCOLOR='.$config['lightborder'].'>
		<TD WIDTH=30%>Members currently online:</TD>
		<TD WIDTH=50%><B>'.$s_members_online.'</B></TD>
	</TR>
	<TR BGCOLOR='.$config['darkborder'].'>
		<TD WIDTH=30%>Total members level:</TD>
		<TD WIDTH=50%><B>'.$s_total_level.'</B></TD>
	</TR>
	<TR BGCOLOR='.$config['lightborder'].'>
		<TD WIDTH=30%>Average members level:</TD>
		<TD WIDTH=50%><B>'.ceil($s_total_level/$s_total_members).'</B></TD>
	</TR>
	<TR BGCOLOR='.$config['darkborder'].'>
		<TD WIDTH=30%>Frags:</TD>
		<TD WIDTH=50%><B>'.$guild->getCustomField('frags').'</B></TD>
	</TR>';
	//guild hall?
	$houseInfo = $db->query('SELECT `id`, `name` FROM `houses` WHERE `owner` = ' . $guild->getId() . ' AND `guild` = 1');
	if($houseInfo->rowCount() > 0) //have guild hall
	{
		$houseInfo = $houseInfo->fetch();
		echo
		'<TR BGCOLOR='.$config['lightborder'].'>
			<TD WIDTH=30%>Guildhall:</TD>
			<TD WIDTH=50%>
				<B>'.$houseInfo['name'].'</B>
					<FORM ACTION=?subtopic=houses&page=view METHOD=post>
						<INPUT TYPE=hidden NAME=houseid VALUE='.$houseInfo['id'].'>
						<INPUT TYPE=image NAME="View" ALT="View" SRC="'.$template_path.'/images/global/buttons/sbutton_view.gif" BORDER=0 WIDTH=120>
					</FORM>
			</TD>
		</TR>';
	}
	echo '</TABLE>';
	*/
	//End statistics
	
	//Lets update some stuff in database
	//$db->query('UPDATE `guilds` SET `total_members` = '.$s_total_members.', `members_online` = '.$s_members_online.', `total_level` = '.$s_total_level.', `average_level` = '.ceil($s_total_level/$s_total_members).' WHERE `id` = '.$guild->getId());
	include(SYSTEM . 'libs/pot/InvitesDriver.php');
	new InvitesDriver($guild);
	$invited_list = $guild->listInvites();
	$show_accept_invite = 0;
	if(count($invited_list) == 0)
		echo '<BR><TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD COLSPAN=2 class="white"><B>Invited Characters</B></TD></TR><TR BGCOLOR='.$config['lightborder'].'><TD>No invited characters found.</TD></TR></TABLE>';
	else
	{
		echo '<BR><BR><TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD COLSPAN=2 class="white"><B>Invited Characters</B></TD></TR>';
		$showed_invited = 1;
		foreach($invited_list as $invited_player)
		{
			if($logged && count($account_players) > 0)
				foreach($account_players as $player_from_acc)
					if($player_from_acc->getName() == $invited_player->getName())
						$show_accept_invite++;
			if(is_int($showed_invited / 2)) { $bgcolor = $config['darkborder']; } else { $bgcolor = $config['lightborder']; } $showed_invited++;
			echo '<TR bgcolor="'.$bgcolor.'"><TD>' . getPlayerLink($invited_player->getName());
			if($guild_vice)
				echo '  (<a href="?subtopic=guilds&action=delete_invite&guild='.$guild->getName().'&name='.$invited_player->getName().'">Cancel Invitation</a>)';
			echo '</TD></TR>';
		}
		echo '</TABLE>';
	}
	echo '<BR><BR>
		<TABLE BORDER=0 WIDTH=100%><TR><TD ALIGN=center><IMG SRC="'.$template_path.'/images/general/blank.gif" WIDTH=80 HEIGHT=1 BORDER=0<BR></TD>';
	if(!$logged)
		echo '<TD ALIGN=center><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="?subtopic=accountmanagement&redirect='.getGuildLink($guild->getName(), false).'" METHOD=post><TR><TD>
			<INPUT TYPE=image NAME="Login" ALT="Login" SRC="'.$template_path.'/images/global/buttons/sbutton_login.gif" BORDER=0 WIDTH=120 HEIGHT=18>
			</TD></TR></FORM></TABLE></TD>';
	else
	{
		if($show_accept_invite > 0)
			echo '<TD ALIGN=center><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="?subtopic=guilds&action=accept_invite&guild='.$guild->getName().'" METHOD=post><TR><TD>
				<INPUT TYPE=image NAME="Accept Invite" ALT="Accept Invite" SRC="'.$template_path.'/images/global/buttons/sbutton_acceptinvite.png" BORDER=0 WIDTH=120 HEIGHT=18>
				</TD></TR></FORM></TABLE></TD>';
		if($guild_vice)
		{
			echo '<TD ALIGN=center><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="?subtopic=guilds&action=invite&guild='.$guild->getName().'" METHOD=post><TR><TD>
				<INPUT TYPE=image NAME="Invite Player" ALT="Invite Player" SRC="'.$template_path.'/images/global/buttons/sbutton_inviteplayer.png" BORDER=0 WIDTH=120 HEIGHT=18>
				</TD></TR></FORM></TABLE></TD>';
			echo '<TD ALIGN=center><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="?subtopic=guilds&action=change_rank&guild='.$guild->getName().'" METHOD=post><TR><TD>
				<INPUT TYPE=image NAME="Change Rank" ALT="Change Rank" SRC="'.$template_path.'/images/global/buttons/sbutton_changerank.png" BORDER=0 WIDTH=120 HEIGHT=18>
				</TD></TR></FORM></TABLE></TD>';
		}
		if(count($players_from_account_in_guild) > 0)
			echo '<TD ALIGN=center><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="?subtopic=guilds&action=leave_guild&guild='.$guild->getName().'" METHOD=post><TR><TD>
				<INPUT TYPE=image NAME="Leave Guild" ALT="Leave Guild" SRC="'.$template_path.'/images/global/buttons/sbutton_leaveguild.png" BORDER=0 WIDTH=120 HEIGHT=18>
				</TD></TR></FORM></TABLE></TD>';
	}
	echo '<TD ALIGN=center><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="?subtopic=guilds" METHOD=post><TR><TD>
		' . $twig->render('buttons.back.html.twig') . '
		</TD></TR></FORM></TABLE>
		</TD><TD ALIGN=center><IMG SRC="'.$template_path.'/images/general/blank.gif" WIDTH=80 HEIGHT=1 BORDER=0<BR></TD></TR></TABLE>
		</TD><TD><IMG src="'.$template_path.'/images/general/blank.gif" WIDTH=10 HEIGHT=1 BORDER=0></TD>
		</TR></TABLE></TABLE>';
}