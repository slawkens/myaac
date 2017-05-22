<?php
/**
 * Spells
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.2.2
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Spells';

$config_vocations = $config['vocations'];
$canEdit = hasFlag(FLAG_CONTENT_SPELLS) || admin();
if(isset($_POST['reload_spells']) && $canEdit)
{
	try { $db->query('DELETE FROM ' . TABLE_PREFIX . 'spells WHERE 1 = 1'); } catch(PDOException $error) {}
	echo '<h2>Reload spells.</h2>';
	echo '<h2>All records deleted from table <b>' . TABLE_PREFIX . 'spells</b> in database.</h2>';
	foreach($config_vocations as $voc_id => $voc_name) {
		$vocations_ids[$voc_name] = $voc_id;
	}

	$allspells = new OTS_SpellsList($config['data_path'].'spells/spells.xml');
	//add conjure spells
	$conjurelist = $allspells->getConjuresList();
	echo "<h3>Conjure:</h3>";
	foreach($conjurelist as $spellname) {
		$spell = $allspells->getConjure($spellname);
		$lvl = $spell->getLevel();
		$mlvl = $spell->getMagicLevel();
		$mana = $spell->getMana();
		$name = $spell->getName();
		$soul = $spell->getSoul();
		$spell_txt = $spell->getWords();
		$vocations = $spell->getVocations();
		$nr_of_vocations = count($vocations);
		$vocations_to_db = "";
		$voc_nr = 0;
		foreach($vocations as $vocation_to_add) {
			if(check_number($vocation_to_add)) {
				$vocations_to_db .= $vocation_to_add;
			}
			else
				$vocations_to_db .= $vocations_ids[$vocation_to_add];
			$voc_nr++;
			
			if($voc_nr != $nr_of_vocations) {
				$vocations_to_db .= ',';
			}
		}

		$enabled = $spell->isEnabled();
		if($enabled) {
			$hide_spell = 0;
		}
		else {
			$hide_spell = 1;
		}
		$pacc = $spell->isPremium();
		if($pacc) {
			$pacc = '1';
		}
		else {
			$pacc = '0';
		}
		$type = 2;
		$count = $spell->getConjureCount();
		try { $db->query("INSERT INTO myaac_spells (spell, name, words, type, mana, level, maglevel, soul, premium, vocations, conjure_count, hidden) VALUES ('".$spell_txt."', '".$name."', '".$spell_txt."', '".$type."', '".$mana."', '".$lvl."', '".$mlvl."', '".$soul."', '".$pacc."', '".$vocations_to_db."', '".$count."', '".$hide_spell."')"); } catch(PDOException $error) {}
		echo "Added: ".$name."<br>";
	}

	//add instant spells
	$instantlist = $allspells->getInstantsList();
	echo "<h3>Instant:</h3>";
	foreach($instantlist as $spellname) {
		$spell = $allspells->getInstant($spellname);
		$lvl = $spell->getLevel();
		$mlvl = $spell->getMagicLevel();
		$mana = $spell->getMana();
		$name = $spell->getName();
		$soul = $spell->getSoul();
		$spell_txt = $spell->getWords();
		if(strpos($spell_txt, '###') !== false)
			continue;

		$vocations = $spell->getVocations();
		$nr_of_vocations = count($vocations);
		$vocations_to_db = "";
		$voc_nr = 0;
		foreach($vocations as $vocation_to_add) {
			if(check_number($vocation_to_add)) {
				$vocations_to_db .= $vocation_to_add;
			}
			else
				$vocations_to_db .= $vocations_ids[$vocation_to_add];
			$voc_nr++;
			
			if($voc_nr != $nr_of_vocations) {
				$vocations_to_db .= ',';
			}
		}
		$enabled = $spell->isEnabled();
		if($enabled) {
			$hide_spell = 0;
		}
		else {
			$hide_spell = 1;
		}
		$pacc = $spell->isPremium();
		if($pacc) {
			$pacc = '1';
		}
		else {
			$pacc = '0';
		}
		$type = 1;
		$count = 0;
		try { $db->query("INSERT INTO myaac_spells (spell, name, words, type, mana, level, maglevel, soul, premium, vocations, conjure_count, hidden) VALUES ('".$spell_txt."', '".$name."', '".$spell_txt."', '".$type."', '".$mana."', '".$lvl."', '".$mlvl."', '".$soul."', '".$pacc."', '".$vocations_to_db."', '".$count."', '".$hide_spell."')"); } catch(PDOException $error) {}
		echo "Added: ".$name."<br/>";
	}
}

if($canEdit)
{
?>
	<form method="post" action="index.php?subtopic=spells">
		<input type="hidden" name="reload_spells" value="yes"/>
		<input type="submit" value="(admin) Reload spells"/>
	</form>
<?php
}

$vocation_id = (int) (isset($_REQUEST['vocation_id']) ? $_REQUEST['vocation_id'] : 'All');
$order = 'spell';
if(isset($_GET['order']))
	$order = $_GET['order'];

if(!in_array($order, array('spell', 'words', 'type', 'mana', 'level', 'maglevel', 'soul')))
	$order = 'level';
?>

<form action="?subtopic=spells" method="post">
<table border="0" cellspacing="1" cellpadding="4" width="100%">
	<tr bgcolor="<?php echo $config['vdarkborder']; ?>">
		<td class="white"><b>Spell Search</b></td>
	</tr>
	<tr bgcolor="<?php echo $config['darkborder']; ?>">
		<td>Only for vocation: <select name="vocation_id">
			<option value="All" <?php
			if('All' == $vocation_id)
				echo 'SELECTED';

			echo '>All';

			foreach($config_vocations as $id => $vocation)
			{
				echo '<option value="' . $id . '" ';
				if($id == $vocation_id && $vocation_id != "All" && $vocation_id != '')
					echo 'SELECTED';

				echo '>' . $vocation;
			}
			?>
			</select>
			<input type="hidden" name="order" value="<?php echo $order; ?>">&nbsp;&nbsp;&nbsp;<input type="image" name="Submit" alt="Submit" src="<?php echo $template_path; ?>/images/buttons/sbutton_submit.gif" border="0" width="120" height="18">
		</td>
	</tr>
</table>
</form>

<table border="0" cellspacing="1" cellpadding="4" width="100%">
	<tr bgcolor="<?php echo $config['vdarkborder']; ?>">
		<td class="white">
			<b><a href="?subtopic=spells&vocation_id=<?php echo $vocation_id; ?>&order=spell"><font class="white">Name</font></a></b>
		</td>
		<td class="white">
			<b><a href="?subtopic=spells&vocation_id=<?php echo $vocation_id; ?>&order=words"><font class="white">Words</font></a></b>
		</td>
		<td class="white">
			<b><a href="?subtopic=spells&vocation_id=<?php echo $vocation_id; ?>&order=type"><font class="white">Type<br/>(count)</font></a></b>
		</td>
		<td class="white">
			<b><a href="?subtopic=spells&vocation_id=<?php echo $vocation_id; ?>&order=mana"><font class="white">Mana</font></a></b>
		</td>
		<td class="white">
			<b><a href="?subtopic=spells&vocation_id=<?php echo $vocation_id; ?>&order=level"><font class="white">Level</font></a></b>
		</td>
		<td class="white">
			<b><a href="?subtopic=spells&vocation_id=<?php echo $vocation_id; ?>&order=maglevel"><font class="white">Magic<br/>Level</font></a></b>
		</td>
		<td class="white">
			<b><a href="?subtopic=spells&vocation_id=<?php echo $vocation_id; ?>&order=soul"><font class="white">Soul</font></a></b>
		</td>
		<td class="white">
			<b>Premium</b>
		</td>
		<td class="white">
			<b>Vocations:</b>
		</td>
	</tr>
<?php

$i = 0;
$spells = $db->query('SELECT * FROM ' . $db->tableName(TABLE_PREFIX . 'spells') . ' WHERE ' . $db->fieldName('hidden') . ' != 1 ORDER BY ' . $order . ', level');
if(isset($vocation_id) && $vocation_id != 'All' && $vocation_id != '')
{
	foreach($spells as $spell)
	{
		$spell_vocations = explode(",", $spell['vocations']);
		if(in_array($vocation_id, $spell_vocations) || empty($spell['vocations']))
		{
			echo '<TR BGCOLOR="' . getStyle(++$i) . '"><TD>' . $spell['name'] . '</TD><TD>' . $spell['words'] . '</TD>';
			if($spell['type'] == 2)
				echo '<TD>Conjure ('.$spell['conjure_count'].')</TD>';
			else
				echo '<TD>Instant</TD>';

			echo '<TD>' . $spell['mana'] . '</TD><TD>' . $spell['level'] . '</TD><TD>' . $spell['maglevel'] . '</TD><TD>' . $spell['soul'] . '</TD><TD>' . ($spell ['premium'] == 1 ? 'yes' : 'no') . '</TD><TD>' . $config_vocations[$vocation_id] . '</TD></TR>';
		}
	}
}
else
{
	foreach($spells as $spell)
	{
		$spell_vocations = explode(",", $spell['vocations']);

		echo '<TR BGCOLOR="' . getStyle(++$i) . '"><TD>' .$spell['name'] . '</TD><TD>' . $spell['words'] . '</TD>';
		if($spell['type'] == 1)
			echo '<TD>Instant</TD>';
		else
			echo '<TD>Conjure ('.$spell['conjure_count'].')</TD>';

		echo '<TD>' . $spell['mana'] . '</TD><TD>' . $spell['level'] . '</TD><TD>' . $spell['maglevel'] . '</TD><TD>' . $spell['soul'] . '</TD><TD>'. ($spell ['premium'] == 1 ? 'yes' : 'no') .'</TD><TD><font size="1">';

		$showed_vocations = 0;
		foreach($spell_vocations as $spell_vocation)
		{
			if(isset($config_vocations[$spell_vocation])) {
				echo $config_vocations[$spell_vocation];
				$showed_vocations++;
			}
			if($showed_vocations != count($spell_vocations))
				echo '<br/>';
		}

		echo '</font></TD></TR>';
	}
}
?>
</table>
