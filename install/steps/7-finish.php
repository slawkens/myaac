<?php
defined('MYAAC') or die('Direct access not allowed!');

ini_set('max_execution_time', 300);
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
			
			$player_used = &$player;
		}
		else {
			$player_used = &$player_db;
		}

		$player_used->setGroupId($groups->getHighestId());

		if($account_db->isLoaded()) {
			$account_db->setPassword(encrypt($password));
			$account_db->setEMail($_SESSION['var_mail_admin']);
			$account_db->save();
			
			$account_used = &$account_db;
		}
		else {
			$new_account = new OTS_Account();
			if(USE_ACCOUNT_NAME) {
				$new_account->create($account);
			}
			else {
				$new_account->create(null, $account_id);
			}
			
			$new_account->setPassword(encrypt($password));
			$new_account->setEMail($_SESSION['var_mail_admin']);
			
			$new_account->unblock();
			$new_account->save();
			
			$new_account->setCustomField('created', time());
			$new_account->logAction('Account created.');
			
			$account_used = &$new_account;
		}

		if($config_salt_enabled)
			$account_used->setCustomField('salt', $salt);

		$account_used->setCustomField('web_flags', FLAG_ADMIN + FLAG_SUPER_ADMIN);
		$account_used->setCustomField('country', 'us');
		if(fieldExist('group_id', 'accounts'))
			$account_used->setCustomField('group_id', $groups->getHighestId());
		if(fieldExist('type', 'accounts'))
			$account_used->setCustomField('type', 5);

		if(!$player_db->isLoaded())
			$player->setAccountId($account_used->getId());
		else
			$player_db->setAccountId($account_used->getId());

		success($locale['step_database_created_account']);

		setSession('account', $account_used->getId());
		setSession('password', encrypt($password));
		setSession('remember_me', true);

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

		$query = $db->query("SELECT `id` FROM `" . TABLE_PREFIX ."news` WHERE `title` LIKE 'Hello!';");
		if($query->rowCount() == 0) {
			if(query("INSERT INTO `" . TABLE_PREFIX ."news` (`id`, `type`, `date`, `category`, `title`, `body`, `player_id`, `comments`, `hidden`) VALUES (NULL, '1', UNIX_TIMESTAMP(), '2', 'Hello!', 'MyAAC is just READY to use!', " . $player_id . ", 'http://my-aac.org', '0');
	INSERT INTO `myaac_news` (`id`, `type`, `date`, `category`, `title`, `body`, `player_id`, `comments`, `hidden`) VALUES (NULL, '2', UNIX_TIMESTAMP(), '4', 'Hello tickets!', 'http://my-aac.org', " . $player_id . ", '', '0');")) {
				success($locale['step_database_created_news']);
			}
		}

		$deleted = 'deleted';
		if(fieldExist('deletion', 'players'))
			$deleted = 'deletion';

		$insert_into_players = "INSERT INTO `players` (`id`, `name`, `group_id`, `account_id`, `level`, `vocation`, `health`, `healthmax`, `experience`, `lookbody`, `lookfeet`, `lookhead`, `looklegs`, `looktype`, `maglevel`, `mana`, `manamax`, `manaspent`, `soul`, `town_id`, `posx`, `posy`, `posz`, `conditions`, `cap`, `sex`, `lastlogin`, `lastip`, `save`, `lastlogout`, `balance`, `$deleted`, `created`, `hidden`, `comment`) VALUES ";
		$success = true;

		$highscores_ignored_ids = array();
		$query = $db->query('SELECT `id` FROM `players` WHERE `name` = ' . $db->quote('Rook Sample'));
		if($query->rowCount() == 0) {
			if(!query($insert_into_players . "(null, 'Rook Sample', 1, " . getSession('account') . ", 1, 0, 150, 150, 4200, 118, 114, 38, 57, 130, 0, 0, 0, 0, 100, 1, 1000, 1000, 7, '', 400, 1, 1255179613, 2453925456, 1, 1255179614, 0, 0, UNIX_TIMESTAMP(), 1, '');"))
				$success = false;
			else {
				$highscores_ignored_ids[] = $db->lastInsertId();
			}
		}

		$query = $db->query('SELECT `id` FROM `players` WHERE `name` = ' . $db->quote('Sorcerer Sample'));
		if($query->rowCount() == 0) {
			if(!query($insert_into_players . "(null, 'Sorcerer Sample', 1, " . getSession('account') . ", 8, 1, 185, 185, 4200, 118, 114, 38, 57, 130, 0, 35, 35, 0, 100, 1, 1000, 1000, 7, '', 470, 1, 1255179571, 2453925456, 1, 1255179612, 0, 0, UNIX_TIMESTAMP(), 1, '');"))
				$success = false;
			else {
				$highscores_ignored_ids[] = $db->lastInsertId();
			}
		}

		$query = $db->query('SELECT `id` FROM `players` WHERE `name` = ' . $db->quote('Druid Sample'));
		if($query->rowCount() == 0) {
			if(!query($insert_into_players . "(null, 'Druid Sample', 1, " . getSession('account') . ", 8, 2, 185, 185, 4200, 118, 114, 38, 57, 130, 0, 35, 35, 0, 100, 1, 1000, 1000, 7, '', 470, 1, 1255179655, 2453925456, 1, 1255179658, 0, 0, UNIX_TIMESTAMP(), 1, '');"))
				$success = false;
			else {
				$highscores_ignored_ids[] = $db->lastInsertId();
			}
		}

		$query = $db->query('SELECT `id` FROM `players` WHERE `name` = ' . $db->quote('Paladin Sample'));
		if($query->rowCount() == 0) {
			if(!query($insert_into_players . "(null, 'Paladin Sample', 1, " . getSession('account') . ", 8, 3, 185, 185, 4200, 118, 114, 38, 57, 129, 0, 35, 35, 0, 100, 1, 1000, 1000, 7, '', 470, 1, 1255179854, 2453925456, 1, 1255179858, 0, 0, UNIX_TIMESTAMP(), 1, '');"))
				$success = false;
			else {
				$highscores_ignored_ids[] = $db->lastInsertId();
			}
		}

		$query = $db->query('SELECT `id` FROM `players` WHERE `name` = ' . $db->quote('Knight Sample'));
		if($query->rowCount() == 0) {
			if(!query($insert_into_players . "(null, 'Knight Sample', 1, " . getSession('account') . ", 8, 4, 185, 185, 4200, 118, 114, 38, 57, 131, 0, 35, 35, 0, 100, 1, 1000, 1000, 7, '', 470, 1, 1255179620, 2453925456, 1, 1255179654, 0, 0, UNIX_TIMESTAMP(), 1, '');"))
				$success = false;
			else {
				$highscores_ignored_ids[] = $db->lastInsertId();
			}
		}

		if($success) {
			success($locale['step_database_imported_players']);
		}
		
		require(LIBS . 'creatures.php');
		if(Creatures::loadFromXML()) {
			success($locale['step_database_loaded_monsters']);
			
			if(Creatures::getMonstersList()->hasErrors()) {
				$locale['step_database_error_monsters'] = str_replace('$LOG$', 'system/logs/error.log', $locale['step_database_error_monsters']);
				warning($locale['step_database_error_monsters']);
			}
		}
		else {
			error(Creatures::getLastError());
		}
		
		require(LIBS . 'spells.php');
		if(Spells::loadFromXML()) {
			success($locale['step_database_loaded_spells']);
		}
		else {
			error(Spells::getLastError());
		}

		$content = PHP_EOL;
		$content .= '$config[\'highscores_ids_hidden\'] = array(' . implode(', ', $highscores_ignored_ids) . ');';
		$content .= PHP_EOL;

		$file = fopen(BASE . 'config.local.php', 'a+');
		if($file) {
			fwrite($file, $content);
		}
		else {
			$locale['step_database_error_file'] = str_replace('$FILE$', '<b>' . BASE . 'config.local.php</b>', $locale['step_database_error_file']);
			warning($locale['step_database_error_file'] . '<br/>
				<textarea cols="70" rows="10">' . $content . '</textarea>');
		}

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