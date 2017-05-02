<?php
//ini_set('display_errors', false);
$error = false;

if(!isset($_SESSION['var_server_path'])) {
	error($locale['step_database_error_path']);
	$error = true;
}

if(!$error) {
	$content = "<?php\n";
	foreach($_SESSION as $key => $value)
	{
		if(strpos($key, 'var_') !== false)
		{
			if($key == 'var_server_path')
			{
				$value = str_replace("\\", "/", $value);
				if($value[strlen($value) - 1] != '/')
					$value .= "/";
			}

			if($key != 'var_account' && $key != 'var_password') {
				$content .= '$config[\'' . str_replace('var_', '', $key) . '\'] = \'' . $value . '\';';
				$content .= PHP_EOL;
			}
		}
	}
				
	$config['server_path'] = $_SESSION['var_server_path'];
	// take care of trailing slash at the end
	if($config['server_path'][strlen($config['server_path']) - 1] != '/')
		$config['server_path'] .= '/';

	if(!file_exists($config['server_path'] . 'config.lua')) {
		error($locale['step_database_error_config']);
		$error = true;
	}
	
	if(!$error) {
		$config['lua'] = load_config_lua($config['server_path'] . 'config.lua');
		if(isset($config['lua']['sqlType'])) // tfs 0.3
			$config['database_type'] = $config['lua']['sqlType'];
		else if(isset($config['lua']['mysqlHost'])) // tfs 0.2/1.0
			$config['database_type'] = 'mysql';
		else if(isset($config['lua']['database_type'])) // otserv
			$config['database_type'] = $config['lua']['database_type'];
		else if(isset($config['lua']['sql_type'])) // otserv
			$config['database_type'] = $config['lua']['sql_type'];
		
		$config['database_type'] = strtolower($config['database_type']);
		if($config['database_type'] != 'mysql') {
			$locale['step_database_error_only_mysql'] = str_replace('$DATABASE_TYPE$', '<b>' . $config['database_type'] . '</b>', $locale['step_database_error_only_mysql']);
			error($locale['step_database_error_only_mysql']);
			$error = true;
		}
		else {
			success($locale['step_database_importing']);
			require(BASE . 'install/includes/database.php');
			
			if(!tableExist('accounts')) {
				$locale['step_database_error_table'] = str_replace('$TABLE$', 'accounts', $locale['step_database_error_table']);
				error($locale['step_database_error_table']);
				$error = true;
			}

			if(tableExist(TABLE_PREFIX . 'account_actions')) {
				$locale['step_database_error_table_exist'] = str_replace('$TABLE$', TABLE_PREFIX . 'account_actions', $locale['step_database_error_table_exist']);
				warning($locale['step_database_error_table_exist']);
			}
			else if(!$error) {
				// import schema
				try {
					$db->query(file_get_contents(BASE . 'install/includes/schema.sql'));
				}
				catch(PDOException $error_) {
					error($locale['step_database_error_schema'] . ' ' . $error_);
					$error = true;
				}
				
				if(!$error) {
					$locale['step_database_success_schema'] = str_replace('$PREFIX$', TABLE_PREFIX, $locale['step_database_success_schema']);
					success($locale['step_database_success_schema']);
				}
			}
			
			if(fieldExist('key', 'accounts')) {
				if(query("ALTER TABLE `accounts` MODIFY `key` VARCHAR(64) NOT NULL DEFAULT '';"))
					success($locale['step_database_modifying_field'] . ' accounts.key...');
			}
			else {
				if(query("ALTER TABLE `accounts` ADD `key` VARCHAR(64) NOT NULL DEFAULT '' AFTER `email`;"))
					success($locale['step_database_adding_field'] . ' accounts.key...');
			}
		
			if(!fieldExist('blocked', 'accounts')) {
				if(query("ALTER TABLE `accounts` ADD `blocked` TINYINT(1) NOT NULL DEFAULT FALSE COMMENT 'internal usage' AFTER `key`;"))
					success($locale['step_database_adding_field'] . ' accounts.created...');
			}
	
			if(!fieldExist('created', 'accounts')) {
				if(query("ALTER TABLE `accounts` ADD `created` INT(11) NOT NULL DEFAULT 0 AFTER `" . (fieldExist('group_id', 'accounts') ? 'group_id' : 'blocked') . "`;"))
					success($locale['step_database_adding_field'] . ' accounts.created...');
			}
			
			if(!fieldExist('rlname', 'accounts')) {
				if(query("ALTER TABLE `accounts` ADD `rlname` VARCHAR(255) NOT NULL DEFAULT '' AFTER `created`;"))
					success($locale['step_database_adding_field'] . ' accounts.rlname...');
			}
			
			if(!fieldExist('location', 'accounts')) {
				if(query("ALTER TABLE `accounts` ADD `location` VARCHAR(255) NOT NULL DEFAULT '' AFTER `rlname`;"))
					success($locale['step_database_adding_field'] . ' accounts.location...');
			}
			
			if(!fieldExist('country', 'accounts')) {
				if(query("ALTER TABLE `accounts` ADD `country` VARCHAR(3) NOT NULL DEFAULT '' AFTER `location`;"))
					success($locale['step_database_adding_field'] . ' accounts.country...');
			}
			
			if(fieldExist('page_lastday', 'accounts')) {
				if(query("ALTER TABLE `accounts` CHANGE `page_lastday` `web_lastlogin` INT(11) NOT NULL DEFAULT 0;")) {
					$tmp = str_replace('$FIELD$', 'accounts.page_lastday', $locale['step_database_changing_field']);
					$tmp = str_replace('$FIELD_NEW$', 'accounts.web_lastlogin', $tmp);
					success($tmp);
				}
			}
			else if(!fieldExist('web_lastlogin', 'accounts')) {
				if(query("ALTER TABLE `accounts` ADD `web_lastlogin` INT(11) NOT NULL DEFAULT 0 AFTER `country`;"))
					success($locale['step_database_adding_field'] . ' accounts.created...');
			}
			
			if(!fieldExist('web_flags', 'accounts')) {
				if(query("ALTER TABLE `accounts` ADD `web_flags` INT(11) NOT NULL DEFAULT 0 AFTER `web_lastlogin`;"))
					success($locale['step_database_adding_field'] . ' accounts.country...');
			}
			
			if(!fieldExist('email_hash', 'accounts')) {
				if(query("ALTER TABLE `accounts` ADD `email_hash` VARCHAR(32) NOT NULL DEFAULT '' AFTER `web_flags`;"))
					success($locale['step_database_adding_field'] . ' accounts.email_hash...');
			}
			
			if(!fieldExist('email_verified', 'accounts')) {
				if(query("ALTER TABLE `accounts` ADD `email_verified` TINYINT(1) NOT NULL DEFAULT 0 AFTER `email_hash`;"))
					success($locale['step_database_adding_field'] . ' accounts.email_verified...');
			}
		
			if(!fieldExist('email_new', 'accounts')) {
				if(query("ALTER TABLE `accounts` ADD `email_new` VARCHAR(255) NOT NULL DEFAULT '' AFTER `email_hash`;"))
					success($locale['step_database_adding_field'] . ' accounts.email_new...');
			}
			
			if(!fieldExist('email_new_time', 'accounts')) {
				if(query("ALTER TABLE `accounts` ADD `email_new_time` INT(11) NOT NULL DEFAULT 0 AFTER `email_new`;"))
					success($locale['step_database_adding_field'] . ' accounts.email_new_time...');
			}
			
			if(!fieldExist('email_code', 'accounts')) {
				if(query("ALTER TABLE `accounts` ADD `email_code` VARCHAR(255) NOT NULL DEFAULT '' AFTER `email_new_time`;"))
					success($locale['step_database_adding_field'] . ' accounts.email_code...');
			}
			
			if(fieldExist('next_email', 'accounts')) {
				if(query("ALTER TABLE `accounts` CHANGE `next_email` `email_next` INT(11) NOT NULL DEFAULT 0;")) {
					$tmp = str_replace('$FIELD$', 'accounts.next_email', $locale['step_database_changing_field']);
					$tmp = str_replace('$FIELD_NEW$', 'accounts.email_next', $tmp);
					success($tmp);
				}
			}
			else if(!fieldExist('email_next', 'accounts')) {
				if(query("ALTER TABLE `accounts` ADD `email_next` INT(11) NOT NULL DEFAULT 0 AFTER `email_code`;"))
					success($locale['step_database_adding_field'] . ' accounts.email_next...');
			}
			
			if(!fieldExist('premium_points', 'accounts')) {
				if(query("ALTER TABLE `accounts` ADD `premium_points` INT(11) NOT NULL DEFAULT 0 AFTER `email_next`;"))
					success($locale['step_database_adding_field'] . ' accounts.premium_points...');
			}
			
			if(!fieldExist('description', 'guilds')) {
				if(query("ALTER TABLE `guilds` ADD `description` TEXT NOT NULL DEFAULT '';"))
					success($locale['step_database_adding_field'] . ' guilds.description...');
			}
			
			if(fieldExist('logo_gfx_name', 'guilds')) {
				if(query("ALTER TABLE `guilds` CHANGE `logo_gfx_name` `logo_name` VARCHAR( 255 ) NOT NULL DEFAULT 'default.gif';")) {
					$tmp = str_replace('$FIELD$', 'guilds.logo_gfx_name', $locale['step_database_changing_field']);
					$tmp = str_replace('$FIELD_NEW$', 'guilds.logo_name', $tmp);
					success($tmp);
				}
			}
			else if(!fieldExist('logo_name', 'guilds')) {
				if(query("ALTER TABLE `guilds` ADD `logo_name` VARCHAR( 255 ) NOT NULL DEFAULT 'default.gif';"))
					success($locale['step_database_adding_field'] . ' guilds.logo_name...');
			}
			
			if(!fieldExist('created', 'players')) {
				if(query("ALTER TABLE `players` ADD `created` INT(11) NOT NULL DEFAULT 0;"))
					success($locale['step_database_adding_field'] . ' players.created...');
			}
		
			if(fieldExist('hide_char', 'players')) {
				if(query("ALTER TABLE `players` CHANGE `hide_char` `hidden` TINYINT(1) NOT NULL DEFAULT 0;")) {
					$tmp = str_replace('$FIELD$', 'players.hide_char', $locale['step_database_changing_field']);
					$tmp = str_replace('$FIELD_NEW$', 'players.hidden', $tmp);
					success($tmp);
				}
			}
			else if(!fieldExist('hidden', 'players')) {
				if(query("ALTER TABLE `players` ADD `hidden` VARCHAR( 255 ) TINYINT(1) NOT NULL DEFAULT 0;"))
					success($locale['step_database_adding_field'] . ' players.hidden...');
			}
			
			if(!fieldExist('comment', 'players')) {
				if(query("ALTER TABLE `players` ADD `comment` TEXT NOT NULL DEFAULT '';"))
					success($locale['step_database_adding_field'] . ' players.comment...');
			}
			
			$account = $_SESSION['var_account'];
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
				$account_db->setName('dummy_account');
				$account_db->setPassword('for sample characters. ' . generateRandomString(10));
				$account_db->save();
			}
			else {
				$new_account = new OTS_Account();
				$new_account->create('dummy_account', 1);
				$account_db->setPassword('for sample characters. ' . generateRandomString(10));
			}
				

			$account_db = new OTS_Account();
			$account_db->find($account);
			if($account_db->isLoaded()) {
				if($config_salt_enabled)
					$account_db->setSalt($salt);
				
				$account_db->setPassword(encrypt($password));
				$account_db->setEMail($_SESSION['var_mail_admin']);
				$account_db->save();
				$account_db->setCustomField('web_flags', 3);
				$account_db->setCustomField('country', 'us');
				
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
				$new_account->logAction('Account created.');
				
				$_SESSION['account'] = $new_account->getId();
			}
		
			success($locale['step_database_created_account']);
			$_SESSION['password'] = encrypt($password);
			$_SESSION['remember_me'] = true;
		
			$deleted = 'deleted';
			if(fieldExist('deletion', 'players'))
				$deleted = 'deletion';

			$query = $db->query('SELECT `id` FROM `players` WHERE `name` = ' . $db->quote('Rook Sample') . ' OR `name` = ' . $db->quote('Sorcerer Sample') . ' OR `name` = ' . $db->quote('Druid Sample') . ' OR `name` = ' . $db->quote('Paladin Sample') . ' OR `name` = ' . $db->quote('Knight Sample'));
			if($query->rowCount() == 0) {
				if(query("INSERT INTO `players` (`id`, `name`, `group_id`, `account_id`, `level`, `vocation`, `health`, `healthmax`, `experience`, `lookbody`, `lookfeet`, `lookhead`, `looklegs`, `looktype`, `lookaddons`, `maglevel`, `mana`, `manamax`, `manaspent`, `soul`, `town_id`, `posx`, `posy`, `posz`, `conditions`, `cap`, `sex`, `lastlogin`, `lastip`, `save`, `skull`, `skulltime`, `lastlogout`, `blessings`, `balance`, `stamina`, `$deleted`, `created`, `hidden`, `comment`) VALUES
	(null, 'Rook Sample', 1, 1, 8, 0, 185, 185, 4200, 118, 114, 38, 57, 130, 0, 0, 35, 35, 0, 100, 11, 2200, 1298, 7, '', 470, 1, 1255179613, 2453925456, 1, 0, 0, 1255179614, 0, 0, 151200000, 1, UNIX_TIMESTAMP(), 1, ''),
	(null, 'Sorcerer Sample', 1, 1, 8, 1, 185, 185, 4200, 118, 114, 38, 57, 130, 0, 0, 35, 35, 0, 100, 11, 2200, 1298, 7, '', 470, 1, 1255179571, 2453925456, 1, 0, 0, 1255179612, 0, 0, 151200000, 1, UNIX_TIMESTAMP(), 1, ''),
	(null, 'Druid Sample', 1, 1, 8, 2, 185, 185, 4200, 118, 114, 38, 57, 130, 0, 0, 35, 35, 0, 100, 11, 2200, 1298, 7, '', 470, 1, 1255179655, 2453925456, 1, 0, 0, 1255179658, 0, 0, 151200000, 1, UNIX_TIMESTAMP(), 1, ''),
	(null, 'Paladin Sample', 1, 1, 8, 3, 185, 185, 4200, 118, 114, 38, 57, 129, 0, 0, 35, 35, 0, 100, 11, 2200, 1298, 7, '', 470, 1, 1255179854, 2453925456, 1, 0, 0, 1255179858, 0, 0, 151200000, 1, UNIX_TIMESTAMP(), 1, ''),
	(null, 'Knight Sample', 1, 1, 8, 4, 185, 185, 4200, 118, 114, 38, 57, 131, 0, 0, 35, 35, 0, 100, 11, 2200, 1298, 7, '', 470, 1, 1255179620, 2453925456, 1, 0, 0, 1255179654, 0, 0, 151200000, 1, UNIX_TIMESTAMP(), 1, '');"))
					success($locale['step_database_imported_players']);
			}
			
			if(!$error && !isset($_SESSION['saved'])) {
				$content .= '$config[\'installed\'] = true;';
				$content .= PHP_EOL;
				if(strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false) {
					$content .= '$config[\'friendly_urls\'] = true;';
					$content .= PHP_EOL;
				}

				$content .= '$config[\'mail_enabled\'] = true;';
				$content .= PHP_EOL;
				if(!check_mail($_SESSION['var_mail_admin'])) {
					error($locale['step_config_mail_admin_error']);
					$error = true;
				}
				if(!check_mail($_SESSION['var_mail_address'])) {
					error($locale['step_config_mail_address_error']);
					$error = true;
				}
				
				$content .= '$config[\'client_download\'] = \'http://clients.halfaway.net/windows.php?tibia=\'. $config[\'client\'];';
				$content .= PHP_EOL;
				$content .= '$config[\'client_download_linux\'] = \'http://clients.halfaway.net/linux.php?tibia=\'. $config[\'client\'];';
				$content .= PHP_EOL;
				
				$content .= "?>";
				$file = fopen(BASE . 'config.local.php', 'a+');
				if($file) {
					if(!$error) {
						fwrite($file, $content);
						$_SESSION['saved'] = true;
					}
				}
				else {
					$locale['step_database_error_file'] = str_replace('$FILE$', '<b>' . BASE . 'config.local.php</b>', $locale['step_database_error_file']);
					warning($locale['step_database_error_file'] . '<br/>
						<textarea cols="70" rows="10">' . $content . '</textarea>');
				}
			}
		}
	}
}
?>

<form action="<?php echo BASE_URL; ?>install/" method="post">
	<input type="hidden" name="step" id="step" value="finish" />
	<?php echo next_buttons(true, $error ? false : true);
	?>
</form>