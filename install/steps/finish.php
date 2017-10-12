<?php
defined('MYAAC') or die('Direct access not allowed!');

if(isset($config['installed']) && $config['installed'] && !isset($_SESSION['saved'])) {
	warning($locale['already_installed']);
}
else {
	require(SYSTEM . 'init.php');
	if(!$error) {
		if(USE_ACCOUNT_NAME)
			$account = isset($_SESSION['var_account']) ? $_SESSION['var_account'] : null;
		else
			$account_id = isset($_SESSION['var_account_id']) ? $_SESSION['var_account_id'] : null;

		$password = $_SESSION['var_password'];
		
		$config_salt_enabled = fieldExist('salt', 'accounts');
		if($config_salt_enabled)
		{
			$salt = generateRandomString(10, false, true, true);
			$password = $salt . $password;
		}
/*
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
				$new_account->create(null, 1);

			$new_account->setPassword('for sample characters. ' . generateRandomString(10));
			$new_account->save();
		}
*/
		$account_db = new OTS_Account();
		if(isset($account))
			$account_db->find($account);
		else
			$account_db->load($account_id);

		$player_db = new OTS_Player();
		$player_db->find('Admin');
		$groups = new OTS_Groups_List();
		if(!$player_db->isLoaded())
		{
			$player = new OTS_Player();
			$player->setName('Admin');
			
			$player->setGroupId($groups->getHighestId());
		}

		if($account_db->isLoaded()) {
			$account_db->setPassword(encrypt($password));
			$account_db->setEMail($_SESSION['var_mail_admin']);
			$account_db->save();
			
			if($config_salt_enabled)
				$account_db->setCustomField('salt', $salt);
			
			$account_db->setCustomField('web_flags', 3);
			$account_db->setCustomField('country', 'us');
			if(fieldExist('group_id', 'accounts'))
				$account_db->setCustomField('group_id', $groups->getHighestId());
			if(fieldExist('type', 'accounts'))
				$account_db->setCustomField('type', 5);

			if(!$player_db->isLoaded())
				$player->setAccountId($account_db->getId());
			else
				$player_db->setAccountId($account_db->getId());
			
			$_SESSION['account'] = $account_db->getId();
		}
		else {
			$new_account = new OTS_Account();
			$new_account->create($account);
			
			$new_account->setPassword(encrypt($password));
			$new_account->setEMail($_SESSION['var_mail_admin']);
			
			$new_account->unblock();
			$new_account->save();
			
			if($config_salt_enabled)
				$new_account->setCustomField('salt', $salt);
			
			$new_account->setCustomField('created', time());
			$new_account->setCustomField('web_flags', 3);
			$new_account->setCustomField('country', 'us');
			if(fieldExist('group_id', 'accounts'))
				$new_account->setCustomField('group_id', $groups->getHighestId());
			if(fieldExist('type', 'accounts'))
				$new_account->setCustomField('type', 5);

			$new_account->logAction('Account created.');
			
			if(!$player_db->isLoaded())
				$player->setAccountId($new_account->getId());
			else
				$player_db->setAccountId($new_account->getId());
			
			$_SESSION['account'] = $new_account->getId();
		}

		success($locale['step_database_created_account']);
		$_SESSION['password'] = encrypt($password);
		$_SESSION['remember_me'] = true;

		if($player_db->isLoaded()) {
			$player_db->save();
		}
		else {
			$player->save();
		}

		$player_id = 0;
		$query = $db->query("SELECT `id` FROM `players` WHERE `name` = " . $db->quote('Admin') . ";");
		if($query->rowCount() == 1) {
			$query = $query->fetch();
			$player_id = $query['id'];
		}

		if(query("INSERT INTO `myaac_news` (`id`, `type`, `date`, `category`, `title`, `body`, `player_id`, `comments`, `hidden`) VALUES (NULL, '1', UNIX_TIMESTAMP(), '2', 'Hello!', 'MyAAC is just READY to use!', " . $player_id . ", 'http://my-aac.org', '0');
INSERT INTO `myaac_news` (`id`, `type`, `date`, `category`, `title`, `body`, `player_id`, `comments`, `hidden`) VALUES (NULL, '2', UNIX_TIMESTAMP(), '4', 'Hello tickets!', 'http://my-aac.org', " . $player_id . ", '', '0');")) {
			success($locale['step_database_created_news']);
		}

		$deleted = 'deleted';
		if(fieldExist('deletion', 'players'))
			$deleted = 'deletion';

		$insert_into_players = "INSERT INTO `players` (`id`, `name`, `group_id`, `account_id`, `level`, `vocation`, `health`, `healthmax`, `experience`, `lookbody`, `lookfeet`, `lookhead`, `looklegs`, `looktype`, `maglevel`, `mana`, `manamax`, `manaspent`, `soul`, `town_id`, `posx`, `posy`, `posz`, `conditions`, `cap`, `sex`, `lastlogin`, `lastip`, `save`, `lastlogout`, `balance`, `$deleted`, `created`, `hidden`, `comment`) VALUES ";
		$success = true;

		$query = $db->query('SELECT `id` FROM `players` WHERE `name` = ' . $db->quote('Rook Sample'));
		if($query->rowCount() == 0) {
			if(!query($insert_into_players . "(null, 'Rook Sample', 4, " . $_SESSION['account'] . ", 1, 0, 150, 150, 4200, 118, 114, 38, 57, 130, 0, 0, 0, 0, 100, 11, 2200, 1298, 7, '', 400, 1, 1255179613, 2453925456, 1, 1255179614, 0, 0, UNIX_TIMESTAMP(), 1, '');"))
				$success = false;
		}

		$query = $db->query('SELECT `id` FROM `players` WHERE `name` = ' . $db->quote('Sorcerer Sample'));
		if($query->rowCount() == 0) {
			if(!query($insert_into_players . "(null, 'Sorcerer Sample', 4, " . $_SESSION['account'] . ", 8, 1, 185, 185, 4200, 118, 114, 38, 57, 130, 0, 35, 35, 0, 100, 11, 2200, 1298, 7, '', 470, 1, 1255179571, 2453925456, 1, 1255179612, 0, 0, UNIX_TIMESTAMP(), 1, '');"))
				$success = false;
		}

		$query = $db->query('SELECT `id` FROM `players` WHERE `name` = ' . $db->quote('Druid Sample'));
		if($query->rowCount() == 0) {
			if(!query($insert_into_players . "(null, 'Druid Sample', 4, " . $_SESSION['account'] . ", 8, 2, 185, 185, 4200, 118, 114, 38, 57, 130, 0, 35, 35, 0, 100, 11, 2200, 1298, 7, '', 470, 1, 1255179655, 2453925456, 1, 1255179658, 0, 0, UNIX_TIMESTAMP(), 1, '');"))
				$success = false;
		}

		$query = $db->query('SELECT `id` FROM `players` WHERE `name` = ' . $db->quote('Paladin Sample'));
		if($query->rowCount() == 0) {
			if(!query($insert_into_players . "(null, 'Paladin Sample', 4, " . $_SESSION['account'] . ", 8, 3, 185, 185, 4200, 118, 114, 38, 57, 129, 0, 35, 35, 0, 100, 11, 2200, 1298, 7, '', 470, 1, 1255179854, 2453925456, 1, 1255179858, 0, 0, UNIX_TIMESTAMP(), 1, '');"))
				$success = false;
		}

		$query = $db->query('SELECT `id` FROM `players` WHERE `name` = ' . $db->quote('Knight Sample'));
		if($query->rowCount() == 0) {
			if(!query($insert_into_players . "(null, 'Knight Sample', 4, " . $_SESSION['account'] . ", 8, 4, 185, 185, 4200, 118, 114, 38, 57, 131, 0, 35, 35, 0, 100, 11, 2200, 1298, 7, '', 470, 1, 1255179620, 2453925456, 1, 1255179654, 0, 0, UNIX_TIMESTAMP(), 1, '');"))
				$success = false;
		}

		if($success) {
			success($locale['step_database_imported_players']);
		}
		
		require LIBS . 'creatures.php';
		if(Creatures::loadFromXML())
			success($locale['step_database_loaded_creatures']);
		
		require LIBS . 'spells.php';
		if(Spells::loadFromXML())
			success($locale['step_database_loaded_spells']);

		$locale['step_finish_desc'] = str_replace('$ADMIN_PANEL$', generateLink(ADMIN_URL, $locale['step_finish_admin_panel'], true), $locale['step_finish_desc']);
		$locale['step_finish_desc'] = str_replace('$HOMEPAGE$', generateLink(BASE_URL, $locale['step_finish_homepage'], true), $locale['step_finish_desc']);
		$locale['step_finish_desc'] = str_replace('$LINK$', generateLink('http://my-aac.org', 'http://my-aac.org', true), $locale['step_finish_desc']);

		success($locale['step_finish_desc']);

		if(!isset($_SESSION['installed'])) {
			file_get_contents('http://my-aac.org/report_install.php?v=' . MYAAC_VERSION . '&b=' . urlencode(BASE_URL));
			$_SESSION['installed'] = true;
		}

		foreach($_SESSION as $key => $value) {
			if(strpos($key, 'var_') !== false)
				unset($_SESSION[$key]);
		}
		unset($_SESSION['saved']);
	}
}
?>