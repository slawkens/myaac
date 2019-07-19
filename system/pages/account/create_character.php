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
defined('MYAAC') or die('Direct access not allowed!');

echo '<script type="text/javascript" src="tools/check_name.js"></script>';
$newchar_name = isset($_POST['name']) ? stripslashes(ucwords(strtolower($_POST['name']))) : NULL;
$newchar_sex = isset($_POST['sex']) ? $_POST['sex'] : NULL;
$newchar_vocation = isset($_POST['vocation']) ? $_POST['vocation'] : NULL;
$newchar_town = isset($_POST['town']) ? $_POST['town'] : NULL;

$newchar_created = false;
$save = isset($_POST['save']) && $_POST['save'] == 1;
if($save) {
	$minLength = $config['character_name_min_length'];
	$maxLength = $config['character_name_max_length'];

	if(empty($newchar_name))
		$errors['name'] = 'Please enter a name for your character!';
	else if(strlen($newchar_name) > $maxLength)
		$errors['name'] = 'Name is too long. Max. lenght <b>'.$maxLength.'</b> letters.';
	else if(strlen($newchar_name) < $minLength)
		$errors['name'] = 'Name is too short. Min. lenght <b>'.$minLength.'</b> letters.';
	else {
		if(!admin() && !Validator::newCharacterName($newchar_name)) {
			$errors['name'] = Validator::getLastError();
		}

		$exist = new OTS_Player();
		$exist->find($newchar_name);
		if($exist->isLoaded()) {
			$errors['name'] = 'Character with this name already exist.';
		}
	}

	if(empty($newchar_sex) && $newchar_sex != "0")
		$errors[] = 'Please select the sex for your character!';

	if(count($config['character_samples']) > 1)
	{
		if(!isset($newchar_vocation))
			$errors[] = 'Please select a vocation for your character.';
	}
	else
		$newchar_vocation = $config['character_samples'][0];

	if(count($config['character_towns']) > 1) {
		if(!isset($newchar_town))
			$errors[] = 'Please select a town for your character.';
	}
	else {
		$newchar_town = $config['character_towns'][0];
	}

	if(empty($errors)) {
		if(!isset($config['genders'][$newchar_sex]))
			$errors[] = 'Sex is invalid.';
		if(!in_array($newchar_town, $config['character_towns']))
			$errors[] = 'Please select valid town.';
		if(count($config['character_samples']) > 1)
		{
			$newchar_vocation_check = false;
			foreach($config['character_samples'] as $char_vocation_key => $sample_char)
				if($newchar_vocation == $char_vocation_key)
					$newchar_vocation_check = true;
			if(!$newchar_vocation_check)
				$errors[] = 'Unknown vocation. Please fill in form again.';
		}
		else
			$newchar_vocation = 0;
	}

	if(empty($errors))
	{
		$number_of_players_on_account = $account_logged->getPlayersList()->count();
		if($number_of_players_on_account >= $config['characters_per_account'])
			$errors[] = 'You have too many characters on your account <b>('.$number_of_players_on_account.'/'.$config['characters_per_account'].')</b>!';
	}

	if(empty($errors))
	{
		$char_to_copy_name = $config['character_samples'][$newchar_vocation];
		$char_to_copy = new OTS_Player();
		$char_to_copy->find($char_to_copy_name);
		if(!$char_to_copy->isLoaded())
			$errors[] = 'Wrong characters configuration. Try again or contact with admin. ADMIN: Edit file config/config.php and set valid characters to copy names. Character to copy: <b>'.$char_to_copy_name.'</b> doesn\'t exist.';
	}

	if(empty($errors))
	{
		if($newchar_sex == "0")
			$char_to_copy->setLookType(136);

		$player = new OTS_Player();
		$player->setName($newchar_name);
		$player->setAccount($account_logged);
		//$player->setGroupId($char_to_copy->getGroup()->getId());
		$player->setGroupId(1);
		$player->setSex($newchar_sex);
		$player->setVocation($char_to_copy->getVocation());
		if($db->hasColumn('players', 'promotion'))
			$player->setPromotion($char_to_copy->getPromotion());

		if($db->hasColumn('players', 'direction'))
			$player->setDirection($char_to_copy->getDirection());

		$player->setConditions($char_to_copy->getConditions());
		$rank = $char_to_copy->getRank();
		if($rank->isLoaded()) {
			$player->setRank($char_to_copy->getRank());
		}

		if($db->hasColumn('players', 'lookaddons'))
			$player->setLookAddons($char_to_copy->getLookAddons());

		$player->setTownId($newchar_town);
		$player->setExperience($char_to_copy->getExperience());
		$player->setLevel($char_to_copy->getLevel());
		$player->setMagLevel($char_to_copy->getMagLevel());
		$player->setHealth($char_to_copy->getHealth());
		$player->setHealthMax($char_to_copy->getHealthMax());
		$player->setMana($char_to_copy->getMana());
		$player->setManaMax($char_to_copy->getManaMax());
		$player->setManaSpent($char_to_copy->getManaSpent());
		$player->setSoul($char_to_copy->getSoul());

		for($skill = POT::SKILL_FIRST; $skill <= POT::SKILL_LAST; $skill++)
			$player->setSkill($skill, 10);

		$player->setLookBody($char_to_copy->getLookBody());
		$player->setLookFeet($char_to_copy->getLookFeet());
		$player->setLookHead($char_to_copy->getLookHead());
		$player->setLookLegs($char_to_copy->getLookLegs());
		$player->setLookType($char_to_copy->getLookType());
		$player->setCap($char_to_copy->getCap());
		$player->setBalance(0);
		$player->setPosX(0);
		$player->setPosY(0);
		$player->setPosZ(0);

		if($db->hasColumn('players', 'stamina')) {
			$player->setStamina($char_to_copy->getStamina());
		}

		if($db->hasColumn('players', 'loss_experience')) {
			$player->setLossExperience($char_to_copy->getLossExperience());
			$player->setLossMana($char_to_copy->getLossMana());
			$player->setLossSkills($char_to_copy->getLossSkills());
		}
		if($db->hasColumn('players', 'loss_items')) {
			$player->setLossItems($char_to_copy->getLossItems());
			$player->setLossContainers($char_to_copy->getLossContainers());
		}

		$player->save();
		$player->setCustomField("created", time());

		$newchar_created = true;
		$account_logged->logAction('Created character <b>' . $player->getName() . '</b>.');
		unset($player);

		$player = new OTS_Player();
		$player->find($newchar_name);

		if($player->isLoaded()) {
			if($db->hasTable('player_skills')) {
				for($i=0; $i<7; $i++) {
					$skillExists = $db->query('SELECT `skillid` FROM `player_skills` WHERE `player_id` = ' . $player->getId() . ' AND `skillid` = ' . $i);
					if($skillExists->rowCount() <= 0) {
						$db->query('INSERT INTO `player_skills` (`player_id`, `skillid`, `value`, `count`) VALUES ('.$player->getId().', '.$i.', 10, 0)');
					}
				}
			}

			$loaded_items_to_copy = $db->query("SELECT * FROM player_items WHERE player_id = ".$char_to_copy->getId()."");
			foreach($loaded_items_to_copy as $save_item)
				$db->query("INSERT INTO `player_items` (`player_id` ,`pid` ,`sid` ,`itemtype`, `count`, `attributes`) VALUES ('".$player->getId()."', '".$save_item['pid']."', '".$save_item['sid']."', '".$save_item['itemtype']."', '".$save_item['count']."', '".$save_item['attributes']."');");

			$twig->display('success.html.twig', array(
				'title' => 'Character Created',
				'description' => 'The character <b>' . $newchar_name . '</b> has been created.<br/>
							Please select the outfit when you log in for the first time.<br/><br/>
							<b>See you on ' . $config['lua']['serverName'] . '!</b>'
			));
		}
		else
		{
			error("Error. Can't create character. Probably problem with database. Please try again later or contact with admin.");
			return;
		}
	}
}

if(count($errors) > 0) {
	$twig->display('error_box.html.twig', array('errors' => $errors));
}

if(!$newchar_created) {
	$twig->display('account.create_character.html.twig', array(
		'name' => $newchar_name,
		'sex' => $newchar_sex,
		'vocation' => $newchar_vocation,
		'town' => $newchar_town,
		'save' => $save,
		'errors' => $errors
	));
}
?>