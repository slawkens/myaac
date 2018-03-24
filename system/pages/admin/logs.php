<?php
/**
 * Logs
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Logs viewer';
?>

<table class="table" width="100%" border="0" cellspacing="1" cellpadding="4">
	<tr>
		<th><b>Log name</b></td>
		<th><b>Last updated</b></td>
	</tr>
<?php

$files = array();
$aac_path_logs = BASE . 'system/logs/';
foreach(scandir($aac_path_logs) as $f) {
	if($f[0] == '.' || $f == '..' || is_dir($aac_path_logs . $f))
		continue;

	$files[] = array($f, $aac_path_logs);
}

$server_path_logs = $config['server_path'] . 'logs/';
if(!file_exists($server_path_logs)) {
    $server_path_logs = $config['data_path'] . 'logs/';
}

if(file_exists($server_path_logs)) {
	foreach(scandir($server_path_logs) as $f) {
		if($f[0] == '.' || $f == '..')
			continue;

		if(is_dir($server_path_logs . $f)) {
			foreach(scandir($server_path_logs . $f) as $f2) {
				if($f2[0] == '.' || $f2 == '..')
					continue;
				$files[] = array($f . '/' . $f2, $server_path_logs);
			}

			continue;
		}

		$files[] = array($f, $server_path_logs);
	}
}

$i = 0;
foreach($files as $f) {
?>
	<tr>
		<td><a href="<?php echo ADMIN_URL . '?p=logs&file=' . $f[0]; ?>"><?php echo $f[0]; ?></a></td>
		<td><?php echo date("Y-m-d H:i:s", filemtime($f[1] . $f[0])); ?></td>
	</tr>
<?php
}
?>
</table>
<?php

$file = isset($_GET['file']) ? $_GET['file'] : NULL;
if(!empty($file))
{
	if(!preg_match('/[^A-z0-9\' _\/\-\.]/', $file))
	{
		if(file_exists($aac_path_logs . $file))
			echo str_repeat('<br/>', 3) . '<b>' . $file . ':</b><br/><br/>' . nl2br(file_get_contents($aac_path_logs . $file));
		else if(file_exists($server_path_logs . $file))
			echo str_repeat('<br/>', 3) . '<b>' . $file . ':</b><br/><br/>' . nl2br(file_get_contents($server_path_logs . $file));

		else
			echo 'Specified file does not exist.';
	}
	else
		echo 'Invalid file name specified.';
}
?>
