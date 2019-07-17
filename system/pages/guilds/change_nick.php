<?php
/**
 * Change nick
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

if(!$logged) {
	echo 'You are not logged.';
	$twig->display('guilds.back_button.html.twig');
	return;
}

$name = isset($_REQUEST['name']) ? stripslashes($_REQUEST['name']) : null;
$new_nick = isset($_REQUEST['nick']) ? stripslashes($_REQUEST['nick']) : null;

if(!$name) {
	echo 'Please enter new name.';
	return;
}

if(!$new_nick) {
	echo 'Please enter new nick.';
	return;
}

$player = new OTS_Player();
$player->find($name);
$player_from_account = false;
if(strlen($new_nick) <= 40)
{
	if($player->isLoaded())
	{
		$account_players = $account_logged->getPlayersList();
		if(count($account_players))
		{
			foreach($account_players as $acc_player)
			{
				if($acc_player->getId() == $player->getId())
					$player_from_account = true;
			}
			if($player_from_account)
			{
				$player->setGuildNick($new_nick);
				echo 'Guild nick of player <b>'.$player->getName().'</b> changed to <b>'.htmlentities($new_nick).'</b>.';
				$addtolink = '&action=show&guild='.$player->getRank()->getGuild()->getName();
			}
			else
				echo 'This player is not from your account.';
		}
		else
			echo 'This player is not from your account.';
	}
	else
		echo 'Unknow error occured.';
}
else
	echo 'Too long guild nick. Max. 40 chars, your length: '.strlen($new_nick);

$twig->display('guilds.back_button.html.twig');
?>