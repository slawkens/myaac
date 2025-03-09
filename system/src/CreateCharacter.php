<?php

namespace MyAAC;

use MyAAC\Models\Player;

/**
 * CreateCharacter
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2019 MyAAC
 * @link      https://my-aac.org
 */

class CreateCharacter
{
	/**
	 * @param $name
	 * @param $errors
	 * @return bool
	 */
	public function checkName($name, &$errors)
	{
		if (!\Validator::characterName($name)) {
			$errors['name'] = \Validator::getLastError();
			return false;
		}

		if(!admin() && !\Validator::newCharacterName($name)) {
			$errors['name'] = \Validator::getLastError();
			return false;
		}

		if(Player::where('name', '=', $name)->exists()) {
			$errors['name'] = 'Character with this name already exist.';
			return false;
		}

		return empty($errors);
	}

	/**
	 * @param string $name
	 * @param int $sex
	 * @param int $vocation
	 * @param int $town
	 * @param array $errors
	 * @return bool
	 */
	public function check($name, $sex, ?int &$vocation, ?int &$town, &$errors)
	{
		$this->checkName($name, $errors);

		if(empty($sex) && $sex != "0") {
			$errors['sex'] = 'Please select the sex for your character!';
		}

		if(count(config('character_samples')) > 1)
		{
			if(!isset($vocation))
				$errors['vocation'] = 'Please select a vocation for your character.';
		}
		else {
			$vocation = config('character_samples')[0];
		}

		if(count(config('character_towns')) > 1) {
			if(!isset($town)) {
				$errors['town'] = 'Please select a town for your character.';
			}
		}
		else {
			$town = config('character_towns')[0];
		}

		if(empty($errors)) {
			if(!isset(config('genders')[$sex]))
				$errors['sex'] = 'Sex is invalid.';
			if(!in_array($town, config('character_towns'), false))
				$errors['town'] = 'Please select valid town.';
			if(count(config('character_samples')) > 1)
			{
				$newchar_vocation_check = false;
				foreach((array)config('character_samples') as $char_vocation_key => $sample_char)
					if($vocation === $char_vocation_key)
						$newchar_vocation_check = true;
				if(!$newchar_vocation_check)
					$errors['vocation'] = 'Unknown vocation. Please fill in form again.';
			}
			else
				$vocation = 0;
		}

		return empty($errors);
	}

