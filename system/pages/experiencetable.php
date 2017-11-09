<?php
/**
 * Experience table
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Experience Table';

$experience = array();
$columns = $config['experiencetable_columns'];
for($i = 0; $i < $columns; $i++) {
	for($level = $i * $config['experiencetable_rows'] + 1; $level < $i * $config['experiencetable_rows'] + ($config['experiencetable_rows'] + 1); $level++) {
		$experience[$level] = OTS_Toolbox::experienceForLevel($level);
	}
}

echo $twig->render('experience_table.html.twig', array(
	'experience' => $experience
));
?>
