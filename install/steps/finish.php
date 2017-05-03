<?php
defined('MYAAC') or die('Direct access not allowed!');

if(isset($config['installed']) && $config['installed'] && !isset($_SESSION['saved'])) {
	echo '<p class="warning">' . $locale['already_installed'] . '</p>';
}
else {
	require(SYSTEM . 'init.php');
	//require(BASE . 'install/includes/config.php');
	if(!$error) {
		//require(BASE . 'install/includes/database.php');

		if(USE_ACCOUNT_NAME)
			$account = isset($_SESSION['var_account']) ? $_SESSION['var_account'] : NULL;
		else
			$account_id = isset($_SESSION['var_account_id']) ? $_SESSION['var_account_id'] : NULL;

		$password = $_SESSION['var_password'];
		
		$config_salt_enabled = fieldExist('salt', 'accounts');
		if($config_salt_enabled)
		{
			$salt = generateRandomString(10, false, true, true);
			$password = $salt . $password;
		}

		$account_db = new OTS_Account();
		$account_db->load(1);
		if($account_db->isLoaded()) {
			if(USE_ACCOUNT_NAME)
				$account_db->setName('dummy_account');

			$account_db->setPassword('for sample characters. ' . generateRandomString(10));
			$account_db->save();
		}
		else {
			$new_account = new OTS_Account();
			if(USE_ACCOUNT_NAME)
				$new_account->create('dummy_account', 1);
			else
				$new_account->create(NULL, 1);

			$new_account->setPassword('for sample characters. ' . generateRandomString(10));
			$new_account->save();
		}
			
		$account_db = new OTS_Account();
		if(isset($account))
			$account_db->find($account);
		else
			$account_db->load($account_id);

		$player_db = $ots->createObject('Player');
		$player_db->find('Admin');
		$groups = new OTS_Groups_List();
		if(!$player_db->isLoaded())
		{
			$player = $ots->createObject('Player');
			$player->setName('Admin');
			
			$player->setGroupId($groups->getHighestId());
		}

		if($account_db->isLoaded()) {
			if($config_salt_enabled)
				$account_db->setSalt($salt);
			
			$account_db->setPassword(encrypt($password));
			$account_db->setEMail($_SESSION['var_mail_admin']);
			$account_db->save();
			$account_db->setCustomField('web_flags', 3);
			$account_db->setCustomField('country', 'us');
			if(fieldExist('group_id', 'accounts'))
				$account_db->setCustomField('group_id', $groups->getHighestId());

			if(!$player_db->isLoaded())
				$player->setAccountId($account_db->getId());
			else
				$player_db->setAccountId($account_db->getId());
			
			$_SESSION['account'] = $account_db->getId();
		}
		else {
			$new_account = $ots->createObject('Account');
			$new_account->create($account);
			
			if($config_salt_enabled)
				$new_account->setSalt($salt);
			
			$new_account->setPassword(encrypt($password));
			$new_account->setEMail($_SESSION['var_mail_admin']);
			$new_account->unblock();
			$new_account->save();
			$new_account->setCustomField('created', time());
			$new_account->setCustomField('web_flags', 3);
			$new_account->setCustomField('country', 'us');
			if(fieldExist('group_id', 'accounts'))
				$new_account->setCustomField('group_id', $groups->getHighestId());
			$new_account->logAction('Account created.');
			
			if(!$player_db->isLoaded())
				$player->setAccountId($new_account->getId());
			else
				$player_db->setAccountId($new_account->getId());
			
			$_SESSION['account'] = $new_account->getId();
		}

		if($player_db->isLoaded())
			$player_db->save();
		else
			$player->save();

		success($locale['step_database_created_account']);
		$_SESSION['password'] = encrypt($password);
		$_SESSION['remember_me'] = true;

		$deleted = 'deleted';
		if(fieldExist('deletion', 'players'))
			$deleted = 'deletion';

		$query = $db->query('SELECT `id` FROM `players` WHERE `name` = ' . $db->quote('Rook Sample') . ' OR `name` = ' . $db->quote('Sorcerer Sample') . ' OR `name` = ' . $db->quote('Druid Sample') . ' OR `name` = ' . $db->quote('Paladin Sample') . ' OR `name` = ' . $db->quote('Knight Sample'));
		if($query->rowCount() == 0) {
			if(query("INSERT INTO `players` (`id`, `name`, `group_id`, `account_id`, `level`, `vocation`, `health`, `healthmax`, `experience`, `lookbody`, `lookfeet`, `lookhead`, `looklegs`, `looktype`, `maglevel`, `mana`, `manamax`, `manaspent`, `soul`, `town_id`, `posx`, `posy`, `posz`, `conditions`, `cap`, `sex`, `lastlogin`, `lastip`, `save`, `lastlogout`, `balance`, `$deleted`, `created`, `hidden`, `comment`) VALUES
	(null, 'Rook Sample', 1, 1, 8, 0, 185, 185, 4200, 118, 114, 38, 57, 130, 0, 35, 35, 0, 100, 11, 2200, 1298, 7, '', 470, 1, 1255179613, 2453925456, 1, 1255179614, 0, 1, UNIX_TIMESTAMP(), 1, ''),
	(null, 'Sorcerer Sample', 1, 1, 8, 1, 185, 185, 4200, 118, 114, 38, 57, 130, 0, 35, 35, 0, 100, 11, 2200, 1298, 7, '', 470, 1, 1255179571, 2453925456, 1, 1255179612, 0, 1, UNIX_TIMESTAMP(), 1, ''),
	(null, 'Druid Sample', 1, 1, 8, 2, 185, 185, 4200, 118, 114, 38, 57, 130, 0, 35, 35, 0, 100, 11, 2200, 1298, 7, '', 470, 1, 1255179655, 2453925456, 1, 1255179658, 0, 1, UNIX_TIMESTAMP(), 1, ''),
	(null, 'Paladin Sample', 1, 1, 8, 3, 185, 185, 4200, 118, 114, 38, 57, 129, 0, 35, 35, 0, 100, 11, 2200, 1298, 7, '', 470, 1, 1255179854, 2453925456, 1, 1255179858, 0, 1, UNIX_TIMESTAMP(), 1, ''),
	(null, 'Knight Sample', 1, 1, 8, 4, 185, 185, 4200, 118, 114, 38, 57, 131, 0, 35, 35, 0, 100, 11, 2200, 1298, 7, '', 470, 1, 1255179620, 2453925456, 1, 1255179654, 0, 1, UNIX_TIMESTAMP(), 1, '');"))
				success($locale['step_database_imported_players']);
		}
					
		$locale['step_finish_desc'] = str_replace('$ADMIN_PANEL$', generateLink(ADMIN_URL, $locale['step_finish_admin_panel'], true), $locale['step_finish_desc']);
		$locale['step_finish_desc'] = str_replace('$HOMEPAGE$', generateLink(BASE_URL, $locale['step_finish_homepage'], true), $locale['step_finish_desc']);
		$locale['step_finish_desc'] = str_replace('$LINK$', generateLink('http://my-aac.org', 'http://my-aac.org', true), $locale['step_finish_desc']);
		?>
		<p class="success"><?php echo $locale['step_finish_desc']; ?></p>
	<?php

		if(!isset($_SESSION['installed'])) {
			file_get_contents('http://my-aac.org/report_install.php?v=' . MYAAC_VERSION);
			$_SESSION['installed'] = false;
		}

		foreach($_SESSION as $key => $value) {
			if(strpos($key, 'var_') !== false)
				unset($_SESSION[$key]);
		}
		unset($_SESSION['saved']);
	}
}
?>