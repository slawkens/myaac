<?php
/**
 * Guilds
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.2.2
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Guilds';

if(tableExist('guild_members'))
	define('GUILD_MEMBERS_TABLE', 'guild_members');
else
	define('GUILD_MEMBERS_TABLE', 'guild_membership');
	
if($action == 'login')
{
	if(check_guild_name($_REQUEST['guild']))
		$guild = $_REQUEST['guild'];
	if($_REQUEST['redirect'] == 'guild' || $_REQUEST['redirect'] == 'guilds')
		$redirect = $_REQUEST['redirect'];
	if(!$logged)
		echo 'Please enter your account number and your password.<br/><a href="?subtopic=createaccount" >Create an account</a> if you do not have one yet.<br/><br/><form action="?subtopic=guilds&action=login&guild='.$guild.'&redirect='.$redirect.'" method="post" ><div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Account Login</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td class="LabelV" ><span >Account Number:</span></td><td style="width:100%;" ><input type="password" name="account_login" SIZE="10" maxlength="10" ></td></tr><tr><td class="LabelV" ><span >Password:</span></td><td><input type="password" name="password_login" size="30" maxlength="29" ></td></tr>          </table>        </div>  </table></div></td></tr><br/><table width="100%" ><tr align="center" ><td><table border="0" cellspacing="0" cellpadding="0" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Submit" alt="Submit" src="'.$template_path.'/images/buttons/_sbutton_submit.gif" ></div></div></td><tr></form></table></td><td><table border="0" cellspacing="0" cellpadding="0" ><form action="?subtopic=lostaccount" method="post" ><tr><td style="border:0px;" ><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Account lost?" alt="Account lost?" src="'.$template_path.'/images/buttons/_sbutton_accountlost.gif" ></div></div></td></tr></form></table></td></tr></table>';
	else
	{
		echo '<center><h3>Now you are logged. Redirecting...</h3></center>';
		if($redirect == 'guilds')
			header("Location: ?subtopic=guilds");
		elseif($redirect == 'guild')
			header("Location: ?subtopic=guilds&action=show&guild=".$guild);
		else
			echo 'Wrong address to redirect!';
	}
}
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//show list of guilds
if($action == '')
{
	$guilds_list = $ots->createObject('Guilds_List');

	if(!isset($_REQUEST['preview']))
		$_REQUEST['preview'] = 1;

	$guilds_list->orderBy("name");

	//echo 'Guilds needs to have atleast 4 members, otherwise it will be deleted automatically after 4 days.<BR/><BR/>Guild statistics are self-updated once per 3 days.<BR/><BR/>';

	//echo '<A HREF="?subtopic=guilds&preview=1">Normal preview</A> / <A HREF="?subtopic=guilds&preview=2">Advanced ranks & statistics</A><BR/><BR/>
	echo '
	<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%>
	<TR BGCOLOR='.$config['vdarkborder'].'><TD COLSPAN='.($_REQUEST['preview'] == 2 ? '7' : '3').' class="white"><B>Active Guilds on '.$config['lua']['serverName'].'</B></TD></TR>
	<TR BGCOLOR='.$config['darkborder'].' '.($_REQUEST['preview'] == 2 ? 'ALIGN="CENTER"' : '' ).'>';
		if($_REQUEST['preview'] == 2) {
			echo '
		<TD WIDTH=50><B><A HREF="?subtopic=guilds&preview=2&order=rank">Rank</A></B></TD>
		<TD WIDTH=32><B>Logo</B></TD>
		<TD WIDTH=200><B><A HREF="?subtopic=guilds&preview=2&order=name">Guild name</A></B></TD>';
		/*
		<TD WIDTH=10><B><A HREF="?subtopic=guilds&preview=2&order=total_members">Members</A></B></TD>
		<TD WIDTH=10><B><A HREF="?subtopic=guilds&preview=2&order=total_level">Total level</A></B></TD>
		<TD WIDTH=10><B><A HREF="?subtopic=guilds&preview=2&order=average_level">Average level</A></B></TD>
		<TD WIDTH=10><B><A HREF="?subtopic=guilds&preview=2&order=frags">Frags</A></B></TD>';*/
		}
		else
			echo '
		<TD WIDTH=64><B>Logo</B></TD>
		<TD WIDTH=100%><B>Description</B></TD>
		<TD WIDTH=50><B> </B></TD>';

	echo '
	</TR>';
	$showed_guilds = 0;

		if($_REQUEST['preview'] == 2)
		{
			if(count($guilds_list) > 0)
			{
				foreach($guilds_list as $guild)
				{
				$guild_logo = $guild->getCustomField('logo_name');
				if(empty($guild_logo) || !file_exists('images/guilds/' . $guild_logo))
					$guild_logo = "default.gif";

				echo '<TR class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'?subtopic=guilds&action=show&guild='.$guild->getName().'\'"';

				echo ' BGCOLOR="' . getStyle($showed_guilds++) . '"><TD ALIGN="CENTER">'.($show_ranks ? $showed_guilds.'.' : '-').'</TD><TD ALIGN="CENTER"><A HREF="?subtopic=guilds&action=show&guild='.$guild->getName().'"><IMG STYLE="border: none" SRC="images/guilds/' .$guild_logo.'" WIDTH=32 HEIGHT=32></A></TD>
				<TD valign="top"><B>'.$guild->getName().'</B><BR/>';
				if(admin())
					echo '<br /><a href="?subtopic=guilds&action=deletebyadmin&guild='.$guild->getName().'">Delete this guild (for ADMIN only!)</a>';
				echo '</TD>';
				//<TD ALIGN="CENTER">'.$guild->getCustomField('total_members').'</TD><TD ALIGN="CENTER">'.$guild->getCustomField('total_level').'</TD><TD ALIGN="CENTER">';
				//if($guild->getCustomField('total_members') > 0)
				//	echo ceil($guild->getCustomField('total_level')/$guild->getCustomField('total_members'));
				//echo '</TD><TD>'.$guild->getCustomField('frags').'</TD>

				echo '</TR>';
				}
			}
			else
				echo '<TR BGCOLOR='.$config['lightborder'].'><TD ALIGN="CENTER">-</TD><TD><IMG SRC="images/guilds/' . 'default.gif" WIDTH=64 HEIGHT=64></TD>
					<TD valign="top" align="center"><B>Create guild</B><BR/>Actually there is no guild on server.' . ($logged ? ' Create first! Press button "Create Guild".' : '') . '</TD>
					<TD colspan="4">';
					if($logged)
						echo '
					<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="?subtopic=guilds&action=createguild" METHOD=post><TR><TD>
					<INPUT TYPE=image NAME="Create Guild" ALT="Create Guild" SRC="'.$template_path.'/images/buttons/sbutton_createguild.png" BORDER=0 WIDTH=120 HEIGHT=18>
					</TD></TR></FORM></TABLE>';
					
					echo '
					</TD></TR>';
		}
		else
		{
			if(count($guilds_list) > 0)
			{
				foreach($guilds_list as $guild)
				{
					$guild_logo = $guild->getCustomField('logo_name');
					if(empty($guild_logo) || !file_exists('images/guilds/' . $guild_logo))
						$guild_logo = "default.gif";
					$description = $guild->getCustomField('description');
					$description_with_lines = str_replace(array("\r\n", "\n", "\r"), '<br />', $description, $count);
					if($count < $config['guild_description_lines_limit'])
						$description = wordwrap(nl2br($description), 60, "<br />", true);
						//$description = $description_with_lines;

					echo '<TR BGCOLOR="' . getStyle($showed_guilds++) . '"><TD><IMG SRC="images/guilds/' . $guild_logo.'" WIDTH=64 HEIGHT=64></TD>
					<TD valign="top"><B>'.$guild->getName().'</B><BR/>'.$description.'';
					if(admin())
						echo '<br /><a href="?subtopic=guilds&action=deletebyadmin&guild='.$guild->getName().'">Delete this guild (for ADMIN only!)</a>';
					echo '</TD><TD><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild->getName().'" METHOD=post><TR><TD>
					<INPUT TYPE=image NAME="View" ALT="View" SRC="'.$template_path.'/images/buttons/sbutton_view.gif" BORDER=0 WIDTH=120 HEIGHT=18>
					</TD></TR></FORM></TABLE>
					</TD></TR>';
				}
			}
			else
				echo '<TR BGCOLOR='.$config['lightborder'].'><TD><IMG SRC="images/guilds/' . 'default.gif" WIDTH=64 HEIGHT=64></TD>
					<TD valign="top"><B>Create guild</B><BR/>Actually there is no guild on server.' . ($logged ? ' Create first! Press button "Create Guild".' : '') . '</TD>
					<TD>';
					if($logged)
						echo '<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="?subtopic=guilds&action=createguild" METHOD=post><TR><TD>
					<INPUT TYPE=image NAME="Create Guild" ALT="Create Guild" SRC="'.$template_path.'/images/buttons/sbutton_createguild.png" BORDER=0 WIDTH=120 HEIGHT=18>
					</TD></TR></FORM></TABLE>';
					echo '
					</TD></TR>';
		}


	echo '</TABLE><br><br>';
	if($logged)
		echo '<TABLE BORDER=0 WIDTH=100%><TR><TD ALIGN=center><IMG SRC="'.$template_path.'/images/general/blank.gif" WIDTH=80 HEIGHT=1 BORDER=0<BR></TD><TD ALIGN=center><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="?subtopic=guilds&action=createguild" METHOD=post><TR><TD>
		<INPUT TYPE=image NAME="Create Guild" ALT="Create Guild" SRC="'.$template_path.'/images/buttons/sbutton_createguild.png" BORDER=0 WIDTH=120 HEIGHT=18>
		</TD></TR></FORM></TABLE></TD><TD ALIGN=center><IMG SRC="'.$template_path.'/images/general/blank.gif" WIDTH=80 HEIGHT=1 BORDER=0<BR></TD></TR></TABLE>
		<BR />If you have any problem with guilds try:
		<BR /><a href="?subtopic=guilds&action=cleanup_players">Cleanup players</a> - can\'t join guild/be invited? Can\'t create guild? Try cleanup players.
		<BR /><a href="?subtopic=guilds&action=cleanup_guilds">Cleanup guilds</a> - made guild, you are a leader, but you are not on players list? Cleanup guilds!';
	else
		echo 'Before you can create guild you must login.<br><TABLE BORDER=0 WIDTH=100%><TR><TD ALIGN=center><IMG SRC="'.$template_path.'/images/general/blank.gif" WIDTH=80 HEIGHT=1 BORDER=0<BR></TD><TD ALIGN=center><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="?subtopic=accountmanagement&redirect=' . getPageLink('guilds') . '" METHOD=post><TR><TD>
		<INPUT TYPE=image NAME="Login" ALT="Login" SRC="'.$template_path.'/images/buttons/sbutton_login.gif" BORDER=0 WIDTH=120 HEIGHT=18>
		</TD></TR></FORM></TABLE></TD><TD ALIGN=center><IMG SRC="'.$template_path.'/images/general/blank.gif" WIDTH=80 HEIGHT=1 BORDER=0<BR></TD></TR></TABLE>';
}
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//show guild page
if($action == 'show')
{
	$guild_name = $_REQUEST['guild'];
	if(!check_guild_name($guild_name))
		$guild_errors[] = 'Invalid guild name format.';
	if(empty($guild_errors))
	{
		$guild = $ots->createObject('Guild');
		$guild->find($guild_name);
		if(!$guild->isLoaded())
			$guild_errors[] = 'Guild with name <b>'.$guild_name.'</b> doesn\'t exist.';
	}
	if(!empty($guild_errors))
	{
		//show errors
		echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
		foreach($guild_errors as $guild_error)
			echo '<li>'.$guild_error;
		//errors and back button
		echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br/><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><FORM ACTION="?subtopic=guilds" METHOD=post><TR><TD><center><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></center></TD></TR></FORM></TABLE>';
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
			echo '&nbsp;&nbsp;&nbsp;<a href="?subtopic=guilds&action=manager&guild='.$guild->getName().'"><IMG SRC="'.$template_path.'/images/buttons/sbutton_manageguild.png" BORDER=0 WIDTH=120 HEIGHT=18 alt="Manage Guild"></a>';
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
							echo '&nbsp;<font size=1>{<a href="?subtopic=guilds&action=kickplayer&guild='.urlencode($guild->getName()).'&name='.urlencode($player->getName()).'">KICK</a>}</font>';
					//if($player->isOnline())
					//	$s_members_online++;
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
							<INPUT TYPE=image NAME="View" ALT="View" SRC="'.$template_path.'/images/buttons/sbutton_view.gif" BORDER=0 WIDTH=120>
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
				if(count($account_players) > 0)
					foreach($account_players as $player_from_acc)
						if($player_from_acc->getName() == $invited_player->getName())
							$show_accept_invite++;
				if(is_int($showed_invited / 2)) { $bgcolor = $config['darkborder']; } else { $bgcolor = $config['lightborder']; } $showed_invited++;
				echo '<TR bgcolor="'.$bgcolor.'"><TD>' . getPlayerLink($invited_player->getName());
				if($guild_vice)
					echo '  (<a href="?subtopic=guilds&action=deleteinvite&guild='.$guild->getName().'&name='.$invited_player->getName().'">Cancel Invitation</a>)';
				echo '</TD></TR>';
			}
			echo '</TABLE>';
		}
		echo '<BR><BR>
		<TABLE BORDER=0 WIDTH=100%><TR><TD ALIGN=center><IMG SRC="'.$template_path.'/images/general/blank.gif" WIDTH=80 HEIGHT=1 BORDER=0<BR></TD>';
		if(!$logged)
			echo '<TD ALIGN=center><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="?subtopic=guilds&action=login&guild='.$guild->getName().'&redirect=guild" METHOD=post><TR><TD>
			<INPUT TYPE=image NAME="Login" ALT="Login" SRC="'.$template_path.'/images/buttons/sbutton_login.gif" BORDER=0 WIDTH=120 HEIGHT=18>
			</TD></TR></FORM></TABLE></TD>';
		else
		{
			if($show_accept_invite > 0)
				echo '<TD ALIGN=center><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="?subtopic=guilds&action=acceptinvite&guild='.$guild->getName().'" METHOD=post><TR><TD>
				<INPUT TYPE=image NAME="Accept Invite" ALT="Accept Invite" SRC="'.$template_path.'/images/buttons/sbutton_acceptinvite.png" BORDER=0 WIDTH=120 HEIGHT=18>
				</TD></TR></FORM></TABLE></TD>';
			if($guild_vice)
			{
				echo '<TD ALIGN=center><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="?subtopic=guilds&action=invite&guild='.$guild->getName().'" METHOD=post><TR><TD>
				<INPUT TYPE=image NAME="Invite Player" ALT="Invite Player" SRC="'.$template_path.'/images/buttons/sbutton_inviteplayer.png" BORDER=0 WIDTH=120 HEIGHT=18>
				</TD></TR></FORM></TABLE></TD>';
				echo '<TD ALIGN=center><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="?subtopic=guilds&action=changerank&guild='.$guild->getName().'" METHOD=post><TR><TD>
				<INPUT TYPE=image NAME="Change Rank" ALT="Change Rank" SRC="'.$template_path.'/images/buttons/sbutton_changerank.png" BORDER=0 WIDTH=120 HEIGHT=18>
				</TD></TR></FORM></TABLE></TD>';
			}
			if(count($players_from_account_in_guild) > 0)
				echo '<TD ALIGN=center><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="?subtopic=guilds&action=leaveguild&guild='.$guild->getName().'" METHOD=post><TR><TD>
				<INPUT TYPE=image NAME="Leave Guild" ALT="Leave Guild" SRC="'.$template_path.'/images/buttons/sbutton_leaveguild.png" BORDER=0 WIDTH=120 HEIGHT=18>
				</TD></TR></FORM></TABLE></TD>';
		}
		echo '<TD ALIGN=center><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="?subtopic=guilds" METHOD=post><TR><TD>
		<INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18>
		</TD></TR></FORM></TABLE>
		</TD><TD ALIGN=center><IMG SRC="'.$template_path.'/images/general/blank.gif" WIDTH=80 HEIGHT=1 BORDER=0<BR></TD></TR></TABLE>
		</TD><TD><IMG src="'.$template_path.'/images/general/blank.gif" WIDTH=10 HEIGHT=1 BORDER=0></TD>
		</TR></TABLE></TABLE>';
	}
}



