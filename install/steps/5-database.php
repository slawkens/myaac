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
	$content = "<?php";
	$content .= PHP_EOL;
	$content .= '// place for your configuration directives, so you can later easily update myaac';
	$content .= PHP_EOL;
	$content .= '$config[\'installed\'] = true;';
	$content .= PHP_EOL;
	$content .= '$config[\'mail_enabled\'] = true;';
	$content .= PHP_EOL;
	foreach($_SESSION as $key => $value)
	{
		if(strpos($key, 'var_') !== false)
		{
			if($key == 'var_server_path')
			{
				$value = str_replace("\\", "/", $value);
				if($value[strlen($value) - 1] != '/')
					$value .= '/';
			}

			if($key == 'var_usage') {
				$content .= '$config[\'anonymous_usage_statistics\'] = ' . ((int)$value == 1 ? 'true' : 'false') . ';';
				$content .= PHP_EOL;
			}
			else if($key != 'var_account' && $key != 'var_account_id' && $key != 'var_password' && $key != 'var_step') {
				$content .= '$config[\'' . str_replace('var_', '', $key) . '\'] = \'' . $value . '\';';
				$content .= PHP_EOL;
			}
		}
	}
				
	require(BASE . 'install/includes/config.php');
		
	if(!$error) {
		success($locale['step_database_importing']);
		require(BASE . 'install/includes/database.php');
		
		if(isset($database_error)) { // we failed connect to the database
			error($database_error);
		}
		else {
			echo $twig->render('install.installer.html.twig', array(
				'url' => 'tools/5-database.php',
				'message' => $locale['loading_spinner']
			));
			
			if(!$error) {
				if(!Validator::email($_SESSION['var_mail_admin'])) {
					error($locale['step_config_mail_admin_error']);
					$error = true;
				}
				if(!Validator::email($_SESSION['var_mail_address'])) {
					error($locale['step_config_mail_address_error']);
					$error = true;
				}
				
				$content .= '$config[\'client_download\'] = \'http://tibia-clients.com/clients/download/\'. $config[\'client\'] . \'/exe/windows\';';
				$content .= PHP_EOL;
				$content .= '$config[\'client_download_linux\'] = \'http://tibia-clients.com/clients/download/\'. $config[\'client\'] . \'/tar/linux\';';
				$content .= PHP_EOL;
				$content .= '$config[\'session_prefix\'] = \'myaac_' . generateRandomString(8, true, false, true, false) . '_\';';
				$content .= PHP_EOL;
				$content .= '$config[\'cache_prefix\'] = \'myaac_' . generateRandomString(8, true, false, true, false) . '_\';';

				$saved = true;
				if(!$error) {
					$saved = file_put_contents(BASE . 'config.local.php', $content);
				}
				
				if($saved) {
					if(!$error) {
						$_SESSION['saved'] = true;
					}
				}
				else {
					$_SESSION['config_content'] = $content;
					unset($_SESSION['saved']);
					
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
	<input type="hidden" name="step" id="step" value="admin" />
	<?php echo next_buttons(true, $error ? false : true);
	?>
</form>