<?php
/**
 * Visitors viewer
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.2.3
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Visitors';

if(!$config['visitors_counter']): ?>
Visitors counter is disabled.<br/>
You can enable it by editing this configurable in <b>config.local.php</b> file:<br/>
<p style="margin-left: 3em;"><b>$config['visitors_counter'] = true;</b></p>
<?php
return;
endif;

require(SYSTEM . 'libs/visitors.php');
$visitors = new Visitors($config['visitors_counter_ttl']);
?>
Users being active within last <?php echo $config['visitors_counter_ttl']; ?> minutes.<br/><br/>
<table class="table" width="100%" border="0">
	<tr>
		<th><b>IP</b></th>
		<th><b>Last visit</b></th>
		<th><b>Page</b></th>
	</tr>
<?php

function compare($a, $b) {
	return $a['lastvisit'] > $b['lastvisit'] ? -1 : 1;
}

$tmp = $visitors->getVisitors();
usort($tmp, 'compare');

$i = 0;
foreach($tmp as $visitor)
{
?>
	<tr>
		<td><?php echo $visitor['ip']; ?></td>
		<td><?php echo date("H:i:s", $visitor['lastvisit']); ?></td>
		<td><a href="<?php echo $visitor['page']; ?>"><?php echo substr($visitor['page'], 0, 50); ?></a></td>
	</tr>
<?php
}
?>
</table>
