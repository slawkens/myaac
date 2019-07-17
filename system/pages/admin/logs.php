<?php
/**
 * Logs
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Logs viewer';
?>

<div class="box">
	<div class="box-header">
		<h3 class="box-title">Logs:</h3>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="col-sm-12">
				<table id="tb_logs">
					<thead>
					<tr>
						<th>Log name</th>
						<th>Last updated</th>
					</tr>
					</thead>
					<tbody>
					<?php
					$files = array();
					$aac_path_logs = BASE . 'system/logs/';
					foreach (scandir($aac_path_logs) as $f) {
						if ($f[0] == '.' || $f == '..' || is_dir($aac_path_logs . $f))
							continue;

						$files[] = array($f, $aac_path_logs);
					}

					$server_path_logs = $config['server_path'] . 'logs/';
					if (!file_exists($server_path_logs)) {
						$server_path_logs = $config['data_path'] . 'logs/';
					}

					if (file_exists($server_path_logs)) {
						foreach (scandir($server_path_logs) as $f) {
							if ($f[0] == '.' || $f == '..')
								continue;

							if (is_dir($server_path_logs . $f)) {
								foreach (scandir($server_path_logs . $f) as $f2) {
									if ($f2[0] == '.' || $f2 == '..')
										continue;
									$files[] = array($f . '/' . $f2, $server_path_logs);
								}

								continue;
							}

							$files[] = array($f, $server_path_logs);
						}
					}

					$i = 0;
					foreach ($files as $f) {
						?>
						<tr>
							<td>
								<a href="<?php echo ADMIN_URL . '?p=logs&file=' . $f[0]; ?>"><?php echo $f[0]; ?></a>
							</td>
							<td><?php echo date("Y-m-d H:i:s", filemtime($f[1] . $f[0])); ?></td>
						</tr>
						<?php
					}
					?>
					</tbody>
					<tfoot>
					<th>Log name</th>
					<th>Last updated</th>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>

<?php

$file = isset($_GET['file']) ? $_GET['file'] : NULL;
if (!empty($file)) {
	if (!preg_match('/[^A-z0-9\' _\/\-\.]/', $file)) {
		if (file_exists($aac_path_logs . $file)) {
			echo '
             <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><b>' . $file . '</b></h3>
                </div>
                <div class="box-body">';
			echo nl2br(file_get_contents($aac_path_logs . $file));
			echo '</div>
             </div>';
		} else if (file_exists($server_path_logs . $file)) {
			echo '<div class="box"><div class="box-header"><h3 class="box-title"><b>' . $file . '</b></h3></div><div class="box-body">';
			echo nl2br(file_get_contents($server_path_logs . $file));
			echo '</div></div>';
		} else
			echo 'Specified file does not exist.';
	} else
		echo 'Invalid file name specified.';
}
?>
<script>
	$(function () {
		$('#tb_logs').DataTable()
	})
</script>