<?php
/**
 * Changelog
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.0.3
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'MyAAC - CHANGELOG';

$data = file_get_contents(SYSTEM . 'docs/CHANGELOG');

// replace special characters with HTML entities
// replace line breaks with <br />
$data = nl2br(htmlspecialchars($data));

// replace multiple spaces with single spaces
$data = preg_replace('/\s\s+/', ' ', $data);

// replace URLs with <a href...> elements
$data = preg_replace('/\s(\w+:\/\/)(\S+)/', ' <a href="\\1\\2" target="_blank">\\1\\2</a>', $data);

echo '<div>' . $data . '</div>';
?>
