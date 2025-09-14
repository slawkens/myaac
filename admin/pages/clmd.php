<?php
/**
 * CHANGELOG viewer
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @author    Lee
 * @copyright 2020 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'MyAAC Changelog';

if (!file_exists(BASE . 'CHANGELOG-1.x.md')) {
	echo 'File CHANGELOG.md doesn\'t exist.';
	return;
}

$changelog = file_get_contents(BASE . 'CHANGELOG-1.x.md');

$Parsedown = new Parsedown();

$changelog = $Parsedown->text($changelog); # prints: <p>Hello <em>Parsedown</em>!</p>

echo '<div>' . $changelog . '</div>';
