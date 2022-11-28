<?php
defined('MYAAC') or die('Direct access not allowed!');
if (isset($status)) {

	$error_icon = '<i class="fas fa-exclamation-circle text-danger"></i>'; ?>
	<div class=" col-md-6 col-lg-6">
		<div class="card card-info card-outline">
			<div class="card-header border-bottom-0">
				<span class="font-weight-bold m-0">Server Status</span> <span class="float-right small"><b>Last checked</b>: <?php echo(isset($status['lastCheck']) ? date("l, d.m.Y H:i:s", $status['lastCheck']) : $error_icon); ?></span>
			</div>
			<div class="card-body p-0 ">
				<table class="table">
					<tbody>
					<tr>
						<th width="30%">Server</th>
						<td><?php echo(isset($status['server']) & isset($status['serverVersion']) ? $status['server'] . ' x ' . $status['serverVersion'] : $error_icon) ?></td>

					</tr>
					<tr>
						<th>Client</th>
						<td><?php echo(isset($status['clientVersion']) ? $status['clientVersion'] : $error_icon) ?></td>
					</tr>
					<tr>
						<th>Map</th>
						<td>
							<?php if (isset($status['mapName']) & isset($status['mapAuthor']) & isset($status['mapWidth']) & isset($status['mapHeight'])) {
								echo $status['mapName'] . ' by <b>' . $status['mapAuthor'] . '</b><br/>' . $status['mapWidth'] . ' x ' . $status['mapHeight'];
							} else {
								echo $error_icon;
							} ?>
						</td>
					</tr>
					<tr>
						<th>Monsters</th>
						<td><?php echo (isset($status['monsters']) ? $status['monsters'] : $error_icon); ?></td>
					</tr>
					<tr>
						<th>MOTD:</th>
						<td><?php echo(isset($status['motd']) ? $status['motd'] : $error_icon); ?></td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
<?php } ?>
