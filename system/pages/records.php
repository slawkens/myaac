<?php
/**
 * Records
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

$title = "Players Online Records";

echo '
<b><center>Players online records on '.$config['lua']['serverName'].'</center></b>
<TABLE BORDER=0 CELLSPACING=1 CELLPADDING=4 WIDTH=100%>
	<TR BGCOLOR="'.$config['vdarkborder'].'">
		<TD class="white"><b><center>Players</center></b></TD>
		<TD class="white"><b><center>Date</center></b></TD>
	</TR>';

	$i = 0;
	$records_query = $db->query('SELECT * FROM `server_record` ORDER BY `record` DESC LIMIT 50;');
	foreach($records_query as $data)
	{
		echo '<TR BGCOLOR=' . getStyle(++$i) . '>
			<TD><center>' . $data['record'] . '</center></TD>
			<TD><center>' . date("d/m/Y, G:i:s", $data['timestamp']) . '</center></TD>
		</TR>';
	}

echo '</TABLE>';
?>