<?php
/**
 * Movies
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.0.3
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Movies';

$movies = $db->query('SELECT * FROM ' . $db->tableName(TABLE_PREFIX . 'movies') . ' ORDER BY ' . $db->fieldName('ordering'));
if(!$movies->rowCount())
{
?>
There are no movies added yet.
<?php
	if(admin())
		echo ' You can add new movies in phpmyadmin under ' . TABLE_PREFIX . 'movies table.';
	return;
}
?>
<center>
<?php foreach($movies as $movie): ?>
	<?php echo $movie['title']; ?><br/>
	Author: <?php echo $movie['author']; ?><br/>
	<object width="425" height="344">
		<param name="movie" value="http://www.youtube.com/v/<?php echo $movie['youtube_id']; ?>&hl=pl&fs=1&color1=0x3a3a3a&color2=0x999999"></param>
		<param name="allowFullScreen" value="true"></param>
		<embed src="http://www.youtube.com/v/<?php echo $movie['youtube_id']; ?>&hl=pl&fs=1&color1=0x3a3a3a&color2=0x999999" type="application/x-shockwave-flash" allowfullscreen="true" width="425" height="344"></embed>
	</object><br/><br/>';
<?php endforeach; ?>
</center>
