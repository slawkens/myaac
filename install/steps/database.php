<?php
defined('MYAAC') or die('Direct access not allowed!');
//ini_set('display_errors', false);
ini_set('max_execution_time', 300);
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

			if($key != 'var_account' && $key != 'var_account_id' && $key != 'var_password') {
				$content .= '$config[\'' . str_replace('var_', '', $key) . '\'] = \'' . $value . '\';';
				$content .= PHP_EOL;
			}
		}
	}
				
	require(BASE . 'install/includes/config.php');
		
	if(!$error) {
		success($locale['step_database_importing']);
		require(BASE . 'install/includes/database.php');
		
		if(!tableExist('accounts')) {
			$locale['step_database_error_table'] = str_replace('$TABLE$', 'accounts', $locale['step_database_error_table']);
			error($locale['step_database_error_table']);
			$error = true;
		}
		else if(!tableExist('players')) {
			$locale['step_database_error_table'] = str_replace('$TABLE$', 'players', $locale['step_database_error_table']);
			error($locale['step_database_error_table']);
			$error = true;
		}
		else if(!tableExist('guilds')) {
			$locale['step_database_error_table'] = str_replace('$TABLE$', 'guilds', $locale['step_database_error_table']);
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
				registerDatabaseConfig('database_version', DATABASE_VERSION);
				$locale['step_database_success_schema'] = str_replace('$PREFIX$', TABLE_PREFIX, $locale['step_database_success_schema']);
				success($locale['step_database_success_schema']);
			}
		}
		
		if(!$error) {
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
					success($locale['step_database_adding_field'] . ' accounts.web_lastlogin...');
			}
			
			if(!fieldExist('web_flags', 'accounts')) {
				if(query("ALTER TABLE `accounts` ADD `web_flags` INT(11) NOT NULL DEFAULT 0 AFTER `web_lastlogin`;"))
					success($locale['step_database_adding_field'] . ' accounts.web_flags...');
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
				if(!fieldExist('email_next', 'accounts')) {
					if(query("ALTER TABLE `accounts` CHANGE `next_email` `email_next` INT(11) NOT NULL DEFAULT 0;")) {
						$tmp = str_replace('$FIELD$', 'accounts.next_email', $locale['step_database_changing_field']);
						$tmp = str_replace('$FIELD_NEW$', 'accounts.email_next', $tmp);
						success($tmp);
					}
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
		
			if(!fieldExist('deleted', 'players') && !fieldExist('deletion', 'players')) {
				if(query("ALTER TABLE `players` ADD `deleted` TINYINT(1) NOT NULL DEFAULT 0;"))
					success($locale['step_database_adding_field'] . ' players.comment...');
			}

			if(fieldExist('hide_char', 'players')) {
				if(!fieldExist('hidden', 'players')) {
					if(query("ALTER TABLE `players` CHANGE `hide_char` `hidden` TINYINT(1) NOT NULL DEFAULT 0;")) {
						$tmp = str_replace('$FIELD$', 'players.hide_char', $locale['step_database_changing_field']);
						$tmp = str_replace('$FIELD_NEW$', 'players.hidden', $tmp);
						success($tmp);
					}
				}
			}
			else if(!fieldExist('hidden', 'players')) {
				if(query("ALTER TABLE `players` ADD `hidden` TINYINT(1) NOT NULL DEFAULT 0;"))
					success($locale['step_database_adding_field'] . ' players.hidden...');
			}
			
			if(!fieldExist('comment', 'players')) {
				if(query("ALTER TABLE `players` ADD `comment` TEXT NOT NULL DEFAULT '';"))
					success($locale['step_database_adding_field'] . ' players.comment...');
			}
		}
		
		if(!$error && (!isset($_SESSION['saved']))) {
			$content .= '$config[\'installed\'] = true;';
			$content .= PHP_EOL;
		//	if(strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false) {
		//		$content .= '$config[\'friendly_urls\'] = true;';
		//		$content .= PHP_EOL;
		//	}

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
			$content .= '// place for your configuration directives, so you can later easily update myaac';
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
?>

<form action="<?php echo BASE_URL; ?>install/" method="post">
	<input type="hidden" name="step" id="step" value="admin" />
	<?php echo next_buttons(true, $error ? false : true);
	?>
</form>