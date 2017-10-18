<?php
/**
 * Leave guild
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
	$errors[] = 'You are not logged in. You can\'t leave guild.';
}

if(!Validator::guildName($guild_name)) {
	$errors[] = Validator::getLastError();
}

if(empty($errors)) {
	$guild = new OTS_Guild();
	$guild->find($guild_name);
	if(!$guild->isLoaded()) {
		$errors[] = 'Guild with name <b>'.$guild_name.'</b> doesn\'t exist.';
	}
}

$array_of_player_ig = array();
if(empty($errors)) {
	$guild_owner_name = $guild->getOwner()->getName();
	if(isset($_REQUEST['todo']) && $_REQUEST['todo'] == 'save') {
		if(!Validator::characterName($name)) {
			$errors[] = 'Invalid name format.';
		}
		
		if(empty($errors)) {
			$player = new OTS_Player();
			$player->find($name);
			if(!$player->isLoaded()) {
				$errors[] = 'Character <b>'.$name.'</b> doesn\'t exist.';
			}
			else {
				if($player->getAccount()->getId() != $account_logged->getId()) {
					$errors[] = 'Character <b>'.$name.'</b> isn\'t from your account!';
				}
			}
		}
		
		if(empty($errors)) {
			$player_loaded_rank = $player->getRank();
			if($player_loaded_rank->isLoaded()) {
				if($player_loaded_rank->getGuild()->getName() != $guild->getName()) {
					$errors[] = 'Character <b>'.$name.'</b> isn\'t from guild <b>'.$guild->getName().'</b>.';
				}
			}
			else {
				$errors[] = 'Character <b>'.$name.'</b> isn\'t in any guild.';
			}
		}
		
		if(empty($errors)) {
			if($guild_owner_name == $player->getName()) {
				$errors[] = 'You can\'t leave guild. You are an owner of guild.';
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

if(!empty($errors)) {
	echo $twig->render('error_box.html.twig', array('errors' => $errors));
	echo '
<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><FORM ACTION="?subtopic=guilds&action=show&guild='.$guild_name.'" METHOD=post><TR><TD><center><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></center></TD></TR></FORM></TABLE>';
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
				<form action="?subtopic=guilds&action=leave_guild&guild='.$guild_name.'&todo=save" METHOD="post">';
			
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

?>