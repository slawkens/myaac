<?php
/**
 * Change comment
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

use MyAAC\Models\Player;

defined('MYAAC') or die('Direct access not allowed!');

$title = 'Change Comment';
require __DIR__ . '/base.php';

if(!$logged) {
	return;
}

$player = null;
$player_name = isset($_REQUEST['name']) ? stripslashes(urldecode($_REQUEST['name'])) : null;
$new_comment = isset($_POST['comment']) ? htmlspecialchars(stripslashes(substr($_POST['comment'],0,2000))) : NULL;
$new_hideacc = isset($_POST['accountvisible']) ? (int)$_POST['accountvisible'] : NULL;

if($player_name != null) {
	if (Validator::characterName($player_name)) {
		$player = Player::query()
			->where('name', $player_name)
			->where('account_id', $account_logged->getId())
			->first();

		if ($player) {
			if ($player->is_deleted) {
				$errors[] = 'This character is deleted.';
				$player = null;
			}

			if (isset($_POST['changecommentsave']) && $_POST['changecommentsave'] == 1) {
				if(empty($errors)) {
					$player->hidden = $new_hideacc;
					$player->comment = $new_comment;
					$player->save();
					$account_logged->logAction('Changed comment for character <b>' . $player->name . '</b>.');
					$twig->display('success.html.twig', array(
						'title' => 'Character Information Changed',
						'description' => 'The character information has been changed.'
					));
					$show_form = false;
				}
			}
		} else {
			$errors[] = "Error. Character with this name doesn't exist.";
		}
	} else {
		$errors[] = 'Error. Name contain illegal characters.';
	}
}
else {
	$errors[] = 'Please enter character name.';
}

if($show_form) {
	if(!empty($errors)) {
		$twig->display('error_box.html.twig', array('errors' => $errors));
	}

	if(isset($player) && $player) {
		$twig->display('account.change_comment.html.twig', array(
			'player' => $player->toArray()
		));
	}
}
?>
