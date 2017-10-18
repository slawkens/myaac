<?php
/**
 * Spells
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.1
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Spells';

$canEdit = hasFlag(FLAG_CONTENT_SPELLS) || admin();
if(isset($_POST['reload_spells']) && $canEdit)
{
	require LIBS . 'spells.php';
	if(!Spells::loadFromXML(true)) {
		error(Spells::getLastError());
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

$vocation_id = (isset($_REQUEST['vocation_id']) ? (int)$_REQUEST['vocation_id'] : 'All');
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
		<td>
			<table border="0" cellpadding="1">
				<tr>
					<td>Only for vocation: <select name="vocation_id">
							<option value="All" <?php
							if('All' == $vocation_id)
								echo 'SELECTED';
							
							echo '>All';
							
							foreach($config['vocations'] as $id => $vocation)
							{
								echo '<option value="' . $id . '" ';
								if($id == $vocation_id && $vocation_id != "All" && $vocation_id != '')
									echo 'SELECTED';
								
								echo '>' . $vocation;
							}
							?>
						</select>
						<input type="hidden" name="order" value="<?php echo $order; ?>">
					</td>
					<td>
						<?php echo $twig->render('buttons.submit.html.twig'); ?>
					</td>
				</tr>
			</table>
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

			echo '<TD>' . $spell['mana'] . '</TD><TD>' . $spell['level'] . '</TD><TD>' . $spell['maglevel'] . '</TD><TD>' . $spell['soul'] . '</TD><TD>' . ($spell ['premium'] == 1 ? 'yes' : 'no') . '</TD><TD>' . $config['vocations'][$vocation_id] . '</TD></TR>';
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
			if(isset($config['vocations'][$spell_vocation])) {
				echo $config['vocations'][$spell_vocation];
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
