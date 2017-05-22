<?php
/**
 * Creatures
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.2.2
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = "Creatures";

$rarity = array( 
    'Not Rare'    => 7, 
    'Semi Rare'    => 2, 
    'Rare'        => 0.5, 
    'Very Rare' => 0 
); 

function addLoot($loot, $level=1)
{ 
	foreach($loot as $test) { 
		$chance = $test['chance']; 
		if(!$chance) 
			$chance = $test['chance1']; 

		printLoot($level, $test['id'], $test['countmax'], $chance); 
		foreach($test as $k => $v) 
			addLoot($v->item, $level + 1); 
	} 
} 
 
 $i = 0;
function printLoot($level, $itemid, $count, $chance)
{ 
	global $itemList, $rarity, $i; 

	$chance /= 1000; 
	if(isset($_GET['lootrate'])) { 
		global $lootRate; 
		$chance *= $lootRate; 
	} 

	foreach($rarity as $lootRarity => $percent){ 
		if($chance >= $percent)
		{ 
			//echo str_repeat("... ", $level) . '<u>' . ($count ? $count : 1) . '</u> <span style="color: #7878FF; font-weight: bold;">' . $itemList[(int)$itemid] . '</span> ' . $itemid . ' <span style="color: #C45; font-weight: bold;">' . $lootRarity . '</span> (<span style="color: #FF9A9A;">' . $chance . '%</span>)<br />'; 
			if($i % 6 == 0)
			{
				if($i != 0)
					echo '</td></tr>';
				echo '<tr BGCOLOR="'.getStyle(0).'"><td width="100">';
			}
			echo getItemImage($itemid);
			$i++;
			break; 
		} 
	} 
}

$canEdit = hasFlag(FLAG_CONTENT_MONSTERS) || admin();
if(isset($_POST['reload_monsters']) && $canEdit)
{
	try { $db->query("DELETE FROM myaac_monsters WHERE 1 = 1"); } catch(PDOException $error) {}
	echo '<h2>Reload monsters.</h2>';
	echo '<h2>All records deleted from table \'myaac_monsters\' in database.</h2>';
	$allmonsters = new OTS_MonstersList($config['data_path'].'monster/');
	//$names_added must be an array
	$names_added[] = '';
	//add monsters
	foreach($allmonsters as $lol) {
		$monster = $allmonsters->current();
		//load monster mana needed to summon/convince
		$mana = $monster->getManaCost();
		//load monster experience
		$exp = $monster->getExperience();
		//load monster name
		$name = $monster->getName();
		//load monster health
		$health = $monster->getHealth();
		//load monster speed and calculate "speed level"
		$speed_ini = $monster->getSpeed();
		if($speed_ini <= 220) {
			$speed_lvl = 1;
		} else {
			$speed_lvl = ($speed_ini - 220) / 2;
		}
		//check "is monster use haste spell"
		$defenses = $monster->getDefenses();
		$use_haste = 0;
		foreach($defenses as $defense) {
			if($defense == 'speed') {
				$use_haste = 1;
			}
		}
		//load monster flags
		$flags = $monster->getFlags();
		//create string with immunities
		$immunities = $monster->getImmunities();
		$imu_nr = 0;
		$imu_count = count($immunities);
		$immunities_string = '';
		foreach($immunities as $immunitie) {
			$immunities_string .= $immunitie;
			$imu_nr++;
			if($imu_count != $imu_nr) {
				$immunities_string .= ", ";
			}
		}
		
		//create string with voices
		$voices = $monster->getVoices();
		$voice_nr = 0;
		$voice_count = count($voices);
		$voices_string = '';
		foreach($voices as $voice) {
			$voices_string .= '"'.$voice.'"';
			$voice_nr++;
			if($voice_count != $voice_nr) {
				$voices_string .= ", ";
			}
		}
		//load race
		$race = $monster->getRace();
		//create monster gfx name
		//$gfx_name =  str_replace(" ", "", trim(mb_strtolower($name))).".gif";
		$gfx_name =  trim(mb_strtolower($name)).".gif";
		//don't add 2 monsters with same name, like Butterfly
		
		if(!isset($flags['summonable']))
			$flags['summonable'] = '0';
		if(!isset($flags['convinceable']))
			$flags['convinceable'] = '0';

		if(!in_array($name, $names_added)) {
			try { $db->query("INSERT INTO myaac_monsters (hide_creature, name, mana, exp, health, speed_lvl, use_haste, voices, immunities, summonable, convinceable, race, gfx_name, file_path) VALUES (0, '".$name."', '".$mana."', '".$exp."', '".$health."', '".$speed_lvl."', '".$use_haste."', '".$voices_string."', '".$immunities_string."', '".$flags['summonable']."', '".$flags['convinceable']."', '".$race."', '".$gfx_name."', '" . $allmonsters->currentFile() . "')"); } catch(PDOException $error) {}
			$names_added[] = $name;
			echo "Added: ".$name."<br/>";
		}
	}
}

if($canEdit)
{
?>
	<form method="post" action="index.php?subtopic=creatures">
		<input type="hidden" name="reload_monsters" value="yes"/>
		<input type="submit" value="(admin) Reload monsters"/>
	</form>
<?php
}

if(empty($_REQUEST['creature']))
{
	$allowed_order_by = array('name', 'exp', 'health', 'summonable', 'convinceable', 'race');
	$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'name';
	//generate sql query
	$desc = '';
	if(isset($_REQUEST['desc']) && $_REQUEST['desc'] == 1) {
	$desc = " DESC";
	}
	if($order == 'name') {
	$whereandorder = ' ORDER BY name'.$desc;
	}
	elseif($order == 'exp') {
	$whereandorder = ' ORDER BY exp'.$desc.', name';
	}
	elseif($order == 'health') {
	$whereandorder = ' ORDER BY health'.$desc.', name';
	}
	elseif($order == 'summonable') {
	$whereandorder = ' AND summonable = 1 ORDER BY mana'.$desc;
	}
	elseif($order == 'convinceable') {
	$whereandorder = ' AND convinceable = 1 ORDER BY mana'.$desc;
	}
	elseif($order == 'race') {
	$whereandorder = ' ORDER BY race'.$desc.', name';
	}
	else {
	$whereandorder = ' ORDER BY name';
	}
	//send query to database
	$monsters = $db->query('SELECT * FROM '.$db->tableName(TABLE_PREFIX . 'monsters').' WHERE hide_creature != 1'.$whereandorder);
	echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><TR BGCOLOR='.$config['vdarkborder'].'>';
	if($order == 'name' && !isset($_REQUEST['desc'])) {
	echo '<TD class="white" width="200"><B><a href="?subtopic=creatures&order=name&desc=1"><font class="white">Name DESC</a></B></TD>';
	} else {
	echo '<TD class="white" width="200"><B><a href="?subtopic=creatures&order=name"><font class="white">Name</a></B></TD>';
	}
	if($order == 'health' && !isset($_REQUEST['desc'])) {
	echo '<TD class="white"><B><a href="?subtopic=creatures&order=health&desc=1"><font class="white">Health<br/>DESC</a></B></TD>';
	} else {
	echo '<TD class="white"><B><a href="?subtopic=creatures&order=health"><font class="white">Health</a></B></TD>';
	}
	if($order == 'exp' && !isset($_REQUEST['desc'])) {
	echo '<TD class="white"><B><a href="?subtopic=creatures&order=exp&desc=1"><font class="white">Experience<br/>DESC</a></B></TD>';
	} else {
	echo '<TD class="white"><B><a href="?subtopic=creatures&order=exp"><font class="white">Experience</a></B></TD>';
	}
	if($order == 'summonable' && !isset($_REQUEST['desc'])) {
	echo '<TD class="white"><B><a href="?subtopic=creatures&order=summonable&desc=1"><font class="white">Summonable<br/>Mana DESC</a></B></TD>';
	} else {
	echo '<TD class="white"><B><a href="?subtopic=creatures&order=summonable"><font class="white">Summonable<br/>Mana</a></B></TD>';
	}
	if($order == 'convinceable' && !isset($_REQUEST['desc'])) {
	echo '<TD class="white"><B><a href="?subtopic=creatures&order=convinceable&desc=1"><font class="white">Convinceable<br/>Mana DESC</a></B></TD>';
	} else {
	echo '<TD class="white"><B><a href="?subtopic=creatures&order=convinceable"><font class="white">Convinceable<br/>Mana</a></B></TD>';
	}
	if($order == 'race' && !isset($_REQUEST['desc'])) {
	echo '<TD class="white"><B><a href="?subtopic=creatures&order=race&desc=1"><font class="white">Race<br/>DESC</a></B></TD></TR>';
	} else {
	echo '<TD class="white"><B><a href="?subtopic=creatures&order=race"><font class="white">Race</a></B></TD></TR>';
	}
	$number_of_rows = 0;
	foreach($monsters as $monster) {
		echo '<TR BGCOLOR="' . getStyle($number_of_rows++) . '"><TD><a href="?subtopic=creatures&creature='.urlencode($monster['name']).'">'.$monster['name'].'</a></TD><TD>'.$monster['health'].'</TD><TD>'.$monster['exp'].'</TD>';
	if($monster['summonable']) {
	echo '<TD>'.$monster['mana'].'</TD>';
	}
		else {
	echo '<TD>---</TD>';
	}
		
	if($monster['convinceable']) {
	echo '<TD>'.$monster['mana'].'</TD>';
	}
		else {
	echo '<TD>---</TD>';
	}
		
	echo '<TD>'.ucwords($monster['race']).'</TD></TR>';
	}

	echo '</TABLE>';
	return;
}


$monster_name = stripslashes(trim(ucwords($_REQUEST['creature'])));
$monster = $db->query('SELECT * FROM '.$db->tableName(TABLE_PREFIX . 'monsters').' WHERE '.$db->fieldName('hide_creature').' != 1 AND '.$db->fieldName('name').' = '.$db->quote($monster_name).';')->fetch();
if(isset($monster['name']))
{
	$title = $monster['name'] . " - Creatures";

	echo '<center><h2>'.$monster['name'].'</h2></center>';
	echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><tr><td>
	<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=60%>';
	$number_of_rows = 0;
	echo '<tr BGCOLOR="'.getStyle($number_of_rows).'"><td width="100"><b>Health: </b></td><td>'.$monster['health'].'</td></tr>';
	$number_of_rows++;
	echo '<tr BGCOLOR="'.getStyle($number_of_rows).'"><td width="100"><b>Experience: </b></td><td>'.$monster['exp'].'</td></tr>';
	$number_of_rows++;
	echo '<tr BGCOLOR="'.getStyle($number_of_rows).'"><td width="100"><b>Speed like: </b></td><td>'.$monster['speed_lvl'].' level';
	$number_of_rows++;
	if($monster['use_haste'])
		echo ' (Can use haste)';

	echo '</td></tr>';

	$number_of_rows++;
	if($monster['summonable'] == 1)
		echo '<tr BGCOLOR="'.getStyle($number_of_rows).'"><td width="100"><b>Summon: </b></td><td>'.$monster['mana'].' mana</td></tr>';
	else {
		echo '<tr BGCOLOR="'.getStyle($number_of_rows).'"><td width="100"><b>Summon: </b></td><td>Impossible</td></tr>';
	}

	$number_of_rows++;
	if($monster['convinceable'] == 1)
		echo '<tr BGCOLOR="'.getStyle($number_of_rows).'"><td width="100"><b>Convince: </b></td><td>'.$monster['mana'].' mana</td></tr>';
	else
		echo '<tr BGCOLOR="'.getStyle($number_of_rows).'"><td width="100"><b>Convince: </b></td><td>Impossible</td></tr>';

	echo '</TABLE></td><td align=left>
	<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=40%>
	<tr><td align=left>';
	if(!file_exists('images/monsters/'.$monster['gfx_name'])) {
		$gfx_name =  str_replace(" ", "", $monster['gfx_name']);
		if(file_exists('images/monsters/' . $gfx_name))
			echo '<img src="images/monsters/'.$gfx_name.'" height="128" width="128">';
	else
		echo '<img src="images/monsters/nophoto.png" height="128" width="128">';
	}
	else
		echo '<img src="images/monsters/' . $monster['gfx_name'] . '" height="128" width="128">';

	echo '</td></tr>
	</TABLE></td></tr><tr><td>
	<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%>';
	if(!empty($monster['immunities']))
	{
		$number_of_rows++;
		echo '<tr BGCOLOR="'.getStyle($number_of_rows).'"><td width="100"><b>Immunities: </b></td><td width="100%">'.$monster['immunities'].'</td></tr>';
	}
	if(!empty($monster['voices']))
	{
		$number_of_rows++;
		echo '<tr BGCOLOR="'.getStyle($number_of_rows).'"><td width="100"><b>Voices: </b></td><td width="100%">'.$monster['voices'].'</td></tr>';
	}
	echo '</TABLE></td></tr>';

	echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%>';
	$loot = simplexml_load_file($config['data_path'] . 'monster/' . $monster['file_path']); 
	if($loot)
	{ 
		if($item = $loot->loot->item)
			addLoot($item);
	}

	echo '</TABLE></td></tr>';
	echo '</TABLE>';
}
else
{
	echo 'Monster with name <b>'.$monster_name.'</b> doesn\'t exist.';
}
//back button
echo '<br/></br><center><form action="?subtopic=creatures" METHOD=post><div class="BigButton" style="background-image:url('.$template_path.'/images/buttons/sbutton.gif)" ><div onMouseOver="MouseOverBigButton(this);" onMouseOut="MouseOutBigButton(this);" ><div class="BigButtonOver" style="background-image:url('.$template_path.'/images/buttons/sbutton_over.gif);" ></div><input class="ButtonText" type="image" name="Back" alt="Back" src="'.$template_path.'/images/buttons/_sbutton_back.gif" ></div></div></form></center>';
?>