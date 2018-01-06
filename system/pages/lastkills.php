<?php
/**
 * Last kills
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Last Kills';

$players_deaths_count = 0;
$players_rows = '';
if($db->hasTable('player_killers')) // tfs 0.3
{
	$players_deaths = $db->query('SELECT `player_deaths`.`id`, `player_deaths`.`date`, `player_deaths`.`level`, `players`.`name`' . ($db->hasColumn('players', 'world_id') ? ', `players`.`world_id`' : '') . ' FROM `player_deaths` LEFT JOIN `players` ON `player_deaths`.`player_id` = `players`.`id` ORDER BY `date` DESC LIMIT 0, ' . $config['last_kills_limit']);
	if(!empty($players_deaths))
	{
		foreach($players_deaths as $death)
		{
			$players_rows .= '<TR BGCOLOR="' . getStyle($players_deaths_count++) . '"><TD WIDTH="30"><center>'.$players_deaths_count.'.</center></TD><TD WIDTH="125"><small>'.date("j.m.Y, G:i:s",$death['date']).'</small></TD><TD>' . getPlayerLink($death['name']). ' ';
			$killers = $db->query("SELECT environment_killers.name AS monster_name, players.name AS player_name, players.deleted AS player_exists
	FROM killers LEFT JOIN environment_killers ON killers.id = environment_killers.kill_id
	LEFT JOIN player_killers ON killers.id = player_killers.kill_id LEFT JOIN players ON players.id = player_killers.player_id
	WHERE killers.death_id = '".$death['id']."' ORDER BY killers.final_hit DESC, killers.id ASC")->fetchAll();

			$i = 0;
			$count = count($killers);
			foreach($killers as $killer)
			{
				$i++;
				if($killer['player_name'] != "")
				{
					if($i == 1)
					{
						if($count <= 4)
							$players_rows .= 'killed';
						elseif($count > 4 and $count < 10)
							$players_rows .= 'slain';
						elseif($count > 9 and $count < 15)
							$players_rows .= 'crushed';
						elseif($count > 14 and $count < 20)
							$players_rows .= 'eliminated';
						elseif($count > 19)
							$players_rows .= 'annihilated';
							
						 $players_rows .= 'at level <b>' . $death['level'] . '</b> by ';
					}
					else if($i == $count)
						$players_rows .= ' and';
					else
						$players_rows .= ',';

					$players_rows .= ' by ';
					if($killer['monster_name'] != '')
						$players_rows .= $killer['monster_name'] . ' summoned by ';

					if($killer['player_exists'] == 0)
						$players_rows .= getPlayerLink($killer['player_name']);
				}
				else
				{
					if($i == 1)
						$players_rows .= 'died at level <b>' . $death['level'] . '</b>';
					else if($i == $count)
						$players_rows .= ' and';
					else
						$players_rows .= ',';

					$players_rows .= ' by ' . $killer['monster_name'];
				}
			}

			$players_rows .= '.</TD>';
			if($config['multiworld'])
				$player_rows .= '<TD>'.$config['worlds'][(int)$death['world_id']].'</TD>';
			
			$players_rows .= '</TR>';
		}
	}
}
else {
	//$players_deaths = $db->query("SELECT `p`.`name` AS `victim`, `player_deaths`.`killed_by` as `killed_by`, `player_deaths`.`time` as `time`, `player_deaths`.`is_player` as `is_player`, `player_deaths`.`level` as `level` FROM `player_deaths`, `players` as `d` INNER JOIN `players` as `p` ON player_deaths.player_id = p.id WHERE player_deaths.`is_player`='1' ORDER BY `time` DESC LIMIT " . $config['last_kills_limit'] . ";");
	
$players_deaths = $db->query("SELECT `p`.`name` AS `victim`, `d`.`killed_by` as `killed_by`, `d`.`time` as `time`, `d`.`level`, `d`.`is_player` FROM `player_deaths` as `d` INNER JOIN `players` as `p` ON d.player_id = p.id ORDER BY `time` DESC LIMIT 20;");

	if(!empty($players_deaths))
	{
		foreach($players_deaths as $death)
		{
			$players_rows .= '<TR BGCOLOR="' . getStyle($players_deaths_count++) . '"><TD WIDTH="30"><center>'.$players_deaths_count.'.</center></TD><TD WIDTH="125"><small>'.date("j.m.Y, G:i:s",$death['time']).'</small></TD><TD>' . getPlayerLink($death['victim']). ' died at level ' . $death['level'] . ' by ';
			if($death['is_player'] == '1')
				$players_rows .= getPlayerLink($death['killed_by']);
			else
				$players_rows .= $death['killed_by'];
			
			$players_rows .= '.</TR>';
		}
	}
}

if($players_deaths_count == 0)
	echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR="'.$config['vdarkborder'].'"><TD class="white"><B>Last Deaths</B></TD></TR><TR BGCOLOR='.$config['darkborder'].'><TD><TABLE BORDER=0 CELLSPACING=1 CELLPADDING=1><TR><TD>No one died on '.$config['lua']['serverName'].'.</TD></TR></TABLE></TD></TR></TABLE><BR>';
else
	echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR="'.$config['vdarkborder'].'"><TD class="white"><B>Last Deaths</B></TD></TR></TABLE><TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%>'.$players_rows.'</TABLE>';
?>
