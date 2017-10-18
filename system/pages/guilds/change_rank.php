<?php
/**
 * Change rank
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.1
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

$guild_name = $_REQUEST['guild'];
if(!Validator::guildName($guild_name))
	$guild_errors[] = Validator::getLastError();
if(!$logged)
	$guild_errors[] = 'You are not logged in. You can\'t change rank.';
if(empty($guild_errors))
{
	$guild = $ots->createObject('Guild');
	$guild->find($guild_name);
	if(!$guild->isLoaded())
		$guild_errors[] = 'Guild with name <b>' . $guild_name . '</b> doesn\'t exist.';
}
if(!empty($guild_errors))
{
	echo $twig->render('error_box.html.twig', array('errors' => $guild_errors));
	echo '
<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH=100%><FORM ACTION="?subtopic=guilds" METHOD=post><TR><TD><center><INPUT TYPE=image NAME="Back" ALT="Back" SRC="'.$template_path.'/images/buttons/sbutton_back.gif" BORDER=0 WIDTH=120 HEIGHT=18></center></TD></TR></FORM></TABLE>';
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
			if(!Validator::characterName($player_name))
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
				echo $twig->render('error_box.html.twig', array('errors' => $change_errors));
			}
		}
		echo '<FORM ACTION="?subtopic=guilds&action=change_rank&guild='.$guild->getName().'&todo=save" METHOD=post>
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