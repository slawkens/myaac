<?php
/**
 * Delete rank
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
$rank_to_delete = (int) $_REQUEST['rankid'];
if(!Validator::guildName($guild_name)) {
	$guild_errors[] = Validator::getLastError();
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
				echo $twig->render('error_box.html.twig', array('errors' => $guild_errors2));
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
	echo $twig->render('error_box.html.twig', array('errors' => $guild_errors));
	
	echo '<br/><center><form action="?subtopic=guilds" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
}

?>