	/**
	 * @param string $name
	 * @param int $sex
	 * @param int $vocation
	 * @param int $town
	 * @param \OTS_Account $account
	 * @param array $errors
	 * @return bool
	 * @throws \E_OTS_NotLoaded
	 */
	public function doCreate($name, $sex, $vocation, $town, $account, &$errors)
	{
		if(!$this->check($name, $sex, $vocation, $town, $errors)) {
			return false;
		}

		if(empty($errors))
		{
			$number_of_players_on_account = $account->getPlayersList(true)->count();
			if($number_of_players_on_account >= setting('core.characters_per_account'))
				$errors[] = 'You have too many characters on your account <b>('.$number_of_players_on_account . '/' . setting('core.characters_per_account') . ')</b>!';
		}

		if(empty($errors))
		{
			$char_to_copy_name = config('character_samples')[$vocation];
			$playerSample = new \OTS_Player();
			$playerSample->find($char_to_copy_name);
			if(!$playerSample->isLoaded())
				$errors[] = 'Wrong characters configuration. Try again or contact with admin. ADMIN: Go to Admin Panel -> Settings -> Create Character and set valid characters to copy names. Character to copy: <b>'.$char_to_copy_name.'</b> doesn\'t exist.';
		}

		if(!empty($errors)) {
			return false;
		}

		global $db;

		if($sex == "0")
			$playerSample->setLookType(136);

		$player = new \OTS_Player();
		$player->setName($name);
		$player->setAccount($account);
		$player->setGroupId(1);
		$player->setSex($sex);
		$player->setVocation($playerSample->getVocation());
		if($db->hasColumn('players', 'promotion'))
			$player->setPromotion($playerSample->getPromotion());

		if($db->hasColumn('players', 'direction'))
			$player->setDirection($playerSample->getDirection());

		$player->setConditions($playerSample->getConditions());
		$rank = $playerSample->getRank();
		if($rank->isLoaded()) {
			$player->setRank($playerSample->getRank());
		}

		if($db->hasColumn('players', 'lookaddons'))
			$player->setLookAddons($playerSample->getLookAddons());

		$player->setTownId($town);
		$player->setExperience($playerSample->getExperience());
		$player->setLevel($playerSample->getLevel());
		$player->setMagLevel($playerSample->getMagLevel());
		$player->setHealth($playerSample->getHealth());
		$player->setHealthMax($playerSample->getHealthMax());
		$player->setMana($playerSample->getMana());
		$player->setManaMax($playerSample->getManaMax());
		$player->setManaSpent($playerSample->getManaSpent());
		$player->setSoul($playerSample->getSoul());

		for($skill = \POT::SKILL_FIRST; $skill <= \POT::SKILL_LAST; $skill++) {
			$value = 10;
			if (setting('core.use_character_sample_skills')) {
				$value = $playerSample->getSkill($skill);
			}

			$player->setSkill($skill, $value);
		}

		$player->setLookBody($playerSample->getLookBody());
		$player->setLookFeet($playerSample->getLookFeet());
		$player->setLookHead($playerSample->getLookHead());
		$player->setLookLegs($playerSample->getLookLegs());
		$player->setLookType($playerSample->getLookType());
		$player->setCap($playerSample->getCap());
		$player->setBalance(0);
		$player->setPosX(0);
		$player->setPosY(0);
		$player->setPosZ(0);

		if($db->hasColumn('players', 'stamina')) {
			$player->setStamina($playerSample->getStamina());
		}

		if($db->hasColumn('players', 'loss_experience')) {
			$player->setLossExperience($playerSample->getLossExperience());
			$player->setLossMana($playerSample->getLossMana());
			$player->setLossSkills($playerSample->getLossSkills());
		}
		if($db->hasColumn('players', 'loss_items')) {
			$player->setLossItems($playerSample->getLossItems());
			$player->setLossContainers($playerSample->getLossContainers());
		}

		$player->save();
		$player->setCustomField('created', time());

		$player = new \OTS_Player();
		$player->find($name);

		if(!$player->isLoaded()) {
			error("Error. Can't create character. Probably problem with database. Please try again later or contact with admin.");
			return false;
		}

		if($db->hasTable('player_skills')) {
			for($skill = \POT::SKILL_FIRST; $skill <= \POT::SKILL_LAST; $skill++) {
				$value = 10;
				if (setting('core.use_character_sample_skills')) {
					$value = $playerSample->getSkill($skill);
				}
				$skillExists = $db->query('SELECT `skillid` FROM `player_skills` WHERE `player_id` = ' . $player->getId() . ' AND `skillid` = ' . $skill);
				if($skillExists->rowCount() <= 0) {
					$db->query('INSERT INTO `player_skills` (`player_id`, `skillid`, `value`, `count`) VALUES ('.$player->getId().', '.$skill.', ' . $value . ', 0)');
				}
			}
		}

		if ($db->hasTable('player_items') && $db->hasColumn('player_items', 'pid') && $db->hasColumn('player_items', 'sid') && $db->hasColumn('player_items', 'itemtype')) {
			$loaded_items_to_copy = $db->query("SELECT * FROM player_items WHERE player_id = ".$playerSample->getId()."");
			foreach($loaded_items_to_copy as $save_item) {
				$blob = $db->quote($save_item['attributes']);
				$db->query("INSERT INTO `player_items` (`player_id` ,`pid` ,`sid` ,`itemtype`, `count`, `attributes`) VALUES ('".$player->getId()."', '".$save_item['pid']."', '".$save_item['sid']."', '".$save_item['itemtype']."', '".$save_item['count']."', $blob);");
			}
		}

		global $hooks;
		if (!$hooks->trigger(HOOK_ACCOUNT_CREATE_CHARACTER_AFTER,
			[
				'account' => $account,
				'player' => $player,
				'playerSample' => $playerSample,
				'name' => $name,
				'sex' => $sex,
				'vocation' => $vocation,
				'town' => $town,
			]
		)) {
			return false;
		}

		global $twig;
		$twig->display('success.html.twig', array(
			'title' => 'Character Created',
			'description' => 'The character <b>' . $name . '</b> has been created.<br/>
					Please select the outfit when you log in for the first time.<br/><br/>
					<b>See you on ' . configLua('serverName') . '!</b>'
		));

		$account->logAction('Created character <b>' . $name . '</b>.');
		return true;
	}
}
