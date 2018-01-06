<?php

$guilds_list = new OTS_Guilds_List();

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
				echo '<br /><a href="?subtopic=guilds&action=delete_by_admin&guild='.$guild->getName().'">Delete this guild (for ADMIN only!)</a>';
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
					<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="?subtopic=guilds&action=create" METHOD=post><TR><TD>
					<INPUT TYPE=image NAME="Create Guild" ALT="Create Guild" SRC="'.$template_path.'/images/global/buttons/sbutton_createguild.png" BORDER=0 WIDTH=120 HEIGHT=18>
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

			echo '<TR BGCOLOR="' . getStyle($showed_guilds++) . '"><TD><IMG SRC="images/guilds/' . $guild_logo.'" WIDTH=64 HEIGHT=64></TD>
					<TD valign="top"><B>'.$guild->getName().'</B><BR/>'.$description.'';
			if(admin())
				echo '<br /><a href="?subtopic=guilds&action=delete_by_admin&guild='.$guild->getName().'">Delete this guild (for ADMIN only!)</a>';
			echo '</TD><TD><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="' . getGuildLink($guild->getName(), false) . '" METHOD=post><TR><TD>
					<INPUT TYPE=image NAME="View" ALT="View" SRC="'.$template_path.'/images/global/buttons/sbutton_view.gif" BORDER=0 WIDTH=120 HEIGHT=18>
					</TD></TR></FORM></TABLE>
					</TD></TR>';
		}
	}
	else
		echo '<TR BGCOLOR='.$config['lightborder'].'><TD><IMG SRC="images/guilds/' . 'default.gif" WIDTH=64 HEIGHT=64></TD>
					<TD valign="top"><B>Create guild</B><BR/>Actually there is no guild on server.' . ($logged ? ' Create first! Press button "Create Guild".' : '') . '</TD>
					<TD>';
	if($logged)
		echo '<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="?subtopic=guilds&action=create" METHOD=post><TR><TD>
					<INPUT TYPE=image NAME="Create Guild" ALT="Create Guild" SRC="'.$template_path.'/images/global/buttons/sbutton_createguild.png" BORDER=0 WIDTH=120 HEIGHT=18>
					</TD></TR></FORM></TABLE>';
	echo '
					</TD></TR>';
}


echo '</TABLE><br><br>';
if($logged)
	echo '<TABLE BORDER=0 WIDTH=100%><TR><TD ALIGN=center><IMG SRC="'.$template_path.'/images/general/blank.gif" WIDTH=80 HEIGHT=1 BORDER=0<BR></TD><TD ALIGN=center><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="?subtopic=guilds&action=create" METHOD=post><TR><TD>
		<INPUT TYPE=image NAME="Create Guild" ALT="Create Guild" SRC="'.$template_path.'/images/global/buttons/sbutton_createguild.png" BORDER=0 WIDTH=120 HEIGHT=18>
		</TD></TR></FORM></TABLE></TD><TD ALIGN=center><IMG SRC="'.$template_path.'/images/general/blank.gif" WIDTH=80 HEIGHT=1 BORDER=0<BR></TD></TR></TABLE>
		<BR />If you have any problem with guilds try:
		<BR /><a href="?subtopic=guilds&action=cleanup_players">Cleanup players</a> - can\'t join guild/be invited? Can\'t create guild? Try cleanup players.
		<BR /><a href="?subtopic=guilds&action=cleanup_guilds">Cleanup guilds</a> - made guild, you are a leader, but you are not on players list? Cleanup guilds!';
else
	echo 'Before you can create guild you must login.<br><TABLE BORDER=0 WIDTH=100%><TR><TD ALIGN=center><IMG SRC="'.$template_path.'/images/general/blank.gif" WIDTH=80 HEIGHT=1 BORDER=0<BR></TD><TD ALIGN=center><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="?subtopic=accountmanagement&redirect=' . getLink('guilds') . '" METHOD=post><TR><TD>
		<INPUT TYPE=image NAME="Login" ALT="Login" SRC="'.$template_path.'/images/global/buttons/sbutton_login.gif" BORDER=0 WIDTH=120 HEIGHT=18>
		</TD></TR></FORM></TABLE></TD><TD ALIGN=center><IMG SRC="'.$template_path.'/images/general/blank.gif" WIDTH=80 HEIGHT=1 BORDER=0<BR></TD></TR></TABLE>';
?>