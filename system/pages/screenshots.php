<?php
/**
 * Screenshots
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.2.0
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Screenshots';

if(isset($_GET['screenshot']))
{
	$screenshot = $db->query('SELECT * FROM ' . $db->tableName(TABLE_PREFIX . 'screenshots') . ' WHERE ' . $db->fieldName('name') . ' = ' . $db->quote($_GET['screenshot']) . ' ORDER by ordering LIMIT 1;');
	if($screenshot->rowCount() == 1)
		$screenshot = $screenshot->fetch();
	else
	{
		echo 'Screenshot with this name does not exists.';
		return;
	}

	$previous_screenshot = $db->query('SELECT * FROM ' . $db->tableName(TABLE_PREFIX . 'screenshots') . ' WHERE `id` = ' . $db->quote($screenshot['id'] - 1) . ' ORDER by ordering;');
	if($previous_screenshot->rowCount() == 1)
		$previous_screenshot = $previous_screenshot->fetch();
	else
		$previous_screenshot = NULL;

	$next_screenshot = $db->query('SELECT * FROM ' . $db->tableName(TABLE_PREFIX . 'screenshots') . ' WHERE `id` = ' . $db->quote($screenshot['id'] + 1) . ' ORDER by ordering;');
	if($next_screenshot->rowCount() == 1)
		$next_screenshot = $next_screenshot->fetch();
	else
		$next_screenshot = NULL;
	?>
		<div style="position: relative; height: 15px; width: 100%;">
			<?php if($next_screenshot): ?>
			<a style="float: right;" href="?subtopic=screenshots&screenshot=<?php echo $next_screenshot['name']; ?>" >next <img src="images/arrow_right.gif" width=15 height=11 border=0 ></a>
			<?php endif;
			if($previous_screenshot): ?>
			<a style="position: absolute;" href="?subtopic=screenshots&screenshot=<?php echo $previous_screenshot['name']; ?>"><img src="images/arrow_left.gif" width=15 height=11 border=0 > previous</a>
			<?php endif; ?>
			<div style="position: absolute; width: 80%; margin-left: 10%; margin-right: 10%; text-align: center;">
				<a href="?subtopic=screenshots" ><img src="images/arrow_up.gif" width=11 height=15 border=0 > back</a>
			</div>
		</div>
		<div style="position: relative; text-align: center; top: 20px; ">
			<img src="<?php echo $screenshot['image']; ?>" />
			<div style="margin-top: 15px; margin-bottom: 35px; "><?php echo $screenshot['comment']; ?></div>
		</div>
	<?php
	return;
}

$screenshots = $db->query('SELECT * FROM ' . $db->tableName(TABLE_PREFIX . 'screenshots') . ' ORDER BY ' . $db->fieldName('ordering'));
if(!$screenshots->rowCount())
{
?>
	There are no screenshots added to gallery yet.
<?php
	if(admin())
		echo ' You can add new screenshots in phpmyadmin under ' . TABLE_PREFIX . 'screenshots table.';
	return;
}
?>

Click on the image to enlarge.<br/><br/>
<?php foreach($screenshots as $screenshot): ?>
	<table>
		<tr>
			<td style="height: 120px;" >
				<a href="?subtopic=screenshots&screenshot=<?php echo $screenshot['name']; ?>" >
					<img src="<?php echo $screenshot['thumb']; ?>" border="0" />
				</a>
			</td>
			<td><?php echo $screenshot['comment']; ?></td>
		</tr>
	</table>
<?php endforeach;

class Screenshots
{


}
?>
