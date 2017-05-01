<?php
/**
 * Statistics
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.0.1
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Statistics';
?>
<table>
	<tr>
		<td>
			<table class="table">
				<tr><th colspan="2">Statistics</th></tr>
				<tr><td>Total accounts:</td>
				<?php
				$query = $db->query('SELECT count(*) as `how_much` FROM `accounts`;');
				$query = $query->fetch();
				echo '<td>' . $query['how_much'] . '</td></tr>';
				?>
				<tr><td>Total players:</td>
				<?php
				$query = $db->query('SELECT count(*) as `how_much` FROM `players`;');
				$query = $query->fetch();
				echo '<td>' . $query['how_much'] . '</td></tr>';
				?>
				<tr><td>Total guilds:</td>
				<?php
				$query = $db->query('SELECT count(*) as `how_much` FROM `guilds`;');
				$query = $query->fetch();
				echo '<td>' . $query['how_much'] . '</td></tr>';
				?>
				<tr><td>Total houses:</td>
				<?php
				$query = $db->query('SELECT count(*) as `how_much` FROM `houses`;');
				$query = $query->fetch();
				echo '<td>' . $query['how_much'] . '</td></tr>';
				?>
			</table>
		</td>
		<td>
			<table class="table">
				<tr><th colspan="3">TOP 10 - Most wealth accounts</th></tr>
				<tr><th>#</th><th>Account name</th><th>Premium points</th></tr>
				<?php
				$query = $db->query('SELECT premium_points, name FROM accounts ORDER BY premium_points DESC LIMIT 10;');
				$i = 0;
				foreach($query as $result)
				{
					echo '<tr><td>' . ++$i . '.</td><td>' . $result['name'] . '</td><td>' . $result['premium_points'] . '</td></tr>';
				}
				?>
			</table>
		</td>
	</tr>
</table>