<?php
/**
 * Videos
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.6.6
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Videos';

$videos = $db->query('SELECT * FROM `' . TABLE_PREFIX . 'videos` ORDER BY `ordering`;');
if(!$videos->rowCount())
{
?>
There are no videos added yet.
<?php
	if(admin())
		echo ' You can add new videos in phpmyadmin under ' . TABLE_PREFIX . 'videos table.';
	return;
}

echo $twig->render('videos.html.twig', array(
	'videos' => $videos
));
?>
