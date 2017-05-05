<?php
/**
 * Item parser
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @version   0.0.5
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
require_once(SYSTEM . 'libs/items.php');

Items::$files = array(
	'otb' => SYSTEM . 'data/items.otb',
	'spr' => SYSTEM . 'data/Tibia.spr',
	'dat' => SYSTEM . 'data/Tibia.dat'
);
Items::$outputDir = BASE . 'images/items/';

function generateItem($id = 100, $count = 1) {
	Items::generate($id, $count);
}

function itemImageExists($id, $count = 1)
{
	if(!isset($id))
		die('ERROR - itemImageExists: id has been not set!');

	$file_name = $id;
	if($count > 1)
		$file_name .= '-' . $count;

	$file_name = Items::$outputDir . $file_name . '.gif';
	return file_exists($file_name);
}

function outputItem($id = 100, $count = 1)
{
	if(!(int)$count)
		$count = 1;

	if(!itemImageExists($id, $count))
	{
		//echo 'plik istnieje';
		Items::generate($id, $count);
	}

	$expires = 60 * 60 * 24 * 30; // 30 days
	header('Content-type: image/gif');
	header('Cache-Control: public');
	header('Cache-Control: maxage=' . $expires);
	header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

	$file_name = $id;
	if($count > 1)
		$file_name .= '-' . $count;

	$file_name = Items::$outputDir . $file_name . '.gif';
	readfile($file_name);
}
?>
