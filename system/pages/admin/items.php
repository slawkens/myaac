<?php
/**
 * Load items.xml
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = 'Load items.xml';

require(LIBS . 'items.php');
require(LIBS . 'weapons.php');

echo $twig->render('admin.items.html.twig');

$reload = isset($_REQUEST['reload']) && (int)$_REQUEST['reload'] == 1;
if($reload) {
	if(Items::loadFromXML(true))
		success('Successfully loaded items.');
	else
		error(Items::getError());
	
	if(Weapons::loadFromXML(true))
		success('Successfully loaded weapons.');
	else
		error(Weapons::getError());
}