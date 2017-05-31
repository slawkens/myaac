<?php
/**
 * Characters
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.2.3
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Characters';

require(SYSTEM . 'item.php');
$groups = new OTS_Groups_List();
function generate_search_table($script = false)
{
	global $config, $template_path;
	$ret = '
	<form action="' . getPageLink('characters') . '" method="post">
		<table width="100%" border="0" cellspacing="1" cellpadding="4">
			<TR><TD BGCOLOR="'.$config['vdarkborder'].'" class="white"><B>Search Character</B></TD></TR>
			<TR><TD BGCOLOR="'.$config['darkborder'].'">
				<TABLE BORDER=0 CELLPADDING=1>
					<TR>
						<TD>Name:</TD><TD><INPUT ID="name-input" NAME="name" VALUE="" SIZE=29 MAXLENGTH=29></TD>
						<TD>
							<INPUT TYPE=image NAME="Submit" SRC="' . $template_path . '/images/buttons/sbutton_submit.gif" BORDER=0 WIDTH=120 HEIGHT=18>
						</TD>
					</TR>
				</TABLE>
			</TD></TR>
		</TABLE>
	</FORM>';
		if($script)
			$ret .= '
			<script type="text/javascript">
			$(function() {
				$(\'#name-input\').focus();
			});
			</script>';

	return $ret;
}

function generate_player_lookup($player)
{
	global $db;
	$eq_sql = $db->query('SELECT `pid`, `itemtype` FROM player_items WHERE player_id = '.$player->getId().' AND (`pid` >= 1 and `pid` <= 10)');
	$player_eq = array();
	foreach($eq_sql as $eq)
		$player_eq[$eq['pid']] = $eq['itemtype'];

	$empty_slots = array("", "no_helmet", "no_necklace", "no_backpack", "no_armor", "no_handleft", "no_handright", "no_legs", "no_boots", "no_ring", "no_ammo");
	for($i = 0; $i <= 10; $i++)
	{
		if(!isset($player_eq[$i]) || $player_eq[$i] == 0)
			$player_eq[$i] = $empty_slots[$i];
	}
/*
	if(PHP_VERSION_ID == NULL || PHP_VERSION_ID < 70000) {
		for($i = 1; $i < 11; $i++)
		{
			if(!itemImageExists($player_eq[$i]))
				Items::generate($player_eq[$i]);
		}
	}
	*/
	for($i = 1; $i < 11; $i++)
	{
		if(check_number($player_eq[$i]))
			$player_eq[$i] = getItemImage($player_eq[$i]);
		else
			$player_eq[$i] = '<img src="images/items/' . $player_eq[$i] . '.gif" width="32" height="32" border="0" alt=" ' . $player_eq[$i] . '" />';
	}

	$skulls = array(
		1 => 'yellow_skull',
		2 => 'green_skull',
		3 => 'white_skull',
		4 => 'red_skull',
		5 => 'black_skull'
	);

	return '<table width="100" align="center" cellspacing="0" cellpadding="0" style="background: #808080; border:1px solid #808080;">
		<tr>
			<td>
				<table cellspacing="0" style="background: #292929;">
<tr><td style="border:1px solid #808080;">'.$player_eq[2].'</td></tr><tr><td style="border:1px solid #808080;">'.$player_eq[6].'</td></tr><tr><td style="border:1px solid #808080;">'.$player_eq[9].'</td></tr>
				<tr height="11px"><td>'.($player->getSkullTime() > 0 && ($player->getSkull() == 4 || $player->getSkull() == 5) ? '<img src="images/' . $skulls[$player->getSkull()] . '.gif">' : '').'</td></tr>
				</table>
			</td>
			<td>
				<table cellspacing="0" style="background: #292929;">
