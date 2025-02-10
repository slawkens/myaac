<?php
/**
 * Create character
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\CreateCharacter;

defined('MYAAC') or die('Direct access not allowed!');

$title = 'Create Character';
require PAGES . 'account/base.php';

if(!$logged) {
	return;
}

$character_name = isset($_POST['name']) ? stripslashes($_POST['name']) : null;
$character_sex = isset($_POST['sex']) ? (int)$_POST['sex'] : null;
$character_vocation = isset($_POST['vocation']) ? (int)$_POST['vocation'] : null;
$character_town = isset($_POST['town']) ? (int)$_POST['town'] : null;

if (!admin() && !empty($character_name)) {
	$character_name = ucwords(strtolower($character_name));
}

$character_created = false;
$save = isset($_POST['save']) && $_POST['save'] == 1;
$errors = array();
if($save) {
	$createCharacter = new CreateCharacter();

	$character_created = $createCharacter->doCreate($character_name, $character_sex, $character_vocation, $character_town, $account_logged, $errors);
}

if(count($errors) > 0) {
	$twig->display('error_box.html.twig', array('errors' => $errors));
}

if(!$character_created) {
	$twig->display('account.characters.create.html.twig', array(
		'name' => $character_name,
		'sex' => $character_sex,
		'vocation' => $character_vocation,
		'town' => $character_town,
		'save' => $save,
		'errors' => $errors
	));
}
