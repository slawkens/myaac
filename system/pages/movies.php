<?php
/**
 * Movies
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.2.4
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
	<iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo $movie['youtube_id']; ?>" frameborder="0" allowfullscreen></iframe><br/><br/>
<?php endforeach; ?>
</center>
