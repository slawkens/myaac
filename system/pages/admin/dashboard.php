<?php
/**
 * Dashboard
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.1.2
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Dashboard';
?>
<div>
	<?php if($status['online']): ?>
	<p class="success" style="width: 150px; text-align: center;">Status: Online<br/>
		<?php echo $status['uptimeReadable'] . ', ' . $status['players'] . '/' . $status['playersMax']; ?><br/>
		<?php echo $config['lua']['ip'] . ' : ' . $config['lua']['loginPort']; ?>
		<br/><br/><u><a id="more-button" href="#"></a></u>

		<span id="status-more">
		<br/>
		<b>Server</b>:<br/> <?php echo $status['server'] . ' ' . $status['serverVersion']; ?><br/>
		<b>Version</b>: <?php echo $status['clientVersion']; ?><br/><br/>

		<b>Monsters</b>: <?php echo $status['monsters']; ?><br/>
		<b>Map</b>: <?php echo $status['mapName']; ?>, <b>author</b>: <?php echo $status['mapAuthor']; ?>, <b>size</b>: <?php echo $status['mapWidth'] . ' x ' . $status['mapHeight']; ?><br/>
		<b>MOTD</b>:<br/> <?php echo $status['motd']; ?><br/><br/>

		<b>Last updated</b>: <?php echo date("H:i:s", $status['lastCheck']); ?>
		</span>
	</p>
	<?php else: ?>
	<p class="error" style="width: 120px; text-align: center;">Status: Offline</p>
	<?php endif; ?>
</div>
<!--div>
	Version: <?php echo MYAAC_VERSION; ?> (<a id="update" href="#">Check for updates</a>)
</div-->
<?php if($status['online']): ?>
<script type="text/javascript">
	var hidden = false;
	$(document).ready(function() {
		$("#status-more").hide();
		$("#more-button").text("More");
		hidden = true;
	});

	$("#more-button").click(function() {
		if(hidden) {
			$("#more-button").text("Hide");
			$("#status-more").show();
			hidden = false;
		}
		else {
			$("#more-button").text("More");
			$("#status-more").hide();
			hidden = true;
		}

		return false;
	});
</script>
<?php endif; ?>
