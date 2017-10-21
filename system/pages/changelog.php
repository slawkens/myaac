<?php
/**
 * Changelog
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.5
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Changelog';

$_page = isset($_GET['page']) ? $_GET['page'] : 0;
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$limit = 30;
$offset = $_page * $limit;
?>

<br/>
<table border="0" cellspacing="1" cellpadding="4" width="100%">
	<tr bgcolor="<?php echo $config['vdarkborder']; ?>">
		<td width="22"><font class="white"><b>Type</b></font></td>
		<td width="22"><font class="white"><b>Where</b></font></td>
		<td width="50"><font class="white"><b>Date</b></font></td>
		<td><font class="white"><b>Description</b></font></td>
	</tr>
<?php

$changelogs = $db->query('SELECT * FROM ' . $db->tableName(TABLE_PREFIX . 'changelog') . ' ORDER BY ' . $db->fieldName('id') . ' DESC LIMIT ' . $limit . ' OFFSET ' . $offset);
if(!$changelogs->rowCount())
{
?>
	<tr>
		<td bgcolor="<?php echo $config['lightborder']; ?>">There are no change logs for the moment.</td>
	</tr>
<?php
	return;
}
else
{
	$i = 0;
	foreach($changelogs as $log)
	{
		$type = getChangelogType($log['type']);
		$where = getChangelogWhere($log['where']);
?>
		<tr bgcolor="<?php echo getStyle($i++); ?>">
			<td align="center">
				<img src="images/changelog/<?php echo $type; ?>.png" title="<?php echo ucfirst($type); ?>"/>
			</td>
			<td align="center">
				<img src="images/changelog/<?php echo $where; ?>.png" title="<?php echo ucfirst($where); ?>"/>
			</td>
			<td><?php echo date("j.m.Y", $log['date']); ?></td>
			<td><?php echo $log['body']; ?></td>
		</tr>
<?php
		if ($i >= $limit)
			$next_page = true;
	}
?>
<table border="0" cellspacing="1" cellpadding="4" width="100%">
<?
	if($_page > 0)
		echo '<tr><td width="100%" align="right" valign="bottom"><a href="?subtopic=changelog&page=' . ($_page - 1) . '" class="size_xxs">Previous Page</a></td></tr>';

	if($next_page)
		echo '<tr><td width="100%" align="right" valign="bottom"><a href="?subtopic=changelog&page=' . ($_page + 1) . '" class="size_xxs">Next Page</a></td></tr>';
?>
</table>
<?php
}

?>
</table>
<?php
function getChangelogType($v)
{
	switch($v) {
		case 1:
			return 'added';
		case 2:
			return 'removed';
		case 3:
			return 'changed';
		case 4:
			return 'fixed';
	}

	return 'Unknown type';
}

function getChangelogWhere($v)
{
	switch($v) {
		case 1:
			return 'server';
		case 2:
			return 'website';
	}

	return 'Unknown where';
}
?>