<tr><td style="border:1px solid #808080;">'.$player_eq[1].'</td></tr><tr><td style="border:1px solid #808080;">'.$player_eq[4].'</td></tr><tr><td style="border:1px solid #808080;">'.$player_eq[7].'</td></tr><tr><td style="border:1px solid #808080;">'.$player_eq[8].'</td></tr>
				</table>
			</td>
			<td>
				<table cellspacing="0" style="background: #292929;">
<tr><td style="border:1px solid #808080;">'.$player_eq[3].'</td></tr><tr><td style="border:1px solid #808080;">'.$player_eq[5].'</td></tr><tr><td style="border:1px solid #808080;">'.$player_eq[10].'</td></tr>
				</table>
			</td>
		</tr>
		</table>';
}

function retrieve_former_name($name)
{
	global $oldName, $db;

	if(tableExist('player_namelocks') && fieldExist('name', 'player_namelocks')) {
		$newNameSql = $db->query('SELECT `name`, `new_name` FROM `player_namelocks` WHERE `name` = ' . $db->quote($name));
		if($newNameSql->rowCount() > 0) // namelocked
		{
			$newNameSql = $newNameSql->fetch();
			$oldName = ' (<small><b>Former name:</b> ' . $newNameSql['name'] . '</small>)';
			return $newNameSql['new_name'];
		}
	}

	return "";
}

$name = '';
if(isset($_REQUEST['name']))
	$name = stripslashes(ucwords(strtolower(trim($_REQUEST['name']))));

if(empty($name))
{
	$tmp_link = getPlayerLink($name);
	echo 'Here you can get detailed information about a certain player on '.$config['lua']['serverName'].'.<BR>';
	echo generate_search_table(true);
	return;
}

$name = str_replace('/', '', $name);

$oldName = '';

$player = $ots->createObject('Player');
$player->find($name);
if(!$player->isLoaded())
{
	$tmp_zmienna = "";
	$tmp_name = retrieve_former_name($name);
	while(!empty($tmp_name))
	{
		$tmp_zmienna = $tmp_name;
		$tmp_name = retrieve_former_name($tmp_zmienna);
	}

	if(!empty($tmp_zmienna))
		$player->find($tmp_zmienna);
}