//--------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------
//change rank of player in guild
if($action == 'changerank')
{
	$guild_name = $_REQUEST['guild'];
	if(!check_guild_name($guild_name))
		$guild_errors[] = 'Invalid guild name format.';
	if(!$logged)
		$guild_errors[] = 'You are not logged in. You can\'t change rank.';
	if(empty($guild_errors))
	{
		$guild = $ots->createObject('Guild');
		$guild->find($guild_name);
		if(!$guild->isLoaded())
			$guild_errors[] = 'Guild with name <b>'.$guild_name.'</b> doesn\'t exist.';
	}
	if(!empty($guild_errors))
	{
		//show errors
		echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
		foreach($guild_errors as $guild_error)
			echo '<li>'.$guild_error;
		//errors and back button
		echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br/><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><FORM ACTION="?subtopic=guilds" METHOD=post><TR><TD><center><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></center></TD></TR></FORM></TABLE>';
	}
	else
	{
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
	//tworzenie listy osob z nizszymi uprawnieniami i rank z nizszym levelem
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
				
				if(fieldExist('rank_id', 'players'))
					$players_with_rank = $db->query('SELECT `id`, `rank_id` FROM `players` WHERE `rank_id` = ' . $rank->getId() . ' AND `deleted` = 0;');
				else
					$players_with_rank = $db->query('SELECT `players`.`id` as `id`, `' . GUILD_MEMBERS_TABLE . '`.`rank_id` as `rank_id` FROM `players`, `' . GUILD_MEMBERS_TABLE . '` WHERE `' . GUILD_MEMBERS_TABLE . '`.`rank_id` = ' . $rank->getId() . ' AND `players`.`id` = `' . GUILD_MEMBERS_TABLE . '`.`player_id` ORDER BY `name`;');

				$players_with_rank_number = $players_with_rank->rowCount();
				if(count($players_with_rank) > 0)
				{
					
					foreach($players_with_rank as $result)
					{
						$player = $ots->createObject('Player');
						$player->load($result['id']);
						if(!$player->isLoaded())
							continue;
						
						if($guild->getOwner()->getId() != $player->getId() || $guild_leader)
						{
							$players_with_lower_rank[$sid]['0'] = $player->getName();
							$players_with_lower_rank[$sid]['1'] = $player->getName().' ('.$rank->getName().')';
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
			if(!check_name($player_name))
				$change_errors[] = 'Invalid player name format.';
			$rank = $ots->createObject('GuildRank');
			$rank->load($new_rank);
			if(!$rank->isLoaded())
				$change_errors[] = 'Rank with this ID doesn\'t exist.';
			if($level_in_guild <= $rank->getLevel() && !$guild_leader)
				$change_errors[] = 'You can\'t set ranks with equal or higher level than your.';
			if(empty($change_errors))
			{
				$player_to_change = $ots->createObject('Player');
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
				echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Guild Deleted</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td>Rank of player <b>'.$player_to_change->getName().'</b> has been changed to <b>'.$rank->getName().'</b>.</td></tr>          </table>        </div>  </table></div></td></tr><br>';
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
						
						if(fieldExist('rank_id', 'players'))
							$players_with_rank = $db->query('SELECT `id`, `rank_id` FROM `players` WHERE `rank_id` = ' . $rank->getId() . ' AND `deleted` = 0;');
						else
							$players_with_rank = $db->query('SELECT `players`.`id` as `id`, `' . GUILD_MEMBERS_TABLE . '`.`rank_id` as `rank_id` FROM `players`, `' . GUILD_MEMBERS_TABLE . '` WHERE `' . GUILD_MEMBERS_TABLE . '`.`rank_id` = ' . $rank->getId() . ' AND `players`.`id` = `' . GUILD_MEMBERS_TABLE . '`.`player_id` ORDER BY `name`;');

						$players_with_rank_number = $players_with_rank->rowCount();
						if(count($players_with_rank) > 0)
						{
							foreach($players_with_rank as $result)
							{
								$player = $ots->createObject('Player');
								$player->load($result['id']);
								if(!$player->isLoaded())
									continue;
								
								if($guild->getOwner()->getId() != $player->getId() || $guild_leader)
								{
									$players_with_lower_rank[$sid]['0'] = $player->getName();
									$players_with_lower_rank[$sid]['1'] = $player->getName().' ('.$rank->getName().')';
									$sid++;
								}
							}
						}
					}
				}
			}
			else
			{
				echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
				foreach($change_errors as $change_error)
					echo '<li>'.$change_error;
				echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br/>';
			}
		}
		echo '<FORM ACTION="?subtopic=guilds&action=changerank&guild='.$guild->getName().'&todo=save" METHOD=post>
		<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%>
		<TR BGCOLOR='.$config['vdarkborder'].'><TD class="white"><B>Change Rank</B></TD></TR>
		<TR BGCOLOR='.$config['darkborder'].'><TD>Name: <SELECT NAME="name">';
		foreach($players_with_lower_rank as $player_to_list)
			echo '<OPTION value="'.$player_to_list['0'].'">'.$player_to_list['1'];
		echo '</SELECT>&nbsp;Rank:&nbsp;<SELECT NAME="rankid">';
		foreach($ranks as $rank)
			echo '<OPTION value="'.$rank['0'].'">'.$rank['1'];
		echo '</SELECT>&nbsp;&nbsp;&nbsp;<INPUT TYPE=image NAME="Submit" ALT="Submit" SRC="'.$template_path.'/images/buttons/sbutton_submit.gif" BORDER=0 WIDTH=120 HEIGHT=18></TD><TR>
		</TABLE></FORM><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild->getName().'" METHOD=post><TR><TD><center><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></center></TD></TR></FORM></TABLE>';
	}
	else
		echo 'Error. You are not a leader or vice leader in guild '.$guild->getName().'.<FORM ACTION="?subtopic=guilds&action=show&guild='.$guild->getName().'" METHOD=post><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></FORM>';
	}
}

