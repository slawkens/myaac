<?php
/**
 * Accept invite
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
$guild_name = isset($_REQUEST['guild']) ? $_REQUEST['guild'] : NULL;
$name = isset($_REQUEST['name']) ? stripslashes($_REQUEST['name']) : NULL;
if(!$logged) {
	$errors[] = 'You are not logged in. You can\'t accept invitations.';
}
if(!Validator::guildName($guild_name)) {
	$errors[] = Validator::getLastError();
}
if(empty($errors)) {
	$guild = $ots->createObject('Guild');
	$guild->find($guild_name);
	if(!$guild->isLoaded()) {
		$errors[] = 'Guild with name <b>'.$guild_name.'</b> doesn\'t exist.';
	}
}

if(isset($_REQUEST['todo']) && $_REQUEST['todo'] == 'save') {
	if(!Validator::characterName($name)) {
		$errors[] = 'Invalid name format.';
	}
	
	if(empty($errors)) {
		$player = new OTS_Player();
		$player->find($name);
		if(!$player->isLoaded()) {
			$errors[] = 'Player with name <b>'.$name.'</b> doesn\'t exist.';
		}
		else
		{
			$rank_of_player = $player->getRank();
			if($rank_of_player->isLoaded()) {
				$errors[] = 'Character with name <b>'.$name.'</b> is already in guild. You must leave guild before you join other guild.';
			}
		}
	}
}

if(isset($_REQUEST['todo']) && $_REQUEST['todo'] == 'save') {
	if(empty($errors)) {
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
			$errors[] = 'Character '.$player->getName.' isn\'t invited to guild <b>'.$guild->getName().'</b>.';
		}
	}
}
else
{
	if(empty($errors)) {
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
		$errors[] = 'Any character from your account isn\'t invited to <b>'.$guild->getName().'</b>.';
	}
}
if(!empty($errors)) {
	echo $twig->render('error_box.html.twig', array('errors' => $errors));
	echo '
<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TR><TD><center><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></center></TD></TR></FORM></TABLE>';
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
		<form action="?subtopic=guilds&action=accept_invite&guild='.$guild_name.'&todo=save" METHOD="post">';
		sort($list_of_invited_players);
		$i = 0;
		foreach($list_of_invited_players as $invited_player_from_list) {
			echo '<input type="radio" name="name" id="name_' . $i . '" value="'.$invited_player_from_list.'" /><label for="name_' . $i++ . '">'.$invited_player_from_list.'</label><br>';
		}
		echo '<br><input type="image" name="Submit" alt="Submit" SRC="'.$template_path.'/images/buttons/sbutton_submit.gif" border="0" width="120" height="18"></form></td></tr></table><br/><center><table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><form action="?subtopic=guilds&action=show&guild='.$guild_name.'" method="post"><td><input type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/sbutton_back.gif" border=0 width=120 height=18></td></tr></form></table></center>';
	}
}

?>