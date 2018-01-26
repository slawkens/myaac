<?php
/**
 * Creatures
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = "Creatures";

?>
<script type="text/javascript" src="tools/tipped.js"></script>
<link rel="stylesheet" type="text/css" href="tools/tipped.css"/>
<script>
	$(document).ready(function() {
		Tipped.create('.tooltip');
	});
</script>

<?php
$canEdit = hasFlag(FLAG_CONTENT_MONSTERS) || admin();
if(isset($_POST['reload_monsters']) && $canEdit)
{
	require LIBS . 'creatures.php';
	if(Creatures::loadFromXML(true)) {
		if (Creatures::getMonstersList()->hasErrors())
			error('There were some problems loading your monsters.xml file. Please check system/logs/error.log for more info.');
	}
	else {
		error(Creatures::getLastError());
	}
}

if($canEdit)
{
?>
	<form method="post" action="<?php echo getLink('creatures'); ?>">
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
	$monsters = $db->query('SELECT * FROM `' . TABLE_PREFIX . 'monsters` WHERE `hidden` != 1'.$whereandorder);
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

	echo '<td>'.ucwords($monster['race']).'</td></tr>';
	}

	echo '</table>';
	return;
}


$monster_name = stripslashes(trim(ucwords($_REQUEST['creature'])));
$monster = $db->query('SELECT * FROM `' . TABLE_PREFIX . 'monsters` WHERE `hidden` != 1 AND `name` = '.$db->quote($monster_name).';')->fetch();
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
	$monster['gfx_name'] = trim(strtolower($monster['name'])).".gif";
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
	$immunities = json_decode($monster['immunities'], true);
	if(count($immunities) > 0)
	{
		$number_of_rows++;
		echo '<tr BGCOLOR="'.getStyle($number_of_rows).'"><td width="100"><b>Immunities: </b></td><td width="100%">'.implode(', ', $immunities).'</td></tr>';
	}

	$voices = json_decode($monster['voices'], true);
	if(count($voices) > 0)
	{
		foreach($voices as &$voice) {
			$voice = '"' . $voice . '"';
		}

		$number_of_rows++;
		echo '<tr BGCOLOR="'.getStyle($number_of_rows).'"><td width="100"><b>Voices: </b></td><td width="100%">'.implode(', ', $voices).'</td></tr>';
	}
	echo '</TABLE></td></tr>';

	$loot = json_decode($monster['loot'], true);
	if($loot)
	{
		echo '<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%><tr><td style="display: block;">';
		function sort_by_chance($a, $b)
		{
			if($a['chance'] == $b['chance']) {
				return 0;
			}
			return ($a['chance'] > $b['chance']) ? -1 : 1;
		}

		usort($loot, 'sort_by_chance');

		$i = 0;
		foreach($loot as $item) {
			$name = getItemNameById($item['id']);
			$tooltip = $name . '<br/>Chance: ' . round($item['chance'] / 1000, 2) . '%<br/>Max count: ' . $item['count'];

			echo '<img src="' . $config['item_images_url'] . $item['id'] . '.gif" class="tooltip" title="' . $tooltip . '" width="32" height="32" border="0" alt=" ' .$name . '" />';
			$i++;
		}

		echo '</td></tr></TABLE>';
	}

	echo '</td></tr>';
	echo '</TABLE>';
}
else
{
	echo "Monster with name <b>" . $monster_name . "</b> doesn't exist.";
}

//back button
echo $twig->render('creatures.back_button.html.twig');
?>