//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//show guild page
if($action == 'deleteinvite')
{
	//set rights in guild
	$guild_name = $_REQUEST['guild'];
	$name = stripslashes($_REQUEST['name']);
	if(!$logged)
		$guild_errors[] = 'You are not logged in. You can\'t delete invitations.';
	if(!check_guild_name($guild_name))
		$guild_errors[] = 'Invalid guild name format.';
	if(!check_name($name))
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
		echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
		foreach($guild_errors as $guild_error)
			echo '<li>'.$guild_error;
		echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br/><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TR><TD><center><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></center></TD></TR></FORM></TABLE>';
	}
	else
	{
		if(isset($_REQUEST['todo']) && $_REQUEST['todo'] == 'save')
		{
			$guild->deleteInvite($player);
			echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD class="white"><B>Delete player invitation</B></TD></TR><TR BGCOLOR='.$config['darkborder'].'><TD WIDTH=100%>Player with name <b>'.$player->getName().'</b> has been deleted from "invites list".</TD></TR></TABLE><br/><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TR><TD><center><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></center></TD></TR></FORM></TABLE>';
		}
		else
			echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD class="white"><B>Delete player invitation</B></TD></TR><TR BGCOLOR='.$config['darkborder'].'><TD WIDTH=100%>Are you sure you want to delete player with name <b>'.$player->getName().'</b> from "invites list"?</TD></TR></TABLE><br/><center><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><TR><FORM ACTION="?subtopic=guilds&action=deleteinvite&guild='.$guild->getName().'&name='.$player->getName().'&todo=save" METHOD=post><TD align="right" width="50%"><INPUT TYPE=image NAME="Submit" ALT="Submit" SRC="'.$template_path.'/images/buttons/sbutton_submit.gif" BORDER=0 WIDTH=120 HEIGHT=18>&nbsp;&nbsp;</TD></FORM><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TD>&nbsp;&nbsp;<INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></TD></TR></FORM></TABLE></center>';
	}
}

