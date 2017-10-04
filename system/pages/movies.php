<?php
/**
 * Movies
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.4.2
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Movies';

$movies = $db->query('SELECT * FROM `' . TABLE_PREFIX . 'movies` ORDER BY `ordering`;');
if(!$movies->rowCount())
{
?>
There are no movies added yet.
<?php
	if(admin())
		echo ' You can add new movies in phpmyadmin under ' . TABLE_PREFIX . 'movies table.';
	return;
}

echo $twig->render('movies.html.twig', array(
	'movies' => $movies
));
?>
