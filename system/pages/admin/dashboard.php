<?php
/**
 * Dashboard
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.2.3
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Dashboard';

if($cache->enabled()) {
	if(isset($_GET['clear_cache'])) {
		if(clearCache())
			success('Cache cleared.');
		else
			error('Error while clearing cache.');
	}

?>
<table class="table">
	<tr>
		<th><a href="?p=dashboard&clear_cache" onclick="return confirm('Are you sure?');">Clear cache</a></th>
	</tr>
</table>
<?php
}
if(isset($_GET['maintenance'])) {
	$_status = (int)$_POST['status'];
	$message = $_POST['message'];
	if(empty($message)) {
		error('Message cannot be empty.');
	}
	else if(strlen($message) > 255) {
		error('Message is too long. Maximum length allowed is 255 chars.');
	}
	else {
		$tmp = '';
		if(fetchDatabaseConfig('site_closed', $tmp))
			updateDatabaseConfig('site_closed', $_status);
		else
			registerDatabaseConfig('site_closed', $_status);

		if(fetchDatabaseConfig('site_closed_message', $tmp))
			updateDatabaseConfig('site_closed_message', $message);
		else
			registerDatabaseConfig('site_closed_message', $message);
	}
}
$is_closed = getDatabaseConfig('site_closed') == '1';

$closed_message = 'Server is under maintenance, please visit later.';
$tmp = '';
if(fetchDatabaseConfig('site_closed_message', $tmp))
	$closed_message = $tmp;
?>
<form action="?p=dashboard&maintenance" method="post">
	<table class="table">
		<tr>
			<th colspan="2">Maintenance
			</th>
		</tr>
		<tr>
			<td>Site status:</td>
			<td>
				<select name="status">
					<option value="0"<?php echo (!$is_closed ? ' selected' : ''); ?>>Open</option>
					<option value="1"<?php echo ($is_closed ? ' selected' : ''); ?>>Closed</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Message: (only if closed)</td>
			<td>
				<textarea name="message" maxlength="255" cols="40" rows="5"><?php echo $closed_message; ?></textarea>
			<td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" class="button" value="Update"/>
			</td>
	</table>
</form>
<br/>
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
	$(document).ready(function() {
		$("#status-more").hide();
		$("#more-button").text("More");
	});

	$("#more-button").click(function() {
		if($("#status-more").is(":hidden")) {
			$("#more-button").text("Hide");
			$("#status-more").show();
		}
		else {
			$("#more-button").text("More");
			$("#status-more").hide();
		}

		return false;
	});
</script>
<?php endif;

function clearCache()
{
	global $cache, $template_name;

	$tmp = '';
	if($cache->fetch('status', $tmp))
		$cache->delete('status');
	
	if($cache->fetch('templates', $tmp))
		$cache->delete('templates');

	if($cache->fetch('config_lua', $tmp))
		$cache->delete('config_lua');

	if($cache->fetch('vocations', $tmp))
		$cache->delete('vocations');

	if($cache->fetch('towns', $tmp))
		$cache->delete('towns');

	if($cache->fetch('groups', $tmp))
		$cache->delete('groups');

	if($cache->fetch('visitors', $tmp))
		$cache->delete('visitors');

	if($cache->fetch('views_counter', $tmp))
		$cache->delete('views_counter');

	if($cache->fetch('failed_logins', $tmp))
		$cache->delete('failed_logins');

	if($cache->fetch('news' . $template_name . '_' . NEWS, $tmp))
		$cache->delete('news' . $template_name . '_' . NEWS);

	if($cache->fetch('news' . $template_name . '_' . TICKET, $tmp))
		$cache->delete('news' . $template_name . '_' . TICKET);

	if($cache->fetch('news' . $template_name . '_' . ARTICLE, $tmp))
		$cache->delete('news' . $template_name . '_' . ARTICLE);

	if($cache->fetch('template_ini' . $template_name, $tmp))
		$cache->delete('template_ini' . $template_name);

	return true;
}