//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//show guild page
if($action == 'invite')
{
//set rights in guild
$guild_name = isset($_REQUEST['guild']) ? $_REQUEST['guild'] : NULL;
$name = isset($_REQUEST['name']) ? stripslashes($_REQUEST['name']) : NULL;
if(!$logged) {
$guild_errors[] = 'You are not logged in. You can\'t invite players.';
}
if(!check_guild_name($guild_name)) {
$guild_errors[] = 'Invalid guild name format.';
}
if(empty($guild_errors)) {
$guild = $ots->createObject('Guild');
$guild->find($guild_name);
if(!$guild->isLoaded()) {
$guild_errors[] = 'Guild with name <b>'.$guild_name.'</b> doesn\'t exist.';
}
}
if(empty($guild_errors)) {
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
if(!$guild_vice) {
$guild_errors[] = 'You are not a leader or vice leader of guild <b>'.$guild_name.'</b>.'.$level_in_guild;
}
if(isset($_REQUEST['todo']) && $_REQUEST['todo'] == 'save') {
if(!check_name($name)) {
$guild_errors[] = 'Invalid name format.';
}
if(empty($guild_errors)) {
$player = new OTS_Player();
$player->find($name);
if(!$player->isLoaded()) {
$guild_errors[] = 'Player with name <b>'.$name.'</b> doesn\'t exist.';
}
else
{
$rank_of_player = $player->getRank();
if($player_rank->isLoaded()) {
$guild_errors[] = 'Player with name <b>'.$name.'</b> is already in guild. He must leave guild before you can invite him.';
}
}
}
}
if(empty($guild_errors)) {
include(SYSTEM . 'libs/pot/InvitesDriver.php');
new InvitesDriver($guild);
$invited_list = $guild->listInvites();
if(count($invited_list) > 0) {
foreach($invited_list as $invited) {
if($invited->getName() == $player->getName()) {
$guild_errors[] = '<b>'.$invited->getName().'</b> is already invited to your guild.';
}
}
}
}

if(!empty($guild_errors)) {
echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
foreach($guild_errors as $guild_error) {
	echo '<li>'.$guild_error;
}
echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br/><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TR><TD><center><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></center></TD></TR></FORM></TABLE>';
}
else
{
if(isset($_REQUEST['todo']) && $_REQUEST['todo'] == 'save') {
$guild->invite($player);
echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD class="white"><B>Invite player</B></TD></TR><TR BGCOLOR='.$config['darkborder'].'><TD WIDTH=100%>Player with name <b>'.$player->getName().'</b> has been invited to your guild.</TD></TR></TABLE><br/><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TR><TD><center><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></center></TD></TR></FORM></TABLE>';
}
else
{
echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD class="white"><B>Invite player</B></TD></TR><TR BGCOLOR='.$config['darkborder'].'><TD WIDTH=100%><FORM ACTION="?subtopic=guilds&action=invite&guild='.$guild->getName().'&todo=save" METHOD=post>Invite player with name:&nbsp;&nbsp;<INPUT TYPE="text" NAME="name">&nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE=image NAME="Submit" ALT="Submit" SRC="'.$template_path.'/images/buttons/sbutton_submit.gif" BORDER=0 WIDTH=120 HEIGHT=18></FORM></TD></TD></TR></TR></TABLE><br/><center><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><TR><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TD><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></TD></TR></FORM></TABLE></center>';
}
}
}


//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//show guild page
if($action == 'acceptinvite') {
	//set rights in guild
	$guild_name = isset($_REQUEST['guild']) ? $_REQUEST['guild'] : NULL;
	$name = isset($_REQUEST['name']) ? stripslashes($_REQUEST['name']) : NULL;
	if(!$logged) {
		$guild_errors[] = 'You are not logged in. You can\'t accept invitations.';
	}
	if(!check_guild_name($guild_name)) {
		$guild_errors[] = 'Invalid guild name format.';
	}
	if(empty($guild_errors)) {
		$guild = $ots->createObject('Guild');
		$guild->find($guild_name);
		if(!$guild->isLoaded()) {
			$guild_errors[] = 'Guild with name <b>'.$guild_name.'</b> doesn\'t exist.';
		}
	}

	if(isset($_REQUEST['todo']) && $_REQUEST['todo'] == 'save') {
		if(!check_name($name)) {
			$guild_errors[] = 'Invalid name format.';
		}
	
		if(empty($guild_errors)) {
			$player = new OTS_Player();
			$player->find($name);
			if(!$player->isLoaded()) {
				$guild_errors[] = 'Player with name <b>'.$name.'</b> doesn\'t exist.';
			}
			else
			{
				$rank_of_player = $player->getRank();
				if($rank_of_player->isLoaded()) {
					$guild_errors[] = 'Character with name <b>'.$name.'</b> is already in guild. You must leave guild before you join other guild.';
				}
			}
		}
	}

	if(isset($_REQUEST['todo']) && $_REQUEST['todo'] == 'save') {
		if(empty($guild_errors)) {
			$is_invited = false;
			include(SYSTEM . 'libs/pot/InvitesDriver.php');
			new InvitesDriver($guild);
			$invited_list = $guild->listInvites();
			if(count($invited_list) > 0) {
				foreach($invited_list as $invited) {
					if($invited->getName() == $player->getName()) {
						$is_invited = true;
					}
				}
			}

			if(!$is_invited) {
				$guild_errors[] = 'Character '.$player->getName.' isn\'t invited to guild <b>'.$guild->getName().'</b>.';
			}
		}
	}
	else
	{
		if(empty($guild_errors)) {
			$acc_invited = false;
			$account_players = $account_logged->getPlayers();
			include(SYSTEM . 'libs/pot/InvitesDriver.php');
			new InvitesDriver($guild);
			$invited_list = $guild->listInvites();

			if(count($invited_list) > 0) {
				foreach($invited_list as $invited) {
					foreach($account_players as $player_from_acc) {
						if($invited->getName() == $player_from_acc->getName()) {
							$acc_invited = true;
							$list_of_invited_players[] = $player_from_acc->getName();
						}
					}
				}
			}
		}

		if(!$acc_invited) {
			$guild_errors[] = 'Any character from your account isn\'t invited to <b>'.$guild->getName().'</b>.';
		}
	}
	if(!empty($guild_errors)) {
		echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
		foreach($guild_errors as $guild_error) {
			echo '<li>'.$guild_error;
		}
		echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br/><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TR><TD><center><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></center></TD></TR></FORM></TABLE>';
	}
	else {
		if(isset($_REQUEST['todo']) && $_REQUEST['todo'] == 'save') {
		$guild->acceptInvite($player);
		echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD class="white"><B>Accept invitation</B></TD></TR><TR BGCOLOR='.$config['darkborder'].'><TD WIDTH=100%>Player with name <b>'.$player->getName().'</b> has been added to guild <b>'.$guild->getName().'</b>.</TD></TR></TABLE><br/><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TR><TD><center><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></center></TD></TR></FORM></TABLE>';
		}
		else
		{
			echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD class="white"><B>Accept invitation</B></TD></TR>';
			echo '<TR BGCOLOR='.$config['lightborder'].'><TD WIDTH=100%>Select character to join guild:</TD></TR>';
			echo '<TR BGCOLOR='.$config['darkborder'].'><TD>
		<form action="?subtopic=guilds&action=acceptinvite&guild='.$guild_name.'&todo=save" METHOD="post">';
			sort($list_of_invited_players);
			$i = 0;
			foreach($list_of_invited_players as $invited_player_from_list) {
				echo '<input type="radio" name="name" id="name_' . $i . '" value="'.$invited_player_from_list.'" /><label for="name_' . $i++ . '">'.$invited_player_from_list.'</label><br>';
			}
			echo '<br><INPUT TYPE=image NAME="Submit" ALT="Submit" SRC="'.$template_path.'/images/buttons/sbutton_submit.gif" BORDER=0 WIDTH=120 HEIGHT=18></form></TD></TR></TABLE><br/><center><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><TR><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TD><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></TD></TR></FORM></TABLE></center>';
		}
	}
}


//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//show guild page
if($action == 'kickplayer') {
	//set rights in guild
	$guild_name = $_REQUEST['guild'];
	$name = stripslashes($_REQUEST['name']);
	if(!$logged) {
		$guild_errors[] = 'You are not logged in. You can\'t kick characters.';
	}
	
	if(!check_guild_name($guild_name)) {
		$guild_errors[] = 'Invalid guild name format.';
	}
	
	if(!check_name($name)) {
		$guild_errors[] = 'Invalid name format.';
	}
	
	if(empty($guild_errors)) {
		$guild = $ots->createObject('Guild');
		$guild->find($guild_name);
		if(!$guild->isLoaded()) {
			$guild_errors[] = 'Guild with name <b>'.$guild_name.'</b> doesn\'t exist.';
		}
	}

	if(empty($guild_errors)) {
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
	
	if(empty($guild_errors)) {
		if(!$guild_leader && $level_in_guild < 3) {
			$guild_errors[] = 'You are not a leader of guild <b>'.$guild_name.'</b>. You can\'t kick players.';
		}
	}
	
	if(empty($guild_errors)) {
		$player = new OTS_Player();
		$player->find($name);
		if(!$player->isLoaded()) {
			$guild_errors[] = 'Character <b>'.$name.'</b> doesn\'t exist.';
		}
		else
		{
			if($player->getRank()->isLoaded() && $player->getRank()->getGuild()->isLoaded() && $player->getRank()->getGuild()->getName() != $guild->getName()) {
				$guild_errors[] = 'Character <b>'.$name.'</b> isn\'t from your guild.';
			}
		}
	}
	
	if(empty($guild_errors)) {
		if($player->getRank()->isLoaded() && $player->getRank()->getLevel() >= $level_in_guild && !$guild_leader) {
			$guild_errors[] = 'You can\'t kick character <b>'.$name.'</b>. Too high access level.';
		}
	}
	
	if(empty($guild_errors)) {
		if($guild->getOwner()->getName() == $player->getName()) {
			$guild_errors[] = 'It\'s not possible to kick guild owner!';
		}
	}

	if(!empty($guild_errors)) {
		echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
		foreach($guild_errors as $guild_error) {
			echo '<li>'.$guild_error;
		}
		echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br/><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TR><TD><center><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></center></TD></TR></FORM></TABLE>';
	}
	else
	{
		if(isset($_REQUEST['todo']) && $_REQUEST['todo'] == 'save') {
			$player->setRank();
			echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD class="white"><B>Kick player</B></TD></TR><TR BGCOLOR='.$config['darkborder'].'><TD WIDTH=100%>Player with name <b>'.$player->getName().'</b> has been kicked from your guild.</TD></TR></TABLE><br/><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TR><TD><center><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></center></TD></TR></FORM></TABLE>';
		}
		else
		{
			echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD class="white"><B>Kick player</B></TD></TR><TR BGCOLOR='.$config['darkborder'].'><TD WIDTH=100%>Are you sure you want to kick player with name <b>'.$player->getName().'</b> from your guild?</TD></TR></TABLE><br/><center><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><TR><FORM ACTION="?subtopic=guilds&action=kickplayer&guild='.$guild->getName().'&name='.$player->getName().'&todo=save" METHOD=post><TD align="right" width="50%"><INPUT TYPE=image NAME="Submit" ALT="Submit" SRC="'.$template_path.'/images/buttons/sbutton_submit.gif" BORDER=0 WIDTH=120 HEIGHT=18>&nbsp;&nbsp;</TD></FORM><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TD>&nbsp;&nbsp;<INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></TD></TR></FORM></TABLE></center>';
		}
	}
}

//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//show guild page
if($action == 'leaveguild') {
	//set rights in guild
	$guild_name = isset($_REQUEST['guild']) ? $_REQUEST['guild'] : NULL;
	$name = isset($_REQUEST['name']) ? stripslashes($_REQUEST['name']) : NULL;
	if(!$logged) {
		$guild_errors[] = 'You are not logged in. You can\'t leave guild.';
	}
	
	if(!check_guild_name($guild_name)) {
		$guild_errors[] = 'Invalid guild name format.';
	}
	
	if(empty($guild_errors)) {
		$guild = $ots->createObject('Guild');
		$guild->find($guild_name);
		if(!$guild->isLoaded()) {
			$guild_errors[] = 'Guild with name <b>'.$guild_name.'</b> doesn\'t exist.';
		}
	}

	$array_of_player_ig = array();
	if(empty($guild_errors)) {
		$guild_owner_name = $guild->getOwner()->getName();
		if(isset($_REQUEST['todo']) && $_REQUEST['todo'] == 'save') {
			if(!check_name($name)) {
				$guild_errors[] = 'Invalid name format.';
			}
			
			if(empty($guild_errors)) {
				$player = new OTS_Player();
				$player->find($name);
				if(!$player->isLoaded()) {
					$guild_errors[] = 'Character <b>'.$name.'</b> doesn\'t exist.';
				}
				else {
					if($player->getAccount()->getId() != $account_logged->getId()) {
						$guild_errors[] = 'Character <b>'.$name.'</b> isn\'t from your account!';
					}
				}
			}
			
			if(empty($guild_errors)) {
				$player_loaded_rank = $player->getRank();
				if($player_loaded_rank->isLoaded()) {
					if($player_loaded_rank->getGuild()->getName() != $guild->getName()) {
						$guild_errors[] = 'Character <b>'.$name.'</b> isn\'t from guild <b>'.$guild->getName().'</b>.';
					}
				}
				else {
					$guild_errors[] = 'Character <b>'.$name.'</b> isn\'t in any guild.';
				}
			}
	
			if(empty($guild_errors)) {
				if($guild_owner_name == $player->getName()) {
					$guild_errors[] = 'You can\'t leave guild. You are an owner of guild.';
				}
			}
		}
		else
		{
			$account_players = $account_logged->getPlayers();
			foreach($account_players as $player_fac) {
				$player_rank = $player_fac->getRank();
				if($player_rank->isLoaded()) {
					if($player_rank->getGuild()->getId() == $guild->getId()) {
						if($guild_owner_name != $player_fac->getName()) {
							$array_of_player_ig[] = $player_fac->getName();
						}
					}
				}
			}
		}
	}

	if(!empty($guild_errors)) {
		echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
		foreach($guild_errors as $guild_error) {
			echo '<li>'.$guild_error;
		}
		echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br/><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TR><TD><center><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></center></TD></TR></FORM></TABLE>';
	}
	else
	{
		if(isset($_REQUEST['todo']) && $_REQUEST['todo'] == 'save') {
			$player->setRank();
			echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD class="white"><B>Leave guild</B></TD></TR><TR BGCOLOR='.$config['darkborder'].'><TD WIDTH=100%>Player with name <b>'.$player->getName().'</b> leaved guild <b>'.$guild->getName().'</b>.</TD></TR></TABLE><br/><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TR><TD><center><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></center></TD></TR></FORM></TABLE>';
		}
		else
		{
			echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD class="white"><B>Leave guild</B></TD></TR>';
			if(count($array_of_player_ig) > 0) {
				echo '<TR BGCOLOR='.$config['lightborder'].'><TD WIDTH=100%>Select character to leave guild:</TD></TR>';
				echo '<TR BGCOLOR='.$config['darkborder'].'><TD>
				<form action="?subtopic=guilds&action=leaveguild&guild='.$guild_name.'&todo=save" METHOD="post">';
				
				sort($array_of_player_ig);
				foreach($array_of_player_ig as $player_to_leave) {
					echo '<input type="radio" name="name" value="'.$player_to_leave.'" />'.$player_to_leave.'<br>';
				}
				echo '</TD></TR><br></TABLE>';
			}
			else {
				echo '<TR BGCOLOR='.$config['lightborder'].'><TD WIDTH=100%>Any of your characters can\'t leave guild.</TD></TR>';
			}
			
			echo '<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><tr>';
			if(count($array_of_player_ig) > 0) {
				echo '<td width="130" valign="top"><INPUT TYPE=image NAME="Submit" ALT="Submit" SRC="'.$template_path.'/images/buttons/sbutton_submit.gif" BORDER=0 WIDTH=120 HEIGHT=18></form></td>';
			}
			
			echo '<td><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></FORM></td></tr></table>';
		}
	}
}

//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//create guild
if($action == 'createguild')
{
	$guild_name = isset($_REQUEST['guild']) ? $_REQUEST['guild'] : NULL;
	$name = isset($_REQUEST['name']) ? stripslashes($_REQUEST['name']) : NULL;
	$todo = isset($_REQUEST['todo']) ? $_REQUEST['todo'] : NULL;
	if(!$logged) {
		$guild_errors[] = 'You are not logged in. You can\'t create guild.';
	}

	$array_of_player_nig = array();
	if(empty($guild_errors))
	{
		$account_players = $account_logged->getPlayers();
		foreach($account_players as $player)
		{
			$player_rank = $player->getRank();
			if(!$player_rank->isLoaded())
			{
				if($player->getLevel() >= $config['guild_need_level']) {
					if(!$config['guild_need_premium'] || $account_logged->isPremium()) {
						$array_of_player_nig[] = $player->getName();
					}
				}
			}
		}
	}

	if(empty($todo)) {
		if(count($array_of_player_nig) == 0) {
			$guild_errors[] = 'On your account all characters are in guilds, have too low level to create new guild' . ($config['guild_need_premium'] ? ' or you don\' have a premium account' : '') . '.';
		}
	}

	if($todo == 'save')
	{
		if(!check_guild_name($guild_name)) {
			$guild_errors[] = 'Invalid guild name format.';
			$guild_name = '';
		}

		if(!check_name($name)) {
			$guild_errors[] = 'Invalid character name format.';
			$name = '';
		}

		if(empty($guild_errors)) {
			$player = $ots->createObject('Player');
			$player->find($name);
			if(!$player->isLoaded()) {
				$guild_errors[] = 'Character <b>'.$name.'</b> doesn\'t exist.';
			}
		}


		if(empty($guild_errors))
		{
			$guild = $ots->createObject('Guild');
			$guild->find($guild_name);
			if($guild->isLoaded()) {
				$guild_errors[] = 'Guild <b>'.$guild_name.'</b> already exist. Select other name.';
			}
		}

		if(empty($guild_errors))
		{
			$bad_char = true;
			foreach($array_of_player_nig as $nick_from_list) {
				if($nick_from_list == $player->getName()) {
				$bad_char = false;
				}
			}
			if($bad_char) {
				$guild_errors[] = 'Character <b>'.$name.'</b> isn\'t on your account or is already in guild.';
			}
		}

		if(empty($guild_errors)) {
			if($player->getLevel() < $config['guild_need_level']) {
			$guild_errors[] = 'Character <b>'.$name.'</b> has too low level. To create guild you need character with level <b>'.$config['guild_need_level'].'</b>.';
			}
			if($config['guild_need_premium'] && !$account_logged->isPremium()) {
			$guild_errors[] = 'Character <b>'.$name.'</b> is on FREE account. To create guild you need PREMIUM account.';
			}
		}
	}

if(!empty($guild_errors)) {
echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
foreach($guild_errors as $guild_error) {
	echo '<li>'.$guild_error;
}
echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br>';
unset($todo);
}

if(isset($todo) && $todo == 'save')
{
	$new_guild = new OTS_Guild();
	$new_guild->setCreationData(time());
	$new_guild->setName($guild_name);
	$new_guild->setOwner($player);
	$new_guild->save();
	$new_guild->setCustomField('description', 'New guild. Leader must edit this text :)');
	//$new_guild->setCustomField('creationdata', time());
	$ranks = $new_guild->getGuildRanksList();
	$ranks->orderBy('level', POT::ORDER_DESC);
	foreach($ranks as $rank) {
		if($rank->getLevel() == 3) {
			$player->setRank($rank);
		}
	}
	echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD class="white"><B>Create guild</B></TD></TR><TR BGCOLOR='.$config['darkborder'].'><TD WIDTH=100%><b>Congratulations!</b><br/>You have created guild <b>'.$guild_name.'</b>. <b>'.$player->getName().'</b> is leader of this guild. Now you can invite players, change picture, description and motd of guild. Press submit to open guild manager.</TD></TR></TABLE><br/><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TR><TD><center><INPUT TYPE=image NAME="Submit" ALT="Submit" SRC="'.$template_path.'/images/buttons/sbutton_Submit.gif" BORDER=0 WIDTH=120 HEIGHT=18></center></TD></TR></FORM></TABLE>';
	/*$db->query('INSERT INTO `guild_ranks` (`id`, `guild_id`, `name`, `level`) VALUES (null, '.$new_guild->getId().', "the Leader", 3)');
	$db->query('INSERT INTO `guild_ranks` (`id`, `guild_id`, `name`, `level`) VALUES (null, '.$new_guild->getId().', "a Vice-Leader", 2)');
	$db->query('INSERT INTO `guild_ranks` (`id`, `guild_id`, `name`, `level`) VALUES (null, '.$new_guild->getId().', "a Member", 1)');*/
}
else
{
echo '
<FORM ACTION="?subtopic=guilds&action=createguild&todo=save" METHOD=post>
<TABLE WIDTH=100% BORDER=0 CELLSPACING=1 CELLPADDING=4>
<TR><TD BGCOLOR="'.$config['vdarkborder'].'" class="white"><B>Create a ' . $config['lua']['serverName'] . ' Guild</B></TD></TR>
<TR><TD BGCOLOR="'.$config['darkborder'].'"><TABLE BORDER=0 CELLSPACING=8 CELLPADDING=0>
  <TR><TD>
    <TABLE BORDER=0 CELLSPACING=5 CELLPADDING=0>';
echo '<TR><TD width="150" valign="top"><B>Leader: </B></TD><TD><SELECT name=\'name\'>';
if(count($array_of_player_nig) > 0) {
sort($array_of_player_nig);
foreach($array_of_player_nig as $nick) {
echo '<OPTION>'.$nick;
}
}
echo '</SELECT><BR><font size="1" face="verdana,arial,helvetica">(Name of leader of new guild.)</font></TD></TR>
	<TR><TD width="150" valign="top"><B>Guild name: </B></TD><TD><INPUT NAME="guild" VALUE="" SIZE=30 MAXLENGTH=50><BR><font size="1" face="verdana,arial,helvetica">(Here write name of your new guild.)</font></TD></TR>
	</TABLE>
  </TD></TR>
</TABLE></TD></TR>
</TABLE>
<BR>
<TABLE BORDER=0 WIDTH=100%>
  <TR><TD ALIGN=center>
    <IMG SRC="'.$template_path.'/images/general/blank.gif" WIDTH=120 HEIGHT=1 BORDER=0><BR>
  </TD><TD ALIGN=center VALIGN=top>
    <INPUT TYPE=image NAME="Submit" SRC="'.$template_path.'/images/buttons/sbutton_submit.gif" BORDER=0 WIDTH=120 HEIGHT=18>
    </FORM>
  </TD><TD ALIGN=center>
    <FORM  ACTION="?subtopic=guilds" METHOD=post>
    <INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18>
    </FORM>
  </TD><TD ALIGN=center>
    <IMG src="'.$template_path.'/images/general/blank.gif" WIDTH=120 HEIGHT=1 BORDER=0><BR>
  </TD></TR>
</TABLE>
</TD>
<TD><IMG SRC="'.$template_path.'/images/general/blank.gif" WIDTH=10 HEIGHT=1 BORDER=0></TD>
</TR>
</TABLE>';
}
}
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
if($action == 'manager') {
$guild_name = $_REQUEST['guild'];
if(!check_guild_name($guild_name)) {
$guild_errors[] = 'Invalid guild name format.';
}
if(empty($guild_errors)) {
$guild = $ots->createObject('Guild');
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
echo '<center><h2>Welcome to guild manager!</h2></center>Here you can change names of ranks, delete and add ranks, pass leadership to other guild member and delete guild.';
echo '<br/><br/><table style=\'clear:both\' border=0 cellpadding=0 cellspacing=0 width=\'100%\'>
<tr bgcolor='.$config['darkborder'].'><td width="170"><font color="red"><b>Option</b></font></td><td><font color="red"><b>Description</b></font></td></tr>
<tr bgcolor='.$config['lightborder'].'><td width="170"><b><a href="?subtopic=guilds&guild='.$guild->getName().'&action=passleadership">Pass Leadership</a></b></td><td><b>Pass leadership of guild to other guild member.</b></td></tr>
<tr bgcolor='.$config['darkborder'].'><td width="170"><b><a href="?subtopic=guilds&guild='.$guild->getName().'&action=deleteguild">Delete Guild</a></b></td><td><b>Delete guild, kick all members.</b></td></tr>
<tr bgcolor='.$config['lightborder'].'><td width="170"><b><a href="?subtopic=guilds&guild='.$guild->getName().'&action=changedescription">Change Description</a></b></td><td><b>Change description of guild.</b></td></tr>
<tr bgcolor='.$config['darkborder'].'><td width="170"><b><a href="?subtopic=guilds&guild='.$guild->getName().'&action=changemotd">Change MOTD</a></b></td><td><b>Change MOTD of guild.</b></td></tr>
<tr bgcolor='.$config['lightborder'].'><td width="170"><b><a href="?subtopic=guilds&guild='.$guild->getName().'&action=changelogo">Change guild logo</a></b></td><td><b>Upload new guild logo.</b></td></tr>
</table>';
echo '<br><div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Add new rank</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td width="120" valign="top">New rank name:</td><td> <form action="?subtopic=guilds&guild='.$guild->getName().'&action=addrank" method="POST"><input type="text" name="rank_name" size="20"><input type="submit" value="Add"></form></td></tr>          </table>        </div>  </table></div></td></tr>';
echo '<center><h3>Change rank names and levels</h3></center><form action="?subtopic=guilds&action=saveranks&guild='.$guild->getName().'" method=POST><table style=\'clear:both\' border=0 cellpadding=0 cellspacing=0 width=\'100%\'><tr bgcolor='.$config['vdarkborder'].'><td rowspan="2" width="120" align="center"><font color="white"><b>ID/Delete Rank</b></font></td><td rowspan="2" width="300"><font color="white"><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Name</b></font></td><td colspan="3" align="center"><font color="white"><b>Level of RANK in guild</b></font></td></tr><tr bgcolor='.$config['vdarkborder'].'><td align="center" bgcolor="red"><font color="white"><b>Leader (3)</b></font></td><td align="center" bgcolor="yellow"><font color="black"><b>Vice (2)</b></font></td><td align="center" bgcolor="green"><font color="white"><b>Member (1)</b></font></td></tr>';
$number_of_ranks = count($rank_list);
$number_of_rows = 0;
foreach($rank_list as $rank) {
echo '<tr bgcolor="' . getStyle($number_of_rows++) . '"><td align="center">'.$rank->getId().' // <a href="?subtopic=guilds&guild='.$guild->getName().'&action=deleterank&rankid='.$rank->getId().'" border="0"><img src="'.$template_path.'/images/news/delete.png" border="0" alt="Delete Rank"></a></td><td><input type="text" name="'.$rank->getId().'_name" value="'.$rank->getName().'" size="35"></td><td align="center"><input type="radio" name="'.$rank->getId().'_level" value="3"';
if($rank->getLevel() == 3) {
echo ' checked="checked"';
}
echo ' /></td><td align="center"><input type="radio" name="'.$rank->getId().'_level" value="2"';
if($rank->getLevel() == 2) {
echo ' checked="checked"';
}
echo ' /></td><td align="center"><input type="radio" name="'.$rank->getId().'_level" value="1"';
if($rank->getLevel() == 1) {
echo ' checked="checked"';
}
echo ' /></td></tr>';

}
echo '<tr bgcolor='.$config['vdarkborder'].'><td>&nbsp;</td><td>&nbsp;</td><td colspan="3" align="center"><input type="submit" value="Save All"></td></tr></table></form>';
echo '<h3>Ranks info:</h3><b>0. Owner of guild</b> - it\'s highest rank, only one player in guild may has this rank. Player with this rank can:
<li>Invite/Cancel Invitation/Kick Player from guild
<li>Change ranks of all players in guild
<li>Delete guild or pass leadership to other guild member
<li>Change names, levels(leader,vice,member), add and delete ranks
<li>Change MOTD, logo and description of guild<hr>
<b>3. Leader</b> - it\'s second rank in guild. Player with this rank can:
<li>Invite/Cancel Invitation/Kick Player from guild (only with lower rank than his)
<li>Change ranks of players with lower rank level ("vice leader", "member") in guild<hr>
<b>2. Vice Leader</b> - it\'s third rank in guild. Player with this rank can:
<li>Invite/Cancel Invitation
<li>Change ranks of players with lower rank level ("member") in guild<hr>
<b>1. Member</b> - it\'s lowest rank in guild. Player with this rank can:
<li>Be a member of guild';
echo '<br/><center><form action="?subtopic=guilds&action=show&guild='.$guild->getName().'" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
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
echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
foreach($guild_errors as $guild_error) {
	echo '<li>'.$guild_error;
}
echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br>';
}
}
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
if($action == 'changelogo') {
$guild_name = $_REQUEST['guild'];
if(!check_guild_name($guild_name)) {
$guild_errors[] = 'Invalid guild name format.';
}
if(empty($guild_errors)) {
$guild = $ots->createObject('Guild');
$guild->find($guild_name);
if(!$guild->isLoaded()) {
$guild_errors[] = 'Guild with name <b>'.$guild_name.'</b> doesn\'t exist.';
}
}
if(empty($guild_errors)) {
if($logged) {
$guild_leader_char = $guild->getOwner();
$guild_leader = false;
$account_players = $account_logged->getPlayers();
foreach($account_players as $player) {
if($guild_leader_char->getId() == $player->getId()) {
$guild_vice = true;
$guild_leader = true;
$level_in_guild = 3;
}
}
if($guild_leader)
{
	$max_image_size_b = $config['guild_image_size_kb'] * 1024;
	$allowed_ext = array('image/gif', 'image/jpg', 'image/pjpeg', 'image/jpeg', 'image/bmp', 'image/png', 'image/x-png');
	$ext_name = array('image/gif' => 'gif', 'image/jpg' => 'jpg', 'image/jpeg' => 'jpg', 'image/pjpeg' => 'jpg', 'image/bmp' => 'bmp', 'image/png' => 'png', 'image/x-png' => 'png');
	$save_file_name = str_replace(' ', '_', strtolower($guild->getName()));
	$save_path = 'images/guilds/' . $save_file_name;
	if(isset($_REQUEST['todo']) && $_REQUEST['todo'] == 'save')
	{
		$file = $_FILES['newlogo'];
		if(is_uploaded_file($file['tmp_name']))
		{
			if($file['size'] > $max_image_size_b) {
				$upload_errors[] = 'Uploaded image is too big. Size: <b>'.$file['size'].' bytes</b>, Max. size: <b>'.$max_image_size_b.' bytes</b>.';
			}

			$type = strtolower($file['type']);
			if(!in_array($type, $allowed_ext)) {
				$upload_errors[] = 'Your file type isn\' allowed. Allowed: <b>gif, jpg, bmp, png</b>. Your file type: <b>'.$type.'</b> If it\'s valid image contact with admin.';
			}
		}
		else
		{
		$upload_errors[] = 'You didn\'t send file or file is too big. Limit: <b>'.$config['guild_image_size_kb'].' KB</b>.';
		}
		if(empty($upload_errors)) {
		$extension = $ext_name[$type];
		if(!move_uploaded_file($file['tmp_name'], $save_path.'.'.$extension)) {
		$upload_errors[] = 'Sorry! Can\'t save your image.';
		}
		}
		if(empty($upload_errors))
		{
			$guild_logo = $guild->getCustomField('logo_name');
			$guild_logo = str_replace(array('..', '/', '\\'), array('','',''), $guild->getCustomField('logo_name'));
			if(empty($guild_logo) || !file_exists('images/guilds/' . $guild_logo)) {
				$guild_logo = "default.gif";
			}
			if($guild_logo != "default.gif" && $guild_logo != $save_file_name.'.'.$extension) {
				unlink('images/guilds/' . $guild_logo);
			}
		}
		//show errors or save file
		if(!empty($upload_errors)) {
		echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
		foreach($upload_errors as $guild_error) {
			echo '<li>'.$guild_error;
		}
		echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br>';
		}
		else
		{
		$guild->setCustomField('logo_name', $save_file_name.'.'.$extension);
		}
	}
	$guild_logo = $guild->getCustomField('logo_name');
	if(empty($guild_logo) || !file_exists('images/guilds/' . $guild_logo)) {
	$guild_logo = "default.gif";
	}
	echo '<center><h2>Change guild logo</h2></center>Here you can change logo of your guild.<BR>Actuall logo: <img src="images/guilds/' .$guild_logo.'" HEIGHT="64" WIDTH="64"><BR><BR>';
	echo '<form enctype="multipart/form-data" action="?subtopic=guilds&guild='.$guild->getName().'&action=changelogo" method="POST">
	<input type="hidden" name="todo" value="save" />
	<input type="hidden" name="MAX_FILE_SIZE" value="'.$max_image_size_b.'" />
		Select new logo: <input name="newlogo" type="file" />
		<input type="submit" value="Send new logo" /></form>Only <b>jpg, gif, png, bmp</b> pictures. Max. size: <b>'.$config['guild_image_size_kb'].' KB</b><br>';
	echo '<br/><center><form action="?subtopic=guilds&guild='.$guild->getName().'&action=manager" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';

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
echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
foreach($guild_errors as $guild_error) {
	echo '<li>'.$guild_error;
}
echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br>';
echo '<br/><center><form action="?subtopic=guilds" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
}
}


//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
if($action == 'deleterank') {
$guild_name = $_REQUEST['guild'];
$rank_to_delete = (int) $_REQUEST['rankid'];
if(!check_guild_name($guild_name)) {
$guild_errors[] = 'Invalid guild name format.';
}
if(empty($guild_errors)) {
$guild = $ots->createObject('Guild');
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
if($guild->getOwner()->getId() == $player->getId()) {
$guild_vice = true;
$guild_leader = true;
$level_in_guild = 3;
}
}
if($guild_leader) {
$rank = new OTS_GuildRank();
$rank->load($rank_to_delete);
if(!$rank->isLoaded()) {
$guild_errors2[] = 'Rank with ID '.$rank_to_delete.' doesn\'t exist.';
}
else
{
if($rank->getGuild()->getId() != $guild->getId()) {
$guild_errors2[] = 'Rank with ID '.$rank_to_delete.' isn\'t from your guild.';
}
else
{
if(count($rank_list) < 2) {
$guild_errors2[] = 'You have only 1 rank in your guild. You can\'t delete this rank.';
}
else
{
	if(fieldExist('rank_id', 'players'))
		$players_with_rank = $db->query('SELECT `id`, `rank_id` FROM `players` WHERE `rank_id` = ' . $rank->getId() . ' AND `deleted` = 0;');
	else
		$players_with_rank = $db->query('SELECT `players`.`id` as `id`, `' . GUILD_MEMBERS_TABLE . '`.`rank_id` as `rank_id` FROM `players`, `' . GUILD_MEMBERS_TABLE . '` WHERE `' . GUILD_MEMBERS_TABLE . '`.`rank_id` = ' . $rank->getId() . ' AND `players`.`id` = `' . GUILD_MEMBERS_TABLE . '`.`player_id` ORDER BY `name`;');

	$players_with_rank_number = $players_with_rank->rowCount();
	if($players_with_rank_number > 0) {
		foreach($rank_list as $checkrank) {
			if($checkrank->getId() != $rank->getId()) {
				if($checkrank->getLevel() <= $rank->getLevel()) {
					$new_rank = $checkrank;
				}
			}
		}
	
		if(empty($new_rank)) {
			$new_rank = new OTS_GuildRank();
			$new_rank->setGuild($guild);
			$new_rank->setLevel($rank->getLevel());
			$new_rank->setName('New Rank level '.$rank->getLevel());
			$new_rank->save();
		}
		foreach($players_with_rank as $player_in_guild) {
			$player_in_guild->setRank($new_rank);
		}
	}
	$rank->delete();
	$saved = true;
}
}
}
if($saved) {
echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Rank Deleted</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td>Rank <b>'.$rank->getName().'</b> has been deleted. Players with this rank has now other rank.</td></tr>          </table>        </div>  </table></div></td></tr>';
} else {
echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
foreach($guild_errors2 as $guild_error) {
	echo '<li>'.$guild_error;
}
echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br>';
}
//back button
echo '<br/><center><form action="?subtopic=guilds&guild='.$guild->getName().'&action=manager" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
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
echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
foreach($guild_errors as $guild_error) {
	echo '<li>'.$guild_error;
}
echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br>';
echo '<br/><center><form action="?subtopic=guilds" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
}
}
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
if($action == 'addrank') {
$guild_name = $_REQUEST['guild'];
$ranknew = $_REQUEST['rank_name'];
if(!check_guild_name($guild_name)) {
$guild_errors[] = 'Invalid guild name format.';
}
if(empty($guild_errors)) {
if(!check_rank_name($ranknew)) {
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
else
{
$guild_errors[] = 'You are not a leader of guild!';
}
}
if(!empty($guild_errors)) {
echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
foreach($guild_errors as $guild_error) {
	echo '<li>'.$guild_error;
}
echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br>';
echo '<br/><center><form action="?subtopic=guilds&guild='.$guild_name.'&action=show" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
}
}
else
{
if(!empty($guild_errors)) {
echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
foreach($guild_errors as $guild_error) {
	echo '<li>'.$guild_error;
}
echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br>';
echo '<br/><center><form action="?subtopic=guilds" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
}
}
}

//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
if($action == 'changedescription') {
	$guild_name = $_REQUEST['guild'];
	if(!check_guild_name($guild_name)) {
		$guild_errors[] = 'Invalid guild name format.';
	}
	
	if(empty($guild_errors)) {
		$guild = $ots->createObject('Guild');
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
				if($guild->getOwner()->getId() == $player->getId()) {
					$guild_vice = true;
					$guild_leader = true;
					$level_in_guild = 3;
				}
			}
			
			$saved = false;
			if($guild_leader) {
				if(isset($_REQUEST['todo']) && $_REQUEST['todo'] == 'save') {
					$description = htmlspecialchars(stripslashes(substr(trim($_REQUEST['description']),0,$config['guild_description_chars_limit'])));
					$guild->setCustomField('description', $description);
					$saved = true;
				}
				echo '<center><h2>Change guild description</h2></center>';
				if($saved) {
					echo '<center><font color="red" size="3"><b>CHANGES HAS BEEN SAVED!</b></font></center><br>';
				}
				echo 'Here you can change description of your guild.<BR>';
				echo '<form enctype="multipart/form-data" action="?subtopic=guilds&guild='.$guild->getName().'&action=changedescription" method="POST">
				<input type="hidden" name="todo" value="save" />
					<textarea name="description" cols="60" rows="'.bcsub($config['guild_description_lines_limit'],1).'">'.$guild->getCustomField('description').'</textarea><br>
					(max. '.$config['guild_description_lines_limit'].' lines, max. '.$config['guild_description_chars_limit'].' chars) <input type="submit" value="Save description" /></form><br>';
				echo '<br/><center><form action="?subtopic=guilds&guild='.$guild->getName().'&action=manager" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
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
	echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
	foreach($guild_errors as $guild_error) {
		echo '<li>'.$guild_error;
	}
	echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br>';
	echo '<br/><center><form action="?subtopic=guilds" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
	}
}

//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
if($action == 'passleadership') {
	$guild_name = isset($_REQUEST['guild']) ? $_REQUEST['guild'] : NULL;
	$pass_to = isset($_REQUEST['player']) ? stripslashes($_REQUEST['player']) : NULL;
	if(!check_guild_name($guild_name)) {
		$guild_errors[] = 'Invalid guild name format.';
	}
	
	if(empty($guild_errors)) {
		$guild = $ots->createObject('Guild');
		$guild->find($guild_name);
		if(!$guild->isLoaded()) {
			$guild_errors[] = 'Guild with name <b>'.$guild_name.'</b> doesn\'t exist.';
		}
	}
	if(empty($guild_errors)) {
		if(isset($_POST['todo']) && $_POST['todo'] == 'save') {
			if(!check_name($pass_to)) {
				$guild_errors2[] = 'Invalid player name format.';
			}
			
			if(empty($guild_errors2)) {
				$to_player = new OTS_Player();
				$to_player->find($pass_to);
				if(!$to_player->isLoaded()) {
					$guild_errors2[] = 'Player with name <b>'.$pass_to.'</b> doesn\'t exist.';
				}
				
				if(empty($guild_errors2)) {
					$to_player_rank = $to_player->getRank();
					if($to_player_rank->isLoaded()) {
						$to_player_guild = $to_player_rank->getGuild();
						if($to_player_guild->getId() != $guild->getId()) {
							$guild_errors2[] = 'Player with name <b>'.$to_player->getName().'</b> isn\'t from your guild.';
						}
					}
					else {
						$guild_errors2[] = 'Player with name <b>'.$to_player->getName().'</b> isn\'t from your guild.';
					}
				}
			}
		}
	}
	if(empty($guild_errors) && empty($guild_errors2)) {
		if($logged) {
			$guild_leader_char = $guild->getOwner();
			$guild_leader = false;
			$account_players = $account_logged->getPlayers();
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
					echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Guild Deleted</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td><b>'.$to_player->getName().'</b> is now a Leader of <b>'.$guild_name.'</b>.</td></tr>          </table>        </div>  </table></div></td></tr>';
					echo '<br/><center><form action="?subtopic=guilds&guild='.$guild->getName().'&action=show" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
				}
				else {
					echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Guild Deleted</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td>Pass leadership to: </b><br>
					<form action="?subtopic=guilds&guild='.$guild->getName().'&action=passleadership" METHOD=post><input type="hidden" name="todo" value="save"><input type="text" size="40" name="player"><input type="submit" value="Save"></form>
					</td></tr>          </table>        </div>  </table></div></td></tr>';
					echo '<br/><center><form action="?subtopic=guilds&guild='.$guild->getName().'&action=manager" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
				}
			}
			else {
				$guild_errors[] = 'You are not a leader of guild!';
			}
		}
		else {
			$guild_errors[] = 'You are not logged. You can\'t manage guild.';
		}
	}
	if(empty($guild_errors) && !empty($guild_errors2)) {
		echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
		foreach($guild_errors2 as $guild_error2) {
			echo '<li>'.$guild_error2;
		}
		echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br>';
		echo '<br/><center><form action="?subtopic=guilds&guild='.$guild->getName().'&action=passleadership" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
	}
	if(!empty($guild_errors)) {
		echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
		foreach($guild_errors as $guild_error) {
			echo '<li>'.$guild_error . '</li>';
		}
		if(!empty($guild_errors2)) {
			foreach($guild_errors2 as $guild_error2) {
				echo '<li>'.$guild_error2 . '</li>';
			}
		}
		echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br>';
		echo '<br/><center><form action="?subtopic=guilds" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
	}
}
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
if($action == 'deleteguild') {
$guild_name = $_REQUEST['guild'];
if(!check_guild_name($guild_name)) {
$guild_errors[] = 'Invalid guild name format.';
}
if(empty($guild_errors)) {
$guild = $ots->createObject('Guild');
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
if($guild->getOwner()->getId() == $player->getId()) {
$guild_vice = true;
$guild_leader = true;
$level_in_guild = 3;
}
}
if($guild_leader) {
	$saved = false;
if(isset($_POST['todo']) && $_POST['todo'] == 'save') {
delete_guild($guild->getId());
$saved = true;
}
if($saved) {
echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Guild Deleted</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td>Guild with name <b>'.$guild_name.'</b> has been deleted.</td></tr>          </table>        </div>  </table></div></td></tr>';
echo '<br/><center><form action="?subtopic=guilds" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
}
else
{
echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Guild Deleted</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td>Are you sure you want delete guild <b>'.$guild_name.'</b>?<br>
<form action="?subtopic=guilds&guild='.$guild->getName().'&action=deleteguild" METHOD=post><input type="hidden" name="todo" value="save"><input type="submit" value="Yes, delete"></form>
</td></tr>          </table>        </div>  </table></div></td></tr>';
echo '<br/><center><form action="?subtopic=guilds&guild='.$guild->getName().'&action=manager" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
}
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
echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
foreach($guild_errors as $guild_error) {
	echo '<li>'.$guild_error;
}
echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br>';
echo '<br/><center><form action="?subtopic=guilds" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
}
}


//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
if($action == 'deletebyadmin') {
$guild_name = $_REQUEST['guild'];
if(!check_guild_name($guild_name)) {
$guild_errors[] = 'Invalid guild name format.';
}
if(empty($guild_errors)) {
$guild = $ots->createObject('Guild');
$guild->find($guild_name);
if(!$guild->isLoaded()) {
$guild_errors[] = 'Guild with name <b>'.$guild_name.'</b> doesn\'t exist.';
}
}
if(empty($guild_errors)) {
if($logged) {
if(admin()) {
	$saved = false;
	if(isset($_POST['todo']) && $_POST['todo'] == 'save') {
	delete_guild($guild->getId());
	$saved = true;
	}
	if($saved) {
	echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Guild Deleted</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td>Guild with name <b>'.$guild_name.'</b> has been deleted.</td></tr>          </table>        </div>  </table></div></td></tr>';
	echo '<br/><center><form action="?subtopic=guilds" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
	}
	else
	{
	echo '<div class="TableContainer" >  <table class="Table1" cellpadding="0" cellspacing="0" >    <div class="CaptionContainer" >      <div class="CaptionInnerContainer" >        <span class="CaptionEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionBorderTop" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <div class="Text" >Guild Deleted</div>        <span class="CaptionVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></span>        <span class="CaptionBorderBottom" style="background-image:url('.$template_path.'/images/content/table-headline-border.gif);" ></span>        <span class="CaptionEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>        <span class="CaptionEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></span>      </div>    </div>    <tr>      <td>        <div class="InnerTableContainer" >          <table style="width:100%;" ><tr><td>Are you sure you want delete guild <b>'.$guild_name.'</b>?<br>
	<form action="?subtopic=guilds&guild='.$guild->getName().'&action=deletebyadmin" METHOD=post><input type="hidden" name="todo" value="save"><input type="submit" value="Yes, delete"></form>
	</td></tr>          </table>        </div>  </table></div></td></tr>';
	echo '<br/><center><form action="?subtopic=guilds" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
	}
}
else
{
$guild_errors[] = 'You are not an admin!';
}
}
else
{
$guild_errors[] = 'You are not logged. You can\'t delete guild.';
}
}
if(!empty($guild_errors)) {
echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
foreach($guild_errors as $guild_error) {
	echo '<li>'.$guild_error;
}
echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br>';
echo '<br/><center><form action="?subtopic=guilds" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
}
}

//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
if($action == 'changemotd') {
	$guild_name = $_REQUEST['guild'];
	if(!check_guild_name($guild_name)) {
		$guild_errors[] = 'Invalid guild name format.';
	}
	
	if(empty($guild_errors)) {
		$guild = $ots->createObject('Guild');
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
				if($guild->getOwner()->getId() == $player->getId()) {
					$guild_vice = true;
					$guild_leader = true;
					$level_in_guild = 3;
				}
			}
			
			$saved = false;
			if($guild_leader) {
				if(isset($_REQUEST['todo']) && $_REQUEST['todo'] == 'save') {
					$motd = htmlspecialchars(stripslashes(substr(trim($_REQUEST['motd']),0,$config['guild_motd_chars_limit'])));
					$guild->setCustomField('motd', $motd);
					$saved = true;
				}
				echo '<center><h2>Change guild MOTD</h2></center>';
				if($saved) {
					echo '<center><font color="red" size="3"><b>CHANGES HAS BEEN SAVED!</b></font></center><br>';
				}
				echo 'Here you can change MOTD (Message of the Day, showed in game!) of your guild.<BR>';
				echo '<form enctype="multipart/form-data" action="?subtopic=guilds&guild='.$guild->getName().'&action=changemotd" method="POST">
				<input type="hidden" name="todo" value="save" />
					<textarea name="motd" cols="60" rows="3">'.$guild->getCustomField('motd').'</textarea><br>
					(max. '.$config['guild_motd_chars_limit'].' chars) <input type="submit" value="Save MOTD" /></form><br>';
				echo '<br/><center><form action="?subtopic=guilds&guild='.$guild->getName().'&action=manager" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
			}
			else {
				$guild_errors[] = 'You are not a leader of guild!';
			}
		}
		else {
			$guild_errors[] = 'You are not logged. You can\'t manage guild.';
		}
	}
	if(!empty($guild_errors)) {
	echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
	foreach($guild_errors as $guild_error) {
		echo '<li>'.$guild_error;
	}
	echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br>';
	echo '<br/><center><form action="?subtopic=guilds" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
	}
}

