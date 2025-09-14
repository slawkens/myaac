<?php
/**
 * Change motd
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

require __DIR__ . '/base.php';

if(!MOTD_EXISTS)
	return;

$guild_name = isset($_REQUEST['guild']) ? urldecode($_REQUEST['guild']) : null;
if(!Validator::guildName($guild_name)) {
	$errors[] = Validator::getLastError();
}

if(empty($errors)) {
	$guild = new OTS_Guild();
	$guild->find($guild_name);
	if(!$guild->isLoaded()) {
		$errors[] = "Guild with name <b>" . $guild_name . "</b> doesn't exist.";
	}
}

if(empty($errors)) {
	if($logged) {
		$guild_leader_char = $guild->getOwner();
		$rank_list = $guild->getGuildRanksList();
		$rank_list->orderBy('level', POT::ORDER_DESC);
		$guild_leader = false;
		$account_players = $account_logged->getPlayersList();
		foreach($account_players as $player) {
			if($guild->getOwner()->getId() == $player->getId()) {
				$guild_vice = true;
				$guild_leader = true;
				$level_in_guild = 3;
			}
		}

		$saved = false;
		if($guild_leader) {
			if(isset($_POST['todo']) && $_POST['todo'] == 'save') {
				$motd = htmlspecialchars(stripslashes(substr($_POST['motd'],0, setting('core.guild_motd_chars_limit'))));
				$guild->setCustomField('motd', $motd);
				$saved = true;
			}

			if($saved) {
				success('Changes has been saved');
			}

			$twig->display('guilds.change_motd.html.twig', array(
				'guild' => $guild
			));
		}
		else {
			$errors[] = 'You are not a leader of guild!';
		}
	}
	else {
		$errors[] = 'You are not logged. You can\'t manage guild.';
	}
}
if(!empty($errors)) {
	$twig->display('error_box.html.twig', array('errors' => $errors));

	$twig->display('guilds.back_button.html.twig', array(
		'new_line' => true,
		'action' => getLink('guilds')
	));
}
