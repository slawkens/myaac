<?php
/**
 * Tools
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Tools';

if (!isset($_GET['tool'])) {
	echo 'Tool not set.';
	return;
}

$tool = $_GET['tool'];
if (preg_match("/[^A-z0-9_\-]/", $tool)) {
	echo 'Invalid tool.';
	return;
}

$file = ADMIN . 'tools/' . $tool . '.php';

if (@file_exists($file)) {
	require $file;
	return;
}

echo 'Tool <strong>' . $tool . '</strong> not found.';

?>