//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
if($action == 'saveranks') {
$guild_name = $_REQUEST['guild'];
if(!check_guild_name($guild_name)) {
$guild_errors[] = 'Invalid guild name format.';
}
if(empty($guild_errors)) {
$guild = $ots->createObject('Guild');
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
foreach($rank_list as $rank) {
$rank_id = $rank->getId();
$name = $_REQUEST[$rank_id.'_name'];
$level = (int) $_REQUEST[$rank_id.'_level'];
if(check_rank_name($name)) {
$rank->setName($name);
}
else
{
$ranks_errors[] = 'Invalid rank name. Please use only a-Z, 0-9 and spaces. Rank ID <b>'.$rank_id.'</b>.';
}
if($level > 0 && $level < 4) {
$rank->setLevel($level);
}
else
{
$ranks_errors[] = 'Invalid rank level. Contact with admin. Rank ID <b>'.$rank_id.'</b>.';
}
$rank->save();
}
//show errors or redirect
if(!empty($ranks_errors)) {
echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
foreach($ranks_errors as $guild_error) {
	echo '<li>'.$guild_error;
}
echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br>';
}
else
{
header("Location: ?subtopic=guilds&action=manager&guild=".$guild->getName());
}
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
echo '<div class="SmallBox" >  <div class="MessageContainer" >    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeLeftTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeRightTop" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="ErrorMessage" >      <div class="BoxFrameVerticalLeft" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="BoxFrameVerticalRight" style="background-image:url('.$template_path.'/images/content/box-frame-vertical.gif);" /></div>      <div class="AttentionSign" style="background-image:url('.$template_path.'/images/content/attentionsign.gif);" /></div><b>The Following Errors Have Occurred:</b><br/>';
foreach($guild_errors as $guild_error) {
	echo '<li>'.$guild_error;
}
echo '</div>    <div class="BoxFrameHorizontal" style="background-image:url('.$template_path.'/images/content/box-frame-horizontal.gif);" /></div>    <div class="BoxFrameEdgeRightBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>    <div class="BoxFrameEdgeLeftBottom" style="background-image:url('.$template_path.'/images/content/box-frame-edge.gif);" /></div>  </div></div><br>';
}
}
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
if($action == 'cleanup_players')
{
	if($logged)
	{
		if(admin())
		{
			$players_list = new OTS_Players_List();
			$players_list->init();
		}
		else
			$players_list = $account_logged->getPlayersList();
		if(count($players_list) > 0)
		{
			foreach($players_list as $player)
			{
				$player_rank = $player->getRank();
				if($player_rank->isLoaded())
				{
					if($player_rank->isLoaded())
					{
						$rank_guild = $player_rank->getGuild();
						if(!$rank_guild->isLoaded())
						{
							$player->setRank();
							$player->setGuildNick();
							$changed_ranks_of[] = $player->getName();
							$deleted_ranks[] = 'ID: '.$player_rank->getId().' - '.$player_rank->getName();
							$player_rank->delete();
						}
					}
					else
					{
						$player->setRank();
						$player->setGuildNick('');
						$changed_ranks_of[] = $player->getName();
					}

				}
			}
			echo "<b>Deleted ranks (this ranks guilds doesn't exist [bug fix]):</b>";
			if(!empty($deleted_ranks))
				foreach($deleted_ranks as $rank)
					echo "<li>".$rank;
			echo "<BR /><BR /><b>Changed ranks of players (rank or guild of rank doesn't exist [bug fix]):</b>";
			if(!empty($changed_ranks_of))
				foreach($changed_ranks_of as $name)
					echo "<li>".$name;
		}
		else
			echo "0 players found.";
	}
	else
		echo "You are not logged in.";
	echo "<center><h3><a href=\"?subtopic=guilds\">BACK</a></h3></center>";
}
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
if($action == 'cleanup_guilds')
{
    if($logged)
    {
        $guilds_list = new OTS_Guilds_List();
        $guilds_list->init();
        if(count($guilds_list) > 0)
        {
            foreach($guilds_list as $guild)
            {
                $error = 0;
                $leader = $guild->getOwner();
                if($leader->isLoaded())
                {
                    $leader_rank = $leader->getRank();
                    if($leader_rank->isLoaded())
                    {
                        if($leader_rank->isLoaded())
                        {
                            $leader_guild = $leader_rank->getGuild();
                            if($leader_guild->isLoaded())
                            {
                                if($leader_guild->getId() != $guild->getId())
                                    $error = 1;
                            }
                            else
                                $error = 1;
                        }
                        else
                            $error = 1;
                    }
                    else
                        $error = 1;
                }
                else
                    $error = 1;
                if($error == 1)
                {
                    $deleted_guilds[] = $guild->getName();
                    $status = delete_guild($guild->getId());
                }
            }
            echo "<b>Deleted guilds (leaders of this guilds are not members of this guild [fix bugged guilds]):</b>";
            if(!empty($deleted_guilds))
                foreach($deleted_guilds as $guild)
                    echo "<li>".$guild;
        }
        else
            echo "0 guilds found.";
    }
    else
        echo "You are not logged in.";
    echo "<center><h3><a href=\"?subtopic=guilds\">BACK</a></h3></center>";
}
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------//-----------------------------------------------------------------------------
	if($action == 'change_nick')
	{
		if($logged)
		{
			$player_n = stripslashes($_REQUEST['name']);
			$new_nick = stripslashes($_REQUEST['nick']);
			$player = new OTS_Player();
			$player->find($player_n);
			$player_from_account = false;
			if(strlen($new_nick) <= 40)
			{
				if($player->isLoaded())
				{
					$account_players = $account_logged->getPlayersList();
					if(count($account_players))
					{
						foreach($account_players as $acc_player)
						{
							if($acc_player->getId() == $player->getId())
								$player_from_account = true;
						}
						if($player_from_account)
						{
							$player->setGuildNick($new_nick);
							echo 'Guild nick of player <b>'.$player->getName().'</b> changed to <b>'.htmlentities($new_nick).'</b>.';
							$addtolink = '&action=show&guild='.$player->getRank()->getGuild()->getName();
						}
						else
							echo 'This player is not from your account.';
					}
					else
						echo 'This player is not from your account.';
				}
				else
					echo 'Unknow error occured.';
			}
			else
				echo 'Too long guild nick. Max. 30 chars, your: '.strlen($new_nick);
		}
			else
				echo 'You are not logged.';
		echo '<center><h3><a href="?subtopic=guilds'.$addtolink.'">BACK</a></h3></center>';
	}
?>