if($player->isLoaded() && !$player->isDeleted())
{
	$title = $player->getName() . ' - ' . $title;
	$account = $player->getAccount();
	$rows = 0;
?>
	<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr>
		<td><img src="<?php echo $template_path; ?>/images/general/blank.gif" width="10" height="1" border="0"></td>
		<td>
			<table border="0" cellspacing="1" cellpadding="4" width="100%">
				<?php if($config['characters']['outfit']): ?>
				<div style="width:64px;height:64px;border:2px solid #F1E0C6; border-radius:50px; padding:13px; margin-top:38px;margin-left:376px;position:absolute;"><img style="margin-left:<?php echo (in_array($player->getLookType(), array(75, 266, 302)) ? '-0px;margin-top:-0px;width:64px;height:64px;' : '-60px;margin-top:-60px;width:128px;height:128px;'); ?>" src="<?php echo $config['outfit_images_url'] . '?id=' . $player->getLookType() . (fieldExist('lookaddons', 'players') ? '&addons=' . $player->getLookAddons() : '') . '&head=' . $player->getLookHead() . '&body=' . $player->getLookBody() . '&legs=' . $player->getLookLegs() . '&feet=' . $player->getLookFeet() . '"';?>></div>
				<?php endif; ?>
				<tr bgcolor="<?php echo $config['vdarkborder']; ?>">
					<td colspan="2" class="white"><b>Character Information</b></td>
				</tr>

				<?php
				$flag = '';
				if($config['account_country'])
					$flag = getFlagImage($account->getCustomField('country'));

				echo
				'<TR BGCOLOR="' . getStyle(++$rows) . '">'.
					'<TD WIDTH=20%>Name:</TD>
					<TD>' . $flag . ' <font color="'.($player->isOnline() ? 'green' : 'red').'"><b>'.$player->getName().'</b></font>'.$oldName.
				'</TD></TR>';

				echo
				'<TR BGCOLOR="' . getStyle(++$rows) . '"><TD>Sex:</TD><TD>'.
				($player->getSex() == 0 ? 'female' : 'male').
				'</TD></TR>';

			if($config['characters']['marriage_info'] && fieldExist('marriage', 'players'))
			{
				echo
					'<TR BGCOLOR="' . getStyle(++$rows) . '"><TD>Marital status:</TD><TD>';
				$marriage = new OTS_Player();
				$marriage->load($player->getMarriage());
				if($marriage->isLoaded())
					echo 'married to ' . getPlayerLink($marriage->getName());
				else
					echo 'single';
				echo
					'</TD></TR>';
			}

			echo
				'<TR BGCOLOR="' . getStyle(++$rows). '">'.
					'<TD>Profession:</TD><TD>' . $config['vocations'][$player->getVocation()] . '</TD>'.
				'</TR>';

			if($config['characters']['level'])
				echo '<TR BGCOLOR="' . getStyle(++$rows) . '"><TD>Level:</TD><TD>'.$player->getLevel().'</TD></TR>';

			if($config['characters']['experience'])
				echo '<TR BGCOLOR="'.getStyle(++$rows).'"><TD>Experience:</TD><TD>'.$player->getExperience().'</TD></TR>';

			if($config['characters']['magic_level'])
				echo'<TR BGCOLOR="'.getStyle(++$rows).'"><TD>Magic Level:</TD><TD>'.$player->getMagLevel().'</TD></TR>';

			//frags
			if(tableExist('player_killers') && $config['characters']['frags']) {
				$frags_count = 0;
				$frags_query = $db->query(
					'SELECT COUNT(`player_id`) as `frags`' .
					'FROM `player_killers`' .
					'WHERE `player_id` = ' .$player->getId() . ' ' .
					'GROUP BY `player_id`' .
					'ORDER BY COUNT(`player_id`) DESC');

				if($frags_query->rowCount() > 0)
				{
					$frags_query = $frags_query->fetch();
					$frags_count = $frags_query['frags'];
				}

				echo
				'<TR BGCOLOR="' . getStyle(++$rows) . '"><TD>Frags:</TD><TD>' . $frags_count . '</TD></TR>';
			}

			if(!empty($config['towns'][$player->getTownId()]))
				echo '<TR BGCOLOR="' . getStyle(++$rows) . '"><TD>Residence:</TD><TD>' . $config['towns'][$player->getTownId()] . '</TD></TR>';

			if($config['characters']['balance'])
				echo '<TR BGCOLOR="'.getStyle(++$rows).'"><TD>Balance:</TD><TD>'.$player->getBalance().' Gold Coins.</TD></TR>';

			$town_field = 'town';
			if(fieldExist('town_id', 'houses'))
				$town_field = 'town_id';
			else if(fieldExist('townid', 'houses'))
				$town_field = 'townid';
			else if(!fieldExist('town', 'houses'))
				$town_field = false;
			
			if(fieldExist('name', 'houses')) {
				$house = $db->query('SELECT `id`, `paid`, `name`' . ($town_field != false ? ', `' . $town_field . '` as `town`' : '') . ' FROM `houses` WHERE `owner` = '.$player->getId())->fetch();
				if(isset($house['id']))
				{
					$add = '';
					if($house['paid'] > 0)
						$add = ' is paid until '.date("M d Y", $house['paid']);

					echo
					'<TR BGCOLOR="'.getStyle(++$rows).'">
						<TD>House:</TD>
						<TD>
							<TABLE BORDER=0><TR>
								<TD>' . (isset($house['name']) ? $house['name'] : $house['id']) . (isset($house['town']) ? ' (' . $config['towns'][$house['town']] . ')' : '') . $add . '</TD>
								<TD>
									<FORM ACTION="?subtopic=houses&page=view" METHOD=post>
										<INPUT TYPE=hidden NAME=house VALUE="'. (isset($house['name']) ? $house['name'] : $house['id']) . '">
										<INPUT TYPE=image NAME="View" ALT="View" SRC="'.$template_path.'/images/buttons/sbutton_view.gif" BORDER=0 WIDTH=120>
									</FORM>
								</TD>
							</TR></TABLE>
						</TD>
					</TR>';
				}
			}

			$rank_of_player = $player->getRank();
			if($rank_of_player->isLoaded()) {
				$guild = $rank_of_player->getGuild();
				if($guild->isLoaded()) {
					$guild_name = $guild->getName();
					echo
					'<TR BGCOLOR="'.getStyle(++$rows).'">'.
						'<TD>Guild membership:</TD><TD>'.$rank_of_player->getName().' of the ' . getGuildLink($guild_name) . '</TD>'.
					'</TR>';
				}
			}

			echo
				'<TR BGCOLOR="'.getStyle(++$rows).'"><TD>Last login:</TD><TD>';
			$lastlogin = $player->getLastLogin();
			if(empty($lastlogin))
				echo 'Never logged in.';
			else
				echo date("M d Y, H:i:s", $lastlogin).' CEST';

			echo '</TD></TR>';
			if($config['characters']['creation_date'])
				echo
				'<TR BGCOLOR="'.getStyle(++$rows).'"><TD>Created:</TD><TD>'.date("M d Y, H:i:s", $player->getCreated()).' CEST</TD></TR>';

		$comment = $player->getComment();/*
		$newlines   = array("\r\n", "\n", "\r");
		$comment_with_lines = str_replace($newlines, '<br />', $comment, $count);
		if($count < 50)
			$comment = $comment_with_lines;*/
		if(!empty($comment))
			echo '<TR BGCOLOR="'.getStyle(++$rows).'"><TD VALIGN=top>Comment:</TD><TD>' . wordwrap(nl2br($comment), 60, "<br />", true) . '</TD></TR>';

		echo
				'<TR BGCOLOR="'.getStyle(++$rows).'"><TD>Account Status:</TD><TD>' . (($account->isPremium()) ? 'Premium Account' : 'Free Account') . '</TD></TR>'.
			'</TABLE>';

	echo '<br>'.
			'<TABLE BORDER=0 WIDTH=100%><TR>';

				if($config['characters']['skills'])
				{
					if(fieldExist('skill_fist', 'players')) {// tfs 1.0+
						$skills_db = $db->query('SELECT `skill_fist`, `skill_club`, `skill_sword`, `skill_axe`, `skill_dist`, `skill_shielding`, `skill_fishing` FROM `players` WHERE `id` = ' . $player->getId())->fetch();
						
						$skill_ids = array(
							POT::SKILL_FIST => 'skill_fist',
							POT::SKILL_CLUB => 'skill_club',
							POT::SKILL_SWORD => 'skill_sword',
							POT::SKILL_AXE => 'skill_axe',
							POT::SKILL_DIST => 'skill_dist',
							POT::SKILL_SHIELD => 'skill_shielding',
							POT::SKILL_FISH => 'skill_fishing',
						);
						
						$skills = array();
						foreach($skill_ids as $skillid => $field_name) {
							$skills[] = array('skillid' => $skillid, 'value' => $skills_db[$field_name]);
						}
					}
					else
						$skills = $db->query('SELECT `skillid`, `value` FROM `player_skills` WHERE `player_id` = ' . $player->getId() . ' LIMIT 7');
					echo '
					<TD WIDTH=30% VALIGN="TOP">'.
						'<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%>
							<TR BGCOLOR='.$config['vdarkborder'].'>
								<TD COLSPAN=2 class="white"><B>Skills</B></TD>
							</TR>';

						$i = 0;
						foreach($skills as $skill)
						{
							echo
							'<TR BGCOLOR=' . getStyle(++$i) . '>
								<TD VALIGN=top>' . getSkillName($skill['skillid']) . '</TD>
								<TD>' . $skill['value'] . '</TD>
							</TR>';
						}

						echo
						'</TABLE>
					</TD>';
				}

				if($config['characters']['quests'] && !empty($config['quests']))
				{
					$quests = $config['quests'];
					$sql_query_in = '';
					$i = 0;
					foreach($quests as $quest_name => $quest_storage)
					{
						if($i != 0)
							$sql_query_in .= ', ';

						$sql_query_in .= $quest_storage;
						$i++;
					}

					$storage_sql = $db->query('SELECT `key`, `value` FROM `player_storage` WHERE `player_id` = '.$player->getId().' AND `key` IN (' . $sql_query_in . ')');
					$player_storage = array();
					foreach($storage_sql as $storage)
						$player_storage[$storage['key']] = $storage['value'];

					echo '
					<TD WIDTH=40% VALIGN="TOP">'.
						'<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%>
							<TR BGCOLOR='.$config['vdarkborder'].'>
								<TD COLSPAN=2 class="white"><B>Quests</B></TD>
							</TR>';

						//for($i=0; $i < count($quests); $i++)
						$i = 0;
						foreach($quests as $name => $storage)
						{
							$i++;
							echo
							'<TR BGCOLOR='.getStyle($i - 1).'>
								<TD VALIGN=top>'.$name.'</TD>
								<TD><img src="images/'.($player_storage[$storage] ? 'true' : 'false').'.png" border="0"/></TD>
							</TR>';
						}

						echo
						'</TABLE>
					</TD>';
				}

				if($config['characters']['equipment'])
				{
					echo '
					<TD WIDTH=100 VALIGN="TOP">'.
						'<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%>'.
							'<TR BGCOLOR='.$config['vdarkborder'].'><TD COLSPAN=2 class="white"><B>Equipment</B></TD></TR>'.
							'<TR BGCOLOR='.getStyle(1).'><TD>'.generate_player_lookup($player).'</TD></TR>
						</TABLE>
					</TD>';
				}

			echo '</TR></TABLE>';


		if(tableExist('killers')) {
			$player_deaths = $db->query('SELECT `id`, `date`, `level` FROM `player_deaths` WHERE `player_id` = '.$player->getId().' ORDER BY `date` DESC LIMIT 0,10;');
			if(count($player_deaths))
			{
				$dead_add_content = '<br/><TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD COLSPAN=2 class="white"><B>Character Deaths</B></TD></TR>';

				$number_of_rows = 0;
				foreach($player_deaths as $death)
				{
					$dead_add_content .= "<tr bgcolor=\"".getStyle($number_of_rows++)."\">
					<td width=\"20%\" align=\"center\">".date("j M Y, H:i", $death['date'])."</td>
					<td> ";
					$killers = $db->query("SELECT environment_killers.name AS monster_name, players.name AS player_name, players.deleted AS player_exists FROM killers LEFT JOIN environment_killers ON killers.id = environment_killers.kill_id
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
								$dead_add_content .= "Killed at level <b>".$death['level']."</b>";
							else if($i == $count)
								$dead_add_content .= " and";
							else
								$dead_add_content .= ",";

							$dead_add_content .= " by ";
							if($killer['monster_name'] != "")
								$dead_add_content .= $killer['monster_name']." summoned by ";

							if($killer['player_exists'] == 0)
								$dead_add_content .= getPlayerLink($killer['player_name']);
							else
								$dead_add_content .= $killer['player_name'];
						}
						else
						{
							if($i == 1)
								$dead_add_content .= "Died at level <b>".$death['level']."</b>";
							else if($i == $count)
								$dead_add_content .= " and";
							else
								$dead_add_content .= ",";

							$dead_add_content .= " by ".$killer['monster_name'];
						}
					}

					$dead_add_content .= ".</td></tr>";
				}

				if($number_of_rows > 0)
					echo $dead_add_content . '</TABLE>';
			}
		}
		else {
			$mostdamage = '';
			if(fieldExist('mostdamage_by', 'player_deaths'))
				$mostdamage = ', `mostdamage_by`, `mostdamage_is_player`, `unjustified`, `mostdamage_unjustified`';
			$deaths = $db->query('SELECT 
				`player_id`, `time`, `level`, `killed_by`, `is_player`' . $mostdamage . ' 
				FROM `player_deaths` 
				WHERE `player_id` = ' . $player->getId() . ' ORDER BY `time` DESC LIMIT 10;');

			if(count($deaths))
			{
				$dead_add_content = '<br/><TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD COLSPAN=2 class="white"><B>Character Deaths</B></TD></TR>';

				$number_of_rows = 0;
				foreach($deaths as $death) 
				{
					$dead_add_content .= "<tr bgcolor=\"".getStyle($number_of_rows++)."\">
					<td width=\"20%\" align=\"center\">".date("j M Y, H:i", $death['time'])."</td>
					<td> ";

					$lasthit = ($death['is_player']) ? getPlayerLink($death['killed_by']) : $death['killed_by'];
					$dead_add_content .=  'Killed at level ' . $death['level'] . ' by ' . $lasthit;
					if($death['unjustified']) {
						$dead_add_content .=  " <font color='red' style='font-style: italic;'>(unjustified)</font>";
					}
				
					$mostdmg = ($death['mostdamage_by'] !== $death['killed_by']) ? true : false;
					if($mostdmg) 
					{
						$mostdmg = ($death['mostdamage_is_player']) ? getPlayerLink($death['mostdamage_by']) : $death['mostdamage_by'];
						$dead_add_content .=  '<br>and by ' . $mostdmg;
						
						if ($death['mostdamage_unjustified']) {
							$dead_add_content .=  " <font color='red' style='font-style: italic;'>(unjustified)</font>";
						}
					} 
					else {
						$dead_add_content .=  " <b>(soloed)</b>";
					}
					
					$dead_add_content .= ".</td></tr>";
				}
			
				if($number_of_rows > 0)
					echo $dead_add_content . '</TABLE>';
			}
		}

		if($config['characters']['frags'])
		{
			//frags list by Xampy
			$i = 0;
			$frags_limit = 10; // frags limit to show? // default: 10
			$player_frags = $db->query('SELECT `player_deaths`.*, `players`.`name`, `killers`.`unjustified` FROM `player_deaths` LEFT JOIN `killers` ON `killers`.`death_id` = `player_deaths`.`id` LEFT JOIN `player_killers` ON `player_killers`.`kill_id` = `killers`.`id` LEFT JOIN `players` ON `players`.`id` = `player_deaths`.`player_id` WHERE `player_killers`.`player_id` = '.$player->getId().' ORDER BY `date` DESC LIMIT 0,'.$frags_limit.';');
			if(count($player_frags))
			{
				$frags = 0;
				$frag_add_content .= '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><br><TR BGCOLOR='.$config['vdarkborder'].'><TD COLSPAN=2 CLASS=white><B>Victims</B></TD></TR>';
				foreach($player_frags as $frag)
				{
					$frags++;
					$frag_add_content .= '<tr bgcolor="' . getStyle($frags) . '">
					<td width="20%" align="center">' . date("j M Y, H:i", $frag['date']) . '</td>
					<td>Fragged <a href="' . getPlayerLink($frag[name], false) . '">' . $frag[name] . '</a> at level ' . $frag[level];

					$frag_add_content .= ". (".(($frag['unjustified'] == 0) ? "<font size=\"1\" color=\"green\">Justified</font>" : "<font size=\"1\" color=\"red\">Unjustified</font>").")</td></tr>";
				}
				if($frags > 0)
					echo $frag_add_content . '</TABLE>';
			}
		}

	//Signature
	//Js
	if($config['signature_enabled'])
	{
		echo '<script type="text/javascript">
		function showSignLinks()
		{
			if(document.getElementById(\'signLinks\').style.display == "none")
			{
				document.getElementById(\'signLinks\').style.display = "inline";
				document.getElementById(\'signText\').innerHTML = "Hide links";
			}
			else
			{
				document.getElementById(\'signLinks\').style.display = "none";
				document.getElementById(\'signText\').innerHTML = "Show links";
			}
		}
		</script>';
		echo '<br>
			<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'><TD COLSPAN=2 class="white"><B>Signature</B></TD></TR>
			<TR BGCOLOR='.$config['lightborder'].'><TD align="center" VALIGN=top>';
					$signature_url = BASE_URL . 'tools/signature/?name=' . urlencode($player->getName());
					if($config['friendly_urls'])
						$signature_url = BASE_URL . urlencode($player->getName()) . '.png';

					echo '
					<img src="' . $signature_url . '" alt="Signature for player '.$player->getName().'">
					<br/>
					<b><a href="#" onclick="showSignLinks(); return false;" id="signText">Show links</a></b>
					<br>
					<table id="signLinks" style="display: none;">
						<tr>
							<td>Website:</td>
							<td><input type="text" value="<a href=&quot;' . getPlayerLink($player->getName(), false) . '&quot;><img src=&quot;' . $signature_url . '&quot;></a>" style="width: 400px;" onclick="this.select()"></td>
						</tr>
						<tr>
							<td>Forum::</td>
							<td><input type="text" value="[URL=' . getPlayerLink($player->getName(), false) . '][IMG]' . $signature_url . '[/IMG][/URL]" style="width: 400px;" onclick="this.select()"></td>
						</tr>
						<tr>
							<td>Direct link::</td>
							<td><input type="text" value="' . $signature_url . '" style="width: 400px;" onclick="this.select()"></td>
						</tr>
					</table>
				</TD></TR>
			</TABLE>';
	}

		if($player->getCustomField('hidden') != 1)
		{
			$rows = 0;
			echo '<br/><br/>
			<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%>
				<TR BGCOLOR='.$config['vdarkborder'].'>
					<TD COLSPAN=2 class="white"><B>Account Information</B></TD>
				</TR>';

			$realName = $account->getCustomField('rlname');
			if(!empty($realName))
			{
				echo '
				<TR BGCOLOR='.getStyle(++$rows).'>
					<TD WIDTH=20%>Real name:</TD>
					<TD>'.$realName.'</TD>
				</TR>';
			}

			$group = $player->getGroup();
			if($group->isLoaded() && $group->getId() != 1)
			{
				echo
				'<TR BGCOLOR='.getStyle(++$rows).'>
					<TD>Position:</TD>
					<TD>' . ucfirst($group->getName()) . '</TD>
				</TR>';
			}

		$realLocation = $account->getCustomField('location');
		if(isset($realLocation[0]))
		{
				echo
				'<TR BGCOLOR='.getStyle(++$rows).'>
					<TD WIDTH=20%>Location:</TD>
					<TD>'.$realLocation.'</TD>
				</TR>';
		}

		echo
				'<TR BGCOLOR='.getStyle(++$rows).'>
					<TD WIDTH=20%>Created:</TD>';
					$bannedUntil = '';
					$banned = array();
					if(tableExist('account_bans'))
						$banned = $db->query('SELECT `expires_at` as `expires` FROM `account_bans` WHERE `account_id` = '.$account->getId().' and `expires_at` > ' . time());
					else if(tableExist('bans')) {
						if(fieldExist('expires', 'bans'))
							$banned = $db->query('SELECT `expires` FROM `bans` WHERE (`value` = '.$account->getId().' or `value` = '.$player->getId().') and `active` = 1 and `type` != 2 and `type` != 4 and `expires` > ' . time());
					else
							$banned = $db->query('SELECT `time` as `time` FROM `bans` WHERE (`account` = '.$account->getId().' or `player` = '.$player->getId().') and `type` != 2 and `type` != 4 and `time` > ' . time());
					}
					foreach($banned as $ban)
					{
						$bannedUntil = ' <font color="red">[Banished '.($ban['expires'] == "-1" ? 'forever' : 'until '.date("d F Y, h:s", $ban['expires'])).']</font>';
					}
					echo '<TD>'.date("j F Y, g:i a", $account->getCustomField("created")).$bannedUntil.'</TD>
				</TR>
			</TABLE>';

		echo '<br/><br/>
			<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%>
				<TR BGCOLOR='.$config['vdarkborder'].'>
					<TD COLSPAN=4 class="white"><B>Characters</B></TD>
				</TR>
				<TR BGCOLOR='.$config['darkborder'].'>
					<TD WIDTH=62%><B>Name</B></TD>
					<TD WIDTH=30%><B>Level</B></TD>
					<TD WIDTH=8%><b>Status</b></TD>
					<TD><B>&#160;</B></TD>
				</TR>';

		$account_players = $account->getPlayersList();
		$account_players->orderBy('name');
		$player_number = 0;
		foreach($account_players as $player_list)
		{
			$player_list_status = '';
			if($player_list->isHidden())
				continue;

			$player_number++;
			if($player_list->isOnline())
				$player_list_status = '<b><font color="green">Online</font></b>';

			echo '<TR BGCOLOR="'.getStyle($player_number).'"><TD><NOBR>'.$player_number.'.&#160;'.$player_list->getName();
			echo ($player_list->isDeleted()) ? '<font color="red"> [DELETED]</font>' : '';
			echo '</NOBR></TD><TD>'.$player_list->getLevel().' '.$config['vocations'][$player_list->getVocation()].'</TD><TD>' . $player_list_status . '</TD><TD><TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0><FORM ACTION="' . internalLayoutLink('characters') . '" METHOD=post><TR><TD><INPUT TYPE=hidden NAME=name VALUE="'.$player_list->getName().'"><INPUT TYPE=image NAME="View '.$player_list->getName().'" ALT="View '.$player_list->getName().'" SRC="'.$template_path.'/images/buttons/sbutton_view.gif" BORDER=0 WIDTH=120 HEIGHT=18></TD></TR></FORM></TABLE></TD></TR>';
		}
		echo '</TABLE></TD><TD><IMG SRC="'.$template_path.'/images/general/blank.gif" WIDTH=10 HEIGHT=1 BORDER=0></TD></TR></TABLE>';
		}
	echo '<br/><br/>' . generate_search_table();
	echo '</TABLE>';
}
else
{
	$search_errors[] = 'Character <b>' . $name . '</b> does not exist or has been deleted.';
	output_errors($search_errors);
	$search_errors = array();

	$promotion = '';
	if(fieldExist('promotion', 'players'))
		$promotion = ', `promotion`';

	$deleted = 'deleted';
	if(fieldExist('deletion', 'players'))
		$deleted = 'deletion';

	$query = $db->query('SELECT `name`, `level`, `vocation`' . $promotion . ' FROM `players` WHERE `name` LIKE  ' . $db->quote('%' . $name . '%') . ' AND ' . $deleted . ' != 1;');
	if($query->rowCount() > 0)
	{
		echo 'Did you mean:<ul>';
		foreach($query as $player) {
			if(isset($player['promotion'])) {
				if((int)$player['promotion'] > 0)
					$player['vocation'] += ($player['promotion'] * $config['vocations_amount']);
			}
			echo '<li>' . getPlayerLink($player['name']) . ' (<small><strong>level ' . $player['level'] . ', ' . $config['vocations'][$player['vocation']] . '</strong></small>)</li>';
		}
		echo '</ul>';
	}

	echo generate_search_table(true);
}

if(!empty($search_errors))
	output_errors($search_errors);
