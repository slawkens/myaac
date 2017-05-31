<?php
/**
 * Experience table
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.2.3
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Experience Table';
?>

This is a list of the experience points that are required to advance to the various levels.
Remember you can also check the respective skill bar in your skill window of the client to check your progress towards the next level.<br/><br/>

<table bgcolor="<?php echo $config['darkborder']; ?>" border="0" cellpadding="4" cellspacing="1" width="100%">
	<tr bgcolor="<?php echo $config['vdarkborder']; ?>">
		<td class="white" colspan="5"><b>Experience Table</b></td>
	</tr>
	<tr>
<?php
	$columns = $config['experiencetable_columns'];
	for($i = 0; $i < $columns; $i++)
	{
?>
		<td>
			<table border="0" cellpadding="2" cellspacing="1" width="100%">
				<tr bgcolor="<?php echo $config['lightborder']; ?>">
					<td><b>Level</b></td>
					<td><b>Experience</b></td>
				</tr>
<?php
		for($level = $i * $config['experiencetable_rows'] + 1; $level < $i * $config['experiencetable_rows'] + ($config['experiencetable_rows'] + 1); $level++)
		{
?>
				<tr bgcolor="<?php echo $config['lightborder']; ?>">
					<td><?php echo $level; ?></td>
					<td><?php echo OTS_Toolbox::experienceForLevel($level); ?></td>
				</tr>
<?php
		}
?>
			</table>
		</td>
<?php
	}
?>
	</tr>
</table>
