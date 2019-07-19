<?php
/**
 * Reports
 *
 * @package   MyAAC
 * @author    Lee
 * @copyright 2018 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Report viewer';
?>

<div class="box">
	<div class="box-header">
		<h3 class="box-title">Reports:</h3>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="col-sm-12">
				<table id="tb_reports">
					<thead>
					<tr>
						<th>Report name</th>
						<th>Last updated</th>
					</tr>
					</thead>
					<tbody>
					<?php
					$files = array();
					$server_path_reports = $config['data_path'] . 'reports/';

					if (file_exists($server_path_reports)) {
						foreach (scandir($server_path_reports) as $f) {
							if ($f[0] == '.' || $f == '..')
								continue;

							if (is_dir($server_path_reports . $f)) {
								foreach (scandir($server_path_reports . $f) as $f2) {
									if ($f2[0] == '.' || $f2 == '..')
										continue;
									$files[] = array($f . '/' . $f2, $server_path_reports);
								}

								continue;
							}

							$files[] = array($f, $server_path_reports);
						}
					}

					$i = 0;
					foreach ($files as $f) {
						?>
						<tr>
							<td>
								<a href="<?php echo ADMIN_URL . '?p=reports&file=' . $f[0]; ?>"><?php echo $f[0]; ?></a>
							</td>
							<td><?php echo date("Y-m-d H:i:s", filemtime($f[1] . $f[0])); ?></td>
						</tr>
						<?php
					}
					?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?php

$file = isset($_GET['file']) ? $_GET['file'] : NULL;
if (!empty($file)) {
	if (!preg_match('/[^A-z0-9\' _\/\-\.]/', $file)) {
		if (file_exists($server_path_reports . $file)) {
			echo '<div class="box"><div class="box-header"><h3 class="box-title"><b>' . $file . '</b></h3></div><div class="box-body">';
			echo nl2br(file_get_contents($server_path_reports . $file));
			echo '</div></div>';
		} else
			echo 'Specified file does not exist.';
	} else
		echo 'Invalid file name specified.';
}
?>
<script>
	$(document).ready(function () {
		$('#tb_reports').DataTable();

	});
</script>