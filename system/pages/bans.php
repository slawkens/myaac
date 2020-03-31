<?php
/**
 * Bans
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Bans list';

if($config['otserv_version'] == TFS_02)
{
	echo 'Bans page doesnt work on TFS 0.2/1.0.';
	return;
}

if(!$config['bans_display_all'])
	echo 'Last ' . $config['bans_limit'] . ' banishments.<br/><br/>';

if($config['bans_display_all'])
{
	$_page = isset($_GET['page']) ? $_GET['page'] : 0;
	$offset = $_page * $config['bans_limit'] + 1;
}

$bans = $db->query('SELECT * FROM ' . $db->tableName('bans') . ' WHERE ' . $db->fieldName('active') . ' = 1 ORDER BY ' . $db->fieldName('added') . ' DESC LIMIT ' . ($config['bans_limit'] + 1) . (isset($offset) ? ' OFFSET ' . $offset : ''));
if(!$bans->rowCount())
{
?>
	There are no banishments yet.
<?php
	return;
}
?>
<table border="0" cellspacing="1" cellpadding="4" width="100%">
	<tr align="center" bgcolor="<?php echo $config['vdarkborder']; ?>" class="white">
		<td><span style="color: white"><b>Nick</b></span></td>
		<td><span style="color: white"><b>Type</b></span></td>
		<td><span style="color: white"><b>Expires</b></span></td>
		<td><span style="color: white"><b>Reason</b></span></td>
		<td><span style="color: white"><b>Comment</b></span></td>
		<td><span style="color: white"><b>Added by:</b></span></td>
	</tr>
<?php
foreach($bans as $ban)
{
	if($i++ > 100)
	{
		$next_page = true;
		break;
	}
?>
	<tr align="center" bgcolor="<?php echo getStyle($i); ?>">
		<td height="50" width="140"><?php echo getPlayerLink(getPlayerNameByAccount($ban['value'])); ?></td>
		<td><?php echo getBanType($ban['type']); ?></td>
		<td>
<?php
			if($ban['expires'] == "-1")
				echo 'Never';
			else
				echo date("H:i:s", $ban['expires']) . '<br/>' . date("d M Y", $ban['expires']);
?>
		</td>
		<td><?php echo getBanReason($ban['reason']); ?></td>
		<td><?php echo $ban['comment']; ?></td>
		<td>
<?php
			if($ban['admin_id'] == "0")
				echo 'Autoban';
			else
				echo getPlayerLink(getPlayerNameByAccount($ban['admin_id']));

			echo '<br/>' . date("d.m.Y", $ban['added']);
?>
		</td>
	</tr>
<?php
}
?>
</table>
<table border="0" cellpadding="4" cellspacing="1" width="100%">
<?php
if($_page > 0)
	echo '<tr><td width="100%" align="right" valign="bottom"><a href="?subtopic=bans&page=' . ($_page - 1) . '" class="size_xxs">Previous Page</a></td></tr>';

if($next_page)
	echo '<tr><td width="100%" align="right" valign="bottom"><a href="?subtopic=bans&page=' . ($_page + 1) . '" class="size_xxs">Next Page</a></td></tr>';
?>
</table>
