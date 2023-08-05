<?php
/**
 * Experience table
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Experience Table';

$experience = array();
$columns = setting('core.experience_table_columns');
for($i = 0; $i < $columns; $i++) {
	for($level = $i * setting('core.experience_table_rows') + 1; $level < $i * setting('core.experience_table_rows') + (setting('core.experience_table_rows') + 1); $level++) {
		$experience[$level] = OTS_Toolbox::experienceForLevel($level);
	}
}

$twig->display('experience_table.html.twig', array(
	'experience' => $experience
));
?>
