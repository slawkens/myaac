<?php
/**
 * Online
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.0.2
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Who is online?';

if($config['account_country'])
	require(SYSTEM . 'countries.conf.php');
?>

<table border="0" cellspacing="1" cellpadding="4" width="100%">
	<tr bgcolor="<?php echo $config['vdarkborder']; ?>">
		<td class="white"><b>Server Status</b></td>
	</tr>
<?php
$order = isset($_GET['order']) ? $_GET['order'] : 'name';
if(!in_array($order, array('country', 'name', 'level', 'vocation')))
	$order = $db->fieldName('name');
else if($order == 'country')
	$order = $db->tableName('accounts') . '.' . $db->fieldName('country');
else if($order == 'vocation')
	$order = 'promotion, vocation ASC';

$skull_type = 'skull';
if(fieldExist('skull_type', 'players')) {
	$skull_type = 'skull_type';
}

$skull_time = 'skulltime';
if(fieldExist('skull_time', 'players')) {
	$skull_time = 'skull_time';
}
	
$vocs = array(0, 0, 0, 0, 0);
if(tableExist('players_online')) // tfs 1.0
	$playersOnline = $db->query('SELECT `accounts`.`country`, `players`.`name`, `level`, `vocation`, `' . $skull_time . '` as `skulltime`, `' . $skull_type . '` as `skull` FROM `accounts`, `players`, `players_online` WHERE `players`.`id` = `players_online`.`player_id` AND `accounts`.`id` = `players`.`account_id`  ORDER BY ' . $order);
else
	$playersOnline = $db->query('SELECT `accounts`.`country`, `players`.`name`, `level`, `vocation`, `promotion`, `' . $skull_time . '` as `skulltime`, `' . $skull_type . '` as `skull` FROM `accounts`, `players` WHERE `players`.`online` > 0 AND `accounts`.`id` = `players`.`account_id`  ORDER BY ' . $order);

$players = 0;
$data = '';
foreach($playersOnline as $player)
{
	$skull = '';
	if($config['online_skulls'])
	{
		if($player['skulltime'] > 0 && $player['skull'] == 3)
			$skull = ' <img style="border: 0;" src="images/whiteskull.gif"/>';
		elseif($player['skulltime'] > 0 && $player['skull'] == 4)
			$skull = ' <img style="border: 0;" src="images/redskull.gif"/>';
		elseif($player['skulltime'] > 0 && $player['skull'] == 5)
			$skull = ' <img style="border: 0;" src="images/blackskull.gif"/>';
	}

	if(isset($player['promotion'])) {
		if((int)$player['promotion'] > 0)
			$player['vocation'] + ($player['promotion'] * 4);
	}
					
	$data .= '<tr bgcolor="' . getStyle(++$players) . '">';
	if($config['account_country'])
		$data .= '<td>' . getFlagImage($player['country']) . '</td>';

	$data .= '<td>' . getPlayerLink($player['name']) . $skull . '</td>
		<td>'.$player['level'].'</td>
		<td>'.$config['vocations'][$player['vocation']].'</td>
	</tr>';

	$vocs[$player['vocation']]++;
}

if(!$players): ?>
	<tr bgcolor="<?php echo $config['darkborder']; ?>"><td>Currently no one is playing on <?php echo $config['lua']['serverName']; ?>.</td></tr></table>
<?php else:
?>
	<tr bgcolor="<?php echo $config['darkborder']; ?>">
		<td>
<?php
		if(!$status['online'])
			echo 'Server is offline.<br/>';
		else
		{
			if($config['online_afk'])
			{
				$afk = $players - $status['players'];
				if($afk < 0) {
					$players += abs($afk);
					$afk = 0;
				}
				?>
				Currently there are <b><?php echo $status['players']; ?></b> active and <b><?php echo $afk ?></b> AFK players.<br/>
				Total number of players: <b><?php echo $players; ?></b>.<br/>
<?php
			}
			else
				echo 'Currently ' . $players . ' players are online.<br/>';
		}

		if($config['online_record'])
		{
			$timestamp = false;
			if(tableExist('server_record')) {
			$query =
				$db->query(
					'SELECT ' . $db->fieldName('record') . ', ' . $db->fieldName('timestamp') .
					' FROM ' . $db->tableName('server_record') .
					' WHERE ' . $db->fieldName('world_id') . ' = ' . (int)$config['lua']['worldId'] .
					' ORDER BY ' . $db->fieldName('record') . ' DESC LIMIT 1');
					$timestamp = true;
			}
			else{ // tfs 1.0
				$query = $db->query('SELECT `value` as `record` FROM `server_config` WHERE `config` = ' . $db->quote('players_record'));
			}

			if($query->rowCount() > 0)
			{
				$result = $query->fetch();
				echo 'The maximum on this game world was ' . $result['record'] . ' players' . ($timestamp ? ' on ' . date("M d Y, H:i:s", $result['timestamp']) . '.' : '.');
			}
		}
		?>
		</td>
	</tr>
</table>

<?php
// vocation statistics
if($config['online_vocations']): ?>
	<br/>
	<?php if($config['online_vocations_images']): ?>
		<table width="200" cellspacing="1" cellpadding="0" border="0" align="center">
			<tr bgcolor="<?php echo $config['darkborder']; ?>">
				<td><img src="images/sorcerer.png" /></td>
				<td><img src="images/druid.png" /></td>
				<td><img src="images/palladin.png" /></td>
				<td><img src="images/knight.png" /></td>
			</tr>
			<tr bgcolor="<?php echo $config['vdarkborder']; ?>">
				<td class="white" style="text-align: center;"><strong>Sorcerers<br /></strong></td>
				<td class="white" style="text-align: center;"><strong>Druids</strong></td>
				<td class="white" style="text-align: center;"><strong>Paladins</strong></td>
				<td class="white" style="text-align: center;"><strong>Knights</strong></td>
			</tr>
			<tr bgcolor="<?php echo $config['lightborder']; ?>">
				<td style="text-align: center;"><?php echo $vocs[1]; ?></td>
				<td style="text-align: center;"><?php echo $vocs[2]; ?></td>
				<td style="text-align: center;"><?php echo $vocs[3]; ?></td>
				<td style="text-align: center;"><?php echo $vocs[4]; ?></td>
			</tr>
		</table>
		<div style="text-align: center;">&nbsp;</div>
	<?php else: ?>
		<table border="0" cellspacing="1" cellpadding="4" width="100%">
			<tr bgcolor="<?php echo $config['vdarkborder']; ?>">
				<td class="white" colspan="2"><b>Vocation statistics</b></td>
			</tr>

		<?php
			for($i = 1; $i < 5; $i++)
			echo '<tr bgcolor="' . getStyle($i) . '">
				<td width="25%">' . $config['vocations'][0][$i] . '</td>
				<td width="75%">' . $vocs[$i] . '</td>
			</tr>';
			?>
		</table><br/>
	<?php endif;
endif;

// frags counter
if($config['online_skulls']): ?>
	<table width="100%" cellspacing="1">
		<tr>
			<td style="background: <?php echo $config['darkborder']; ?>;" align="center">
				<img src="images/whiteskull.gif"/> - 1 - 6 Frags<br/>
				<img src="images/redskull.gif"/> - 6+ Frags or Red Skull<br/>
				<img src="images/blackskull.gif"/> - 10+ Frags or Black Skull
			</td>
		</tr>
	</table>
<?php endif; ?>

<table border="0" cellspacing="1" cellpadding="4" width="100%">
	<tr bgcolor="<?php echo $config['vdarkborder']; ?>">
		<?php if($config['account_country']): ?>
		<td width="11px"><a href="?subtopic=online&order=country" class="white">#</A></td>
		<?php endif; ?>
		<td width="60%"><a href="?subtopic=online&order=name" class="white">Name</A></td>
		<td width="20%"><a href="?subtopic=online&order=level" class="white">Level</A></td>
		<td width="20%"><a href="?subtopic=online&order=vocation" class="white">Vocation</td>
	</tr>
	<?php echo $data; ?>
</table>
<?php
endif;

//search bar
echo '<BR><FORM ACTION="?subtopic=characters" METHOD=post>  <TABLE WIDTH=100% BORDER=0 CELLSPACING=1 CELLPADDING=4><TR><TD BGCOLOR="'.$config['vdarkborder'].'" class="white"><B>Search Character</B></TD></TR><TR><TD BGCOLOR="'.$config['darkborder'].'"><TABLE BORDER=0 CELLPADDING=1><TR><TD>Name:</TD><TD><INPUT NAME="name" VALUE=""SIZE=29 MAXLENGTH=29></TD><TD><INPUT TYPE=image NAME="Submit" SRC="'.$template_path.'/images/buttons/sbutton_submit.gif" BORDER=0 WIDTH=120 HEIGHT=18></TD></TR></TABLE></TD></TR></TABLE></FORM>';

/* temporary disable it - shows server offline
// update online players counter
if($players > 0)
{
	$status['players'] = $players;
	if($cache->enabled())
		$cache->set('status', serialize($status));
	else
	{
		foreach($status as $key => $value)
			updateDatabaseConfig('serverStatus_' . $key, $value);
	}
}*/
?